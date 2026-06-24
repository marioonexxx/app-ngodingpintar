<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\ProjectApplicant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectRequestController extends Controller
{
    public function index()
    {
        $projects = ProjectRequest::with(['user', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/ProjectRequests/Index', [
            'projects' => $projects,
        ]);
    }

    public function show(ProjectRequest $projectRequest)
    {
        $projectRequest->load(['user', 'vendor', 'transaction', 'applicants.vendor', 'progressUpdates', 'complaint']);

        return Inertia::render('Admin/ProjectRequests/Show', [
            'project' => $projectRequest,
        ]);
    }

    public function approve(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->status === ProjectRequest::STATUS_PENDING_ADMIN, 400, 'Status tidak valid untuk disetujui.');

        $projectRequest->update([
            'status' => ProjectRequest::STATUS_WAITING_PAYMENT,
        ]);

        return redirect()->back(303)->with('success', 'Project Request disetujui. Member akan diminta untuk melakukan pembayaran.');
    }

    public function reject(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->status === ProjectRequest::STATUS_PENDING_ADMIN, 400, 'Status tidak valid untuk ditolak.');

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $projectRequest->update([
            'status' => ProjectRequest::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back(303)->with('success', 'Project Request ditolak. Member dapat mengajukan ulang.');
    }

    public function assignVendor(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->status === ProjectRequest::STATUS_OPEN, 400, 'Project belum berstatus open.');
        
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:users,id'],
        ]);

        // Pastikan vendor id ada di dalam daftar applicant
        $applicant = ProjectApplicant::where('project_request_id', $projectRequest->id)
            ->where('vendor_id', $validated['vendor_id'])
            ->first();

        abort_unless($applicant, 400, 'Vendor tersebut tidak mengajukan diri untuk project ini.');

        // Update project
        $projectRequest->update([
            'vendor_id' => $validated['vendor_id'],
            'status' => ProjectRequest::STATUS_IN_PROGRESS,
        ]);

        // Update applicant status
        $applicant->update(['status' => ProjectApplicant::STATUS_ACCEPTED]);

        // Tolak applicant lainnya
        ProjectApplicant::where('project_request_id', $projectRequest->id)
            ->where('id', '!=', $applicant->id)
            ->update(['status' => ProjectApplicant::STATUS_REJECTED]);

        return redirect()->back(303)->with('success', 'Vendor berhasil dipilih. Project mulai dikerjakan.');
    }

    public function destroy(ProjectRequest $projectRequest)
    {
        abort_unless(in_array($projectRequest->status, [ProjectRequest::STATUS_PENDING_ADMIN, ProjectRequest::STATUS_REJECTED]), 400, 'Hanya request pending atau rejected yang bisa dihapus.');
        
        $projectRequest->delete();

        return redirect()->route('admin.project-requests.index', [], 303)->with('success', 'Project Request berhasil dihapus.');
    }
}
