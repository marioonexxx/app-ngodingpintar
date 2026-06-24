<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_page_contains_transaction_specific_update_url(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'VERIFY-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 175000,
            'total' => 175000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.payment-verifications.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/PaymentVerifications')
                ->where(
                    'transactions.data.0.verification_url',
                    '/admin/payment-verifications/'.$transaction->id
                )
            );
    }

    public function test_admin_can_approve_verifying_manual_payment(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'VERIFY-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 175000,
            'total' => 175000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        $response = $this->actingAs($admin)->patch(
            route('admin.payment-verifications.update', ['transaction' => $transaction->id]),
            ['action' => 'approve']
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $transaction->refresh();
        $this->assertSame(Transaction::PAYMENT_PAID, $transaction->payment_status);
        $this->assertSame(Transaction::STATUS_COMPLETED, $transaction->status);
        $this->assertNotNull($transaction->paid_at);
    }

    public function test_completed_payment_cannot_be_verified_again(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'VERIFY-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 175000,
            'total' => 175000,
            'status' => Transaction::STATUS_COMPLETED,
            'payment_status' => Transaction::PAYMENT_PAID,
        ]);

        $this->actingAs($admin)->patch(
            route('admin.payment-verifications.update', ['transaction' => $transaction->id]),
            ['action' => 'approve']
        )->assertUnprocessable();
    }

    public function test_collection_fallback_can_approve_payment_with_transaction_id(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'VERIFY-FALLBACK-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 175000,
            'total' => 175000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        $this->actingAs($admin)->patch(
            route('admin.payment-verifications.update-collection'),
            [
                'transaction_id' => $transaction->id,
                'action' => 'approve',
            ]
        )->assertRedirect()->assertSessionHas('success');

        $this->assertSame(Transaction::PAYMENT_PAID, $transaction->refresh()->payment_status);
    }

    public function test_collection_fallback_without_id_only_runs_when_one_payment_is_verifying(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'VERIFY-SINGLE-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 175000,
            'total' => 175000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        $this->actingAs($admin)->patch(
            route('admin.payment-verifications.update-collection'),
            ['action' => 'approve']
        )->assertRedirect()->assertSessionHas('success');

        $this->assertSame(Transaction::PAYMENT_PAID, $transaction->refresh()->payment_status);
    }
}
