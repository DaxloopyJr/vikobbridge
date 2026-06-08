<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing.home');
    }

    public function about()
    {
        return view('landing.about');
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function plans()
    {
        $plans = SubscriptionPlan::active()->orderBy('display_order')->get();
        return view('landing.plans', compact('plans'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        return redirect()->route('contact')
            ->with('success', 'Thank you for contacting us. We will get back to you soon!');
    }
}
