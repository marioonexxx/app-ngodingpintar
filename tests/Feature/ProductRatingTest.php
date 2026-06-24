<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_rating_only_uses_approved_testimonials(): void
    {
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $product = Product::create([
            'name' => 'Produk Rating',
            'slug' => 'produk-rating',
            'price' => 100000,
            'status' => Product::STATUS_ACTIVE,
            'product_type' => 'digital_product',
        ]);

        foreach ([
            [5, Testimonial::STATUS_APPROVED],
            [4, Testimonial::STATUS_APPROVED],
            [1, Testimonial::STATUS_PENDING],
            [1, Testimonial::STATUS_REJECTED],
        ] as [$rating, $status]) {
            Testimonial::create([
                'user_id' => $member->id,
                'product_id' => $product->id,
                'rating' => $rating,
                'content' => 'Testimonial produk untuk pengujian.',
                'status' => $status,
                'approved_at' => $status === Testimonial::STATUS_APPROVED ? now() : null,
            ]);
        }

        $ratedProduct = Product::withApprovedRating()->findOrFail($product->id);

        $this->assertSame(2, $ratedProduct->approved_testimonials_count);
        $this->assertEqualsWithDelta(4.5, $ratedProduct->approved_testimonials_avg_rating, 0.01);
    }
}
