<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectComplaint;
use App\Models\ProjectRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectComplaintController extends Controller
{
    public function resolve(Request $request, ProjectComplaint $projectComplaint)
    {
        abort_unless($projectComplaint->status === ProjectComplaint::STATUS_PENDING_ADMIN, 400, 'Komplain ini tidak sedang menunggu keputusan admin.');
        
        $projectRequest = $projectComplaint->projectRequest;

        $validated = $request->validate([
            'resolution' => ['required', 'in:refund_member,release_to_vendor'],
            'admin_notes' => ['required', 'string'],
        ]);

        DB::beginTransaction();
        try {
            if ($validated['resolution'] === 'refund_member') {
                // Member menang, refund dana
                $projectComplaint->update([
                    'status' => ProjectComplaint::STATUS_RESOLVED_REFUND,
                    'admin_notes' => $validated['admin_notes'],
                ]);

                $projectRequest->update([
                    'status' => ProjectRequest::STATUS_REFUND_PENDING,
                ]);

                // Create a refund request automatically if the transaction doesn't have one yet
                $transaction = $projectRequest->transaction;
                if ($transaction && !$transaction->refundRequests()->exists()) {
                    $transaction->refundRequests()->create([
                        'user_id' => $projectRequest->user_id,
                        'reason' => 'Refund dari resolusi komplain project: ' . $projectRequest->title,
                        'status' => 'pending',
                        'amount' => $transaction->total_amount, // or project budget if partial
                    ]);
                }
            } else {
                // Vendor menang, teruskan pembayaran
                $projectComplaint->update([
                    'status' => ProjectComplaint::STATUS_RESOLVED_RELEASED,
                    'admin_notes' => $validated['admin_notes'],
                ]);

                $projectRequest->update([
                    'status' => ProjectRequest::STATUS_COMPLETED,
                ]);

                // Hitung potongan fee platform (opsional, ikuti alur transaksi eksisting jika ada)
                $vendor = $projectRequest->vendor;
                if ($vendor) {
                    $amount = $projectRequest->budget;
                    $platformFee = $amount * 0.10; // Misalnya potongan 10%
                    $netAmount = $amount - $platformFee;

                    $vendor->increment('balance', $netAmount);

                    $vendor->earnings()->create([
                        'amount' => $netAmount,
                        'description' => 'Pendapatan dari Project: ' . $projectRequest->title,
                        'type' => 'project',
                        'reference_id' => $projectRequest->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Komplain berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses resolusi komplain.');
        }
    }
}
