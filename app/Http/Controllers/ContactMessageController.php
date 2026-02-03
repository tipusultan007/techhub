<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Show the public contact page.
     */
    public function index()
    {
        return view('frontend.contact');
    }

    /**
     * Store a public contact form submission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'phone'                => ['required', 'string', 'regex:/^(?:\+971|00971|0)?(?:50|51|52|54|55|56|58|2|3|4|6|7|9)\d{7}$/'],
            'subject'              => 'required|string|max:255',
            'message'              => 'required|string',
            'g-recaptcha-response' => 'required',
        ], [
            'phone.regex' => 'Please enter a valid UAE phone number.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ]);

        // Verify reCAPTCHA
        $secret = config('services.recaptcha.secret') ?: settings('recaptcha_secret_key');
        
        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'])->withInput();
        }

        \App\Models\ContactMessage::create($request->except('g-recaptcha-response'));

        return back()->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }

    /**
     * Admin: List all contact messages.
     */
    public function adminIndex()
    {
        $messages = \App\Models\ContactMessage::latest()->paginate(20);
        return view('admin.contact_messages.index', compact('messages'));
    }

    /**
     * Admin: Show a specific message.
     */
    public function adminShow(\App\Models\ContactMessage $message)
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
        return view('admin.contact_messages.show', compact('message'));
    }

    /**
     * Admin: Delete a message.
     */
    public function adminDestroy(\App\Models\ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.contact_messages.index')->with('success', 'Message deleted.');
    }
}
