<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_uploaded_payment_proof(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('proof.jpg')->store('payment-proofs', 'public');
        $transaction = $this->transaction($user, $path);

        $this->actingAs($user)
            ->get(route('member.transactions.proof.show', $transaction))
            ->assertOk();
    }

    public function test_other_user_cannot_view_payment_proof(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $path = UploadedFile::fake()->image('proof.jpg')->store('payment-proofs', 'public');
        $transaction = $this->transaction($owner, $path);

        $this->actingAs($otherUser)
            ->get(route('member.transactions.proof.show', $transaction))
            ->assertRedirect(route('member.dashboard'))
            ->assertSessionHas('error');
    }

    public function test_verifying_payment_proof_can_be_reuploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $oldPath = UploadedFile::fake()->image('old-proof.jpg')->store('payment-proofs', 'public');
        $transaction = $this->transaction($user, $oldPath);

        $response = $this->actingAs($user)->post(
            route('member.transactions.proof', $transaction),
            ['payment_proof' => UploadedFile::fake()->image('new-proof.jpg')]
        );

        $response->assertSessionHasNoErrors();
        $transaction->refresh();
        $this->assertSame(Transaction::PAYMENT_VERIFYING, $transaction->payment_status);
        $this->assertNotSame($oldPath, $transaction->payment_proof);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($transaction->payment_proof);
    }

    private function transaction(User $user, string $proofPath): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'invoice_number' => 'PROOF-'.uniqid(),
            'payment_gateway' => 'manual_transfer',
            'payment_proof' => $proofPath,
            'subtotal' => 100000,
            'total' => 100000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);
    }
}
