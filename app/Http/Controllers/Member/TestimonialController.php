<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Transaction;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function create(Request $request, Transaction $transaction)
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        abort_unless($transaction->status === Transaction::STATUS_COMPLETED, 403, 'Testimonial tersedia setelah transaksi selesai.');

        return Frontend::render('Member/TestimonialForm', [
            'transaction' => $transaction->load('items.product'),
        ]);
    }

    public function store(Request $request, Transaction $transaction): RedirectResponse
    {
        abort_unless($transaction->user_id === $request->user()->id, 403);
        abort_unless($transaction->status === Transaction::STATUS_COMPLETED, 403);

        $data = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
        ]);

        Testimonial::create([
            ...$data,
            'user_id' => $request->user()->id,
            'transaction_id' => $transaction->id,
            'status' => Testimonial::STATUS_PENDING,
        ]);

        return redirect()->route('member.transactions.history')->with('success', 'Testimonial dikirim dan menunggu approval.');
    }
}
