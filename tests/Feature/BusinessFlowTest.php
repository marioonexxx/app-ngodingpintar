<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Mentor;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan platform fee adalah 20%
        Setting::updateOrCreate(['key' => 'platform_fee_percent'], ['value' => '20']);
    }

    public function test_member_cannot_create_products_or_classes(): void
    {
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);

        $this->actingAs($member)
            ->get(route('vendor.products.create'))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($member)
            ->get(route('mentor.classes.create'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_vendor_can_only_create_source_code(): void
    {
        $vendorUser = User::factory()->create(['role' => User::ROLE_VENDOR]);
        Vendor::create(['user_id' => $vendorUser->id, 'store_name' => 'Toko Vendor', 'status' => 'approved']);

        // Vendor can access vendor route
        $this->actingAs($vendorUser)
            ->get(route('vendor.products.create'))
            ->assertSuccessful();

        // Vendor cannot access mentor route
        $this->actingAs($vendorUser)
            ->get(route('mentor.classes.create'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_mentor_can_only_create_classes(): void
    {
        $mentorUser = User::factory()->create(['role' => User::ROLE_MENTOR]);
        Mentor::create(['user_id' => $mentorUser->id, 'name' => 'Si Mentor', 'status' => 'approved']);

        // Mentor can access mentor route
        $this->actingAs($mentorUser)
            ->get(route('mentor.classes.create'))
            ->assertSuccessful();

        // Mentor cannot access vendor route
        $this->actingAs($mentorUser)
            ->get(route('vendor.products.create'))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_partner_can_create_both_source_code_and_classes(): void
    {
        $partnerUser = User::factory()->create(['role' => User::ROLE_PARTNER]);
        Vendor::create(['user_id' => $partnerUser->id, 'store_name' => 'Toko Dual', 'status' => 'approved']);
        Mentor::create(['user_id' => $partnerUser->id, 'name' => 'Mentor Dual', 'status' => 'approved']);

        $this->actingAs($partnerUser)
            ->get(route('vendor.products.create'))
            ->assertSuccessful();

        $this->actingAs($partnerUser)
            ->get(route('mentor.classes.create'))
            ->assertSuccessful();
    }

    public function test_member_buying_source_code_splits_earnings_correctly(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        
        $vendorUser = User::factory()->create(['role' => User::ROLE_VENDOR]);
        Vendor::create(['user_id' => $vendorUser->id, 'store_name' => 'Toko 1', 'status' => 'approved']);

        $category = ProductCategory::create(['name' => 'Source Code', 'slug' => 'sc', 'product_type' => 'source_code']);

        $product = Product::create([
            'name' => 'Source Code E-Commerce',
            'slug' => 'sc-ecommerce',
            'price' => 100000,
            'status' => Product::STATUS_ACTIVE,
            'product_type' => 'source_code',
            'vendor_id' => $vendorUser->id,
        ]);
        $product->categories()->sync([$category->id]);

        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'INV-SC-001',
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 100000,
            'total' => 100000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 100000,
            'subtotal' => 100000,
        ]);

        // Verify transaction as admin
        $this->actingAs($admin)
            ->patch(route('admin.transactions.verify', $transaction->id), [
                'action' => 'approve'
            ]);

        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_COMPLETED, $transaction->status);
        
        // Assert vendor received 80.000 (80%)
        $vendorUser->refresh();
        $this->assertEquals(80000, $vendorUser->balance);

        // Verify PartnerEarning row exists
        $this->assertDatabaseHas('partner_earnings', [
            'user_id' => $vendorUser->id,
            'transaction_id' => $transaction->id,
            'amount' => 100000,
            'platform_fee' => 20000,
            'net_earning' => 80000,
        ]);
    }

    public function test_member_buying_class_splits_earnings_correctly(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        
        $mentorUser = User::factory()->create(['role' => User::ROLE_MENTOR]);
        Mentor::create(['user_id' => $mentorUser->id, 'name' => 'Mentor 1', 'status' => 'approved']);

        $category = ProductCategory::create(['name' => 'Class', 'slug' => 'cl', 'product_type' => 'class']);

        $product = Product::create([
            'name' => 'Kelas React',
            'slug' => 'kelas-react',
            'price' => 200000,
            'status' => Product::STATUS_ACTIVE,
            'product_type' => 'class',
            'mentor_id' => $mentorUser->id,
        ]);
        $product->categories()->sync([$category->id]);

        $transaction = Transaction::create([
            'user_id' => $member->id,
            'invoice_number' => 'INV-CL-001',
            'payment_gateway' => 'manual_transfer',
            'subtotal' => 200000,
            'total' => 200000,
            'status' => Transaction::STATUS_PENDING,
            'payment_status' => Transaction::PAYMENT_VERIFYING,
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 200000,
            'subtotal' => 200000,
        ]);

        // Verify transaction as admin
        $this->actingAs($admin)
            ->patch(route('admin.transactions.verify', $transaction->id), [
                'action' => 'approve'
            ]);

        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_COMPLETED, $transaction->status);
        
        // Assert mentor received 160.000 (80%)
        $mentorUser->refresh();
        $this->assertEquals(160000, $mentorUser->balance);

        // Verify PartnerEarning row exists
        $this->assertDatabaseHas('partner_earnings', [
            'user_id' => $mentorUser->id,
            'transaction_id' => $transaction->id,
            'amount' => 200000,
            'platform_fee' => 40000,
            'net_earning' => 160000,
        ]);
    }
}
