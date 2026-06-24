<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTestimonialApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_page_contains_testimonial_specific_action_urls(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $testimonial = Testimonial::create([
            'user_id' => $member->id,
            'rating' => 5,
            'title' => 'Bagus',
            'content' => 'Produknya sangat membantu sekali.',
            'status' => Testimonial::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.testimonials.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Testimonials')
                ->where('testimonials.data.0.approve_url', '/admin/testimonials/'.$testimonial->id.'/approve')
                ->where('testimonials.data.0.reject_url', '/admin/testimonials/'.$testimonial->id.'/reject')
            );
    }

    public function test_admin_can_approve_testimonial(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $member = User::factory()->create(['role' => User::ROLE_MEMBER]);
        $testimonial = Testimonial::create([
            'user_id' => $member->id,
            'rating' => 5,
            'title' => 'Bagus',
            'content' => 'Produknya sangat membantu sekali.',
            'status' => Testimonial::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.testimonials.approve', $testimonial))
            ->assertRedirect()
            ->assertSessionHas('success');

        $testimonial->refresh();
        $this->assertSame(Testimonial::STATUS_APPROVED, $testimonial->status);
        $this->assertNotNull($testimonial->approved_at);
    }
}
