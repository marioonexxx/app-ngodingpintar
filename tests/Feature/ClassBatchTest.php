<?php

namespace Tests\Feature;

use App\Models\ClassBatch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ClassBatchStatusService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ClassBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_number_is_automatic_and_previous_open_batch_is_closed(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->classProduct();
        $firstBatch = $product->classBatches()->create([
            ...$this->batchData(),
            'batch_number' => 1,
        ]);

        $this->actingAs($admin)->post(
            route('admin.class-products.batches.store', $product),
            $this->batchData()
        )->assertSessionHasNoErrors();

        $this->actingAs($admin)->post(
            route('admin.class-products.batches.store', $product),
            $this->batchData([
                'start_date' => '2026-09-01',
                'end_date' => '2026-09-01',
            ])
        )->assertSessionHasNoErrors();

        $this->assertSame([1, 2, 3], $product->classBatches()->orderBy('batch_number')->pluck('batch_number')->all());
        $this->assertSame(ClassBatch::STATUS_CLOSED, $firstBatch->refresh()->status);
        $this->assertSame(
            ClassBatch::STATUS_OPEN,
            $product->classBatches()->where('batch_number', 3)->value('status')
        );
        $this->assertSame(1, $product->classBatches()->open()->count());
    }

    public function test_cart_and_transaction_keep_the_selected_class_batch(): void
    {
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $product = $this->classProduct();
        $batch = $product->classBatches()->create([
            ...$this->batchData(),
            'batch_number' => 1,
        ]);

        $this->actingAs($member)->post(route('cart.store', $product))->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'class_batch_id' => $batch->id,
        ]);

        $this->actingAs($member)->post(route('checkout.store'), [
            'payment_gateway' => 'manual_transfer',
        ])->assertRedirect(route('member.transactions.active'));

        $transaction = Transaction::where('user_id', $member->id)->firstOrFail();
        $this->assertDatabaseHas('transaction_items', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'class_batch_id' => $batch->id,
        ]);
    }

    public function test_admin_can_read_update_and_delete_batch_using_specific_urls(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->classProduct();
        $batch = $product->classBatches()->create([
            ...$this->batchData(),
            'batch_number' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.class-products.batches.index', $product))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/ClassProducts/Batches')
                ->where('storeUrl', '/admin/class-products/'.$product->id.'/batches')
                ->where('batches.0.update_url', '/admin/class-products/'.$product->id.'/batches/'.$batch->id)
                ->where('batches.0.delete_url', '/admin/class-products/'.$product->id.'/batches/'.$batch->id)
            );

        $this->actingAs($admin)
            ->patch(
                route('admin.class-products.batches.update', [$product, $batch]),
                $this->batchData([
                    'status' => ClassBatch::STATUS_CLOSED,
                    'platform' => 'Google Meet',
                ])
            )
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('class_batches', [
            'id' => $batch->id,
            'status' => ClassBatch::STATUS_CLOSED,
            'platform' => 'Google Meet',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.class-products.batches.destroy', [$product, $batch]))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_batches', ['id' => $batch->id]);
    }

    public function test_collection_fallback_routes_can_update_and_delete_batch(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $product = $this->classProduct();
        $batch = $product->classBatches()->create([
            ...$this->batchData(),
            'batch_number' => 1,
        ]);

        $this->actingAs($admin)
            ->patch(
                route('admin.class-products.batches.update-collection', $product),
                [
                    ...$this->batchData(['platform' => 'Fallback Meet']),
                    'batch_id' => $batch->id,
                ]
            )
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('class_batches', [
            'id' => $batch->id,
            'platform' => 'Fallback Meet',
        ]);

        $this->actingAs($admin)
            ->delete(
                route('admin.class-products.batches.destroy-collection', $product),
                ['batch_id' => $batch->id]
            )
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_batches', ['id' => $batch->id]);
    }

    public function test_open_batch_is_closed_automatically_after_its_start_time(): void
    {
        CarbonImmutable::setTestNow(
            CarbonImmutable::parse('2026-08-01 09:01:00', 'Asia/Jakarta')
        );

        try {
            $product = $this->classProduct();
            $startedBatch = $product->classBatches()->create([
                ...$this->batchData([
                    'start_date' => '2026-08-01',
                    'start_time' => '09:00',
                ]),
                'batch_number' => 1,
            ]);
            $futureBatch = $product->classBatches()->create([
                ...$this->batchData([
                    'start_date' => '2026-08-01',
                    'end_date' => '2026-08-01',
                    'start_time' => '10:00',
                ]),
                'batch_number' => 2,
            ]);

            $closedCount = app(ClassBatchStatusService::class)->closeStartedBatches($product);

            $this->assertSame(1, $closedCount);
            $this->assertSame(ClassBatch::STATUS_CLOSED, $startedBatch->refresh()->status);
            $this->assertSame(ClassBatch::STATUS_OPEN, $futureBatch->refresh()->status);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    private function classProduct(): Product
    {
        return Product::create([
            'name' => 'Kelas Batch '.uniqid(),
            'slug' => 'kelas-batch-'.uniqid(),
            'price' => 99000,
            'status' => Product::STATUS_ACTIVE,
            'product_type' => 'class',
        ]);
    }

    private function batchData(array $overrides = []): array
    {
        return [
            'status' => ClassBatch::STATUS_OPEN,
            'schedule_type' => 'fixed',
            'start_date' => '2026-08-01',
            'end_date' => '2026-08-01',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'recurring_days' => [],
            'platform' => 'Zoom',
            'meeting_link' => 'https://zoom.us/example',
            'max_participants' => 20,
            ...$overrides,
        ];
    }
}
