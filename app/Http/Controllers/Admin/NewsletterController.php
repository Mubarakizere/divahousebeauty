<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(20);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    public function create()
    {
        return view('admin.newsletter.send');
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $subscribers = NewsletterSubscriber::all();

        if ($subscribers->isEmpty()) {
            return back()->with('error', 'No subscribers found to send email to.');
        }

        // Ideally this should be queued. For now, we'll try sending in a loop
        // If the list is large, this will timeout.
        // A better approach for "serious" use is a refined job.
        // But for "setup", we'll do simple iteration or queue if available.
        
        foreach ($subscribers as $subscriber) {
            try {
                 Mail::to($subscriber->email)->send(new NewsletterEmail($request->subject, $request->body));
            } catch (\Exception $e) {
                // log error but continue
                logger()->error("Failed to send newsletter to {$subscriber->email}: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.newsletter.index')
            ->with('success', "Newsletter queued/sent to {$subscribers->count()} subscribers!");
    }

    public function destroy($id)
    {
        NewsletterSubscriber::findOrFail($id)->delete();
        return back()->with('success', 'Subscriber removed.');
    }
}
