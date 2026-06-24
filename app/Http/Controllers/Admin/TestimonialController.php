<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::with(['user', 'product'])->latest()->paginate(15);

        $testimonials->getCollection()->each(function (Testimonial $testimonial): void {
            $testimonial->setAttribute(
                'approve_url',
                route('admin.testimonials.approve', ['testimonial' => $testimonial->id], false)
            );
            $testimonial->setAttribute(
                'reject_url',
                route('admin.testimonials.reject', ['testimonial' => $testimonial->id], false)
            );
        });

        return Frontend::render('Admin/Testimonials', [
            'testimonials' => $testimonials,
        ]);
    }

    public function approve(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'status' => Testimonial::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        return back(303)->with('success', 'Testimonial disetujui.');
    }

    public function reject(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'status' => Testimonial::STATUS_REJECTED,
            'approved_at' => null,
        ]);

        return back(303)->with('success', 'Testimonial ditolak.');
    }
}
