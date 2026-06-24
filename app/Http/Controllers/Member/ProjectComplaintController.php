<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Models\ProjectComplaint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectComplaintController extends Controller
{
    public function create(ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === request()->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_IN_PROGRESS, 403, 'Komplain hanya dapat diajukan jika project sedang dikerjakan.');
        abort_unless(!$projectRequest->complaint, 403, 'Anda sudah mengajukan komplain untuk project ini.');

        return Inertia::render('Member/ProjectRequests/Complaint', [
            'projectRequest' => $projectRequest,
        ]);
    }

    public function store(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === $request->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_IN_PROGRESS, 403);
        abort_unless(!$projectRequest->complaint, 403);

        $validated = $request->validate([
            'member_reason' => ['required', 'string'],
            'member_proof' => ['nullable', 'image', 'max:2048'], // Max 2MB image
        ]);

        $proofPath = null;
        if ($request->hasFile('member_proof')) {
            $proofPath = $request->file('member_proof')->store('complaints', 'public');
        }

        $projectRequest->complaint()->create([
            'member_reason' => $validated['member_reason'],
            'member_proof' => $proofPath,
            'status' => ProjectComplaint::STATUS_PENDING_VENDOR,
        ]);

        $projectRequest->update([
            'status' => ProjectRequest::STATUS_COMPLAINED,
        ]);

        return redirect()->route('member.project-requests.show', $projectRequest)->with('success', 'Komplain berhasil diajukan. Menunggu tanggapan dari vendor.');
    }
}
