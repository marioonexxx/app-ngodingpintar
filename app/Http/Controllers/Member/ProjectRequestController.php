<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = ProjectRequest::where('user_id', $request->user()->id)
            ->with(['vendor'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Member/ProjectRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Member/ProjectRequests/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:new_app,revision'],
            'description' => ['required', 'string'],
            'budget' => ['required', 'numeric', 'min:50000'],
            'deadline_date' => ['required', 'date', 'after:today'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('project_requests/attachments', 'public');
        }

        $request->user()->projectRequests()->create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'deadline_date' => $validated['deadline_date'],
            'whatsapp' => $validated['whatsapp'],
            'attachment' => $attachmentPath,
            'status' => ProjectRequest::STATUS_PENDING_ADMIN,
        ]);

        return redirect()->route('member.project-requests.index')->with('success', 'Request project berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function show(ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === request()->user()->id, 403);

        $projectRequest->load(['vendor', 'transaction', 'progressUpdates', 'complaint']);

        return Inertia::render('Member/ProjectRequests/Show', [
            'projectRequest' => $projectRequest,
        ]);
    }

    public function edit(ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === request()->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_REJECTED, 403, 'Hanya request yang ditolak yang dapat diedit.');

        return Inertia::render('Member/ProjectRequests/Edit', [
            'projectRequest' => $projectRequest,
        ]);
    }

    public function update(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === $request->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_REJECTED, 403, 'Hanya request yang ditolak yang dapat diedit.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:new_app,revision'],
            'description' => ['required', 'string'],
            'budget' => ['required', 'numeric', 'min:50000'],
            'deadline_date' => ['required', 'date', 'after:today'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:10240'],
        ]);

        $data = [
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'budget' => $validated['budget'],
            'deadline_date' => $validated['deadline_date'],
            'whatsapp' => $validated['whatsapp'],
            'status' => ProjectRequest::STATUS_PENDING_ADMIN, // Resubmit
            'rejection_reason' => null, // Clear rejection reason
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('project_requests/attachments', 'public');
        }

        $projectRequest->update($data);

        return redirect()->route('member.project-requests.show', $projectRequest)->with('success', 'Request project berhasil diajukan ulang.');
    }

    public function complete(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === $request->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_IN_PROGRESS, 403);

        $validated = $request->validate([
            'completion_notes' => ['nullable', 'string'],
        ]);

        $projectRequest->update([
            'status' => ProjectRequest::STATUS_COMPLETED,
            'completion_notes' => $validated['completion_notes'] ?? null,
        ]);

        // Release funds to vendor logic will be handled here or via event
        // We need to implement the Earning logic for the vendor here
        $vendor = $projectRequest->vendor;
        if ($vendor && $projectRequest->transaction) {
            $feePercentage = $vendor->fee_percentage ?? 15; // default 15%
            $platformFee = $projectRequest->budget * ($feePercentage / 100);
            $vendorAmount = $projectRequest->budget - $platformFee;

            $vendor->earnings()->create([
                'transaction_id' => $projectRequest->transaction_id,
                'amount' => $vendorAmount,
                'platform_fee' => $platformFee,
                'status' => \App\Models\Earning::STATUS_AVAILABLE,
            ]);
        }

        return redirect()->back()->with('success', 'Project berhasil diselesaikan. Dana diteruskan ke vendor.');
    }

    public function pay(ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === request()->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_WAITING_PAYMENT, 403, 'Request ini tidak sedang menunggu pembayaran.');

        return Inertia::render('Member/ProjectRequests/Pay', [
            'projectRequest' => $projectRequest,
            'bankAccounts' => \App\Models\BankAccount::where('is_active', true)->get(),
        ]);
    }

    public function processPayment(Request $request, ProjectRequest $projectRequest)
    {
        abort_unless($projectRequest->user_id === $request->user()->id, 403);
        abort_unless($projectRequest->status === ProjectRequest::STATUS_WAITING_PAYMENT, 403, 'Request ini tidak sedang menunggu pembayaran.');

        $data = $request->validate([
            'payment_gateway' => ['required', 'in:manual_transfer'],
        ]);

        $transaction = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $data, $projectRequest) {
            $transaction = \App\Models\Transaction::create([
                'user_id' => $request->user()->id,
                'cart_id' => null,
                'invoice_number' => 'PRJ-'.now()->format('Ymd').'-'.\Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(6)),
                'payment_gateway' => $data['payment_gateway'],
                'subtotal' => $projectRequest->budget,
                'total' => $projectRequest->budget,
                'status' => \App\Models\Transaction::STATUS_PENDING,
                'payment_status' => \App\Models\Transaction::PAYMENT_UNPAID,
            ]);

            $projectRequest->update([
                'transaction_id' => $transaction->id,
                // Status remains waiting_payment until the transaction is paid.
                // The Admin/TransactionController will change status to open upon payment approval.
            ]);

            return $transaction;
        });

        return redirect()->route('member.transactions.active')
            ->with('success', "Checkout berhasil. Silakan lakukan pembayaran untuk Invoice {$transaction->invoice_number}.");
    }
}
