<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use App\Support\Frontend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectRequestController extends Controller
{
    public function create()
    {
        return Frontend::render('Frontend/ProjectRequest/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:new_app,revision'],
            'budget' => ['required', 'numeric', 'min:50000'],
            'deadline_date' => ['required', 'date', 'after:today'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,rar', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('project_requests/attachments', 'public');
        }

        if (Auth::check()) {
            $user = Auth::user();
            ProjectRequest::create([
                'user_id' => $user->id,
                'title' => $validated['title'],
                'category' => $validated['category'],
                'budget' => $validated['budget'],
                'deadline_date' => $validated['deadline_date'],
                'whatsapp' => $validated['whatsapp'],
                'description' => $validated['description'],
                'attachment' => $attachmentPath,
                'status' => 'pending_admin',
            ]);

            return redirect()->route('member.project-requests.index')->with('success', 'Project request berhasil dibuat dan menunggu verifikasi Admin.');
        }

        // Store in session if guest
        $sessionData = $validated;
        $sessionData['attachment'] = $attachmentPath;
        session(['pending_project_request' => $sessionData]);
        
        // Save intended url to member dashboard
        session(['url.intended' => route('member.project-requests.index')]);

        return redirect()->route('login')->with('success', 'Silakan login terlebih dahulu. Form request Anda telah disimpan dan otomatis disubmit setelah login.');
    }
}
