<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\ProjectApplicant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectBursaController extends Controller
{
    public function index()
    {
        // Must be an approved vendor
        abort_unless(request()->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses Bursa Project.');

        $projects = ProjectRequest::with('user')
            ->where('status', ProjectRequest::STATUS_OPEN)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get IDs of projects the current vendor has already applied to
        $appliedProjectIds = ProjectApplicant::where('vendor_id', request()->user()->id)
            ->pluck('project_request_id')
            ->toArray();

        return Inertia::render('Partner/Projects/Bursa', [
            'projects' => $projects,
            'appliedProjectIds' => $appliedProjectIds,
        ]);
    }

    public function show(ProjectRequest $projectRequest)
    {
        abort_unless(request()->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses Bursa Project.');
        abort_unless($projectRequest->status === ProjectRequest::STATUS_OPEN, 404, 'Project tidak tersedia.');

        $projectRequest->load('user');
        
        $hasApplied = ProjectApplicant::where('project_request_id', $projectRequest->id)
            ->where('vendor_id', request()->user()->id)
            ->exists();

        return Inertia::render('Partner/Projects/BursaShow', [
            'project' => $projectRequest,
            'hasApplied' => $hasApplied,
        ]);
    }

    public function apply(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($request->user()->isVendor(), 403, 'Hanya vendor aktif yang dapat mengakses Bursa Project.');
        abort_unless($projectRequest->status === ProjectRequest::STATUS_OPEN, 404, 'Project tidak tersedia.');

        $exists = ProjectApplicant::where('project_request_id', $projectRequest->id)
            ->where('vendor_id', $request->user()->id)
            ->exists();

        abort_if($exists, 400, 'Anda sudah mengajukan diri untuk project ini.');

        $validated = $request->validate([
            'cover_letter' => ['required', 'string'],
        ]);

        ProjectApplicant::create([
            'project_request_id' => $projectRequest->id,
            'vendor_id' => $request->user()->id,
            'cover_letter' => $validated['cover_letter'],
            'status' => ProjectApplicant::STATUS_PENDING,
        ]);

        return redirect()->back()->with('success', 'Berhasil mengajukan diri. Menunggu persetujuan Admin.');
    }
}
