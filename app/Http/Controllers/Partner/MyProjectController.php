<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\ProjectProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MyProjectController extends Controller
{
    public function index()
    {
        abort_unless(request()->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses halaman ini.');

        $projects = ProjectRequest::with('user')
            ->where('vendor_id', request()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Partner/Projects/MyProjects', [
            'projects' => $projects,
        ]);
    }

    public function show(ProjectRequest $projectRequest)
    {
        abort_unless(request()->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses halaman ini.');
        abort_unless($projectRequest->vendor_id === request()->user()->id, 403, 'Ini bukan project Anda.');

        $projectRequest->load(['user', 'progressUpdates', 'complaint']);

        return Inertia::render('Partner/Projects/MyProjectShow', [
            'project' => $projectRequest,
        ]);
    }

    public function updateProgress(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($request->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses halaman ini.');
        abort_unless($projectRequest->vendor_id === $request->user()->id, 403, 'Ini bukan project Anda.');
        abort_unless($projectRequest->status === ProjectRequest::STATUS_IN_PROGRESS, 403, 'Project tidak sedang dalam proses pengerjaan.');

        $validated = $request->validate([
            'description' => ['required', 'string'],
            'github_link' => ['nullable', 'url'],
            'attachment' => ['nullable', 'file', 'max:5120'], // Max 5MB
        ]);

        if (!empty($validated['github_link'])) {
            $projectRequest->update(['github_link' => $validated['github_link']]);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('project_progress', 'public');
        }

        $projectRequest->progressUpdates()->create([
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->back()->with('success', 'Progress berhasil diupdate.');
    }

    public function respondComplaint(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($request->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses halaman ini.');
        abort_unless($projectRequest->vendor_id === $request->user()->id, 403, 'Ini bukan project Anda.');
        abort_unless($projectRequest->status === ProjectRequest::STATUS_COMPLAINED, 403, 'Project tidak sedang dalam status komplain.');
        abort_unless($projectRequest->complaint && $projectRequest->complaint->status === \App\Models\ProjectComplaint::STATUS_PENDING_VENDOR, 403, 'Anda tidak dapat merespon komplain ini.');

        $validated = $request->validate([
            'vendor_response' => ['required', 'string'],
            'vendor_proof' => ['nullable', 'image', 'max:2048'],
        ]);

        $proofPath = null;
        if ($request->hasFile('vendor_proof')) {
            $proofPath = $request->file('vendor_proof')->store('complaints', 'public');
        }

        $projectRequest->complaint->update([
            'vendor_response' => $validated['vendor_response'],
            'vendor_proof' => $proofPath,
            'status' => \App\Models\ProjectComplaint::STATUS_PENDING_ADMIN,
        ]);

        return redirect()->back()->with('success', 'Tanggapan komplain berhasil dikirim. Menunggu keputusan Admin.');
    }
}
