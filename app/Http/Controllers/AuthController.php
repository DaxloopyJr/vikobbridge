<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Group;
use App\Models\SubscriptionPlan;
use App\Services\SelcomPaymentService;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected SelcomPaymentService $paymentService;

    public function __construct(SelcomPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            $primaryGroup = $user->primaryGroup()->first() ?? $user->groups()->first();
            if ($primaryGroup) {
                session(['current_group_id' => $primaryGroup->id]);
            }

            if (!$user->profile_completed) {
                return redirect()->route('profile.wizard');
            }

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegister()
    {
        $plans = SubscriptionPlan::active()->orderBy('display_order')->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'chairman_first_name' => 'required|string|max:255',
            'chairman_middle_name' => 'nullable|string|max:255',
            'chairman_last_name' => 'required|string|max:255',
            'chairman_phone' => 'required|string|max:20|unique:users,phone_number',
            'chairman_email' => 'required|email|max:255|unique:users,email',
            'subscription_plan' => 'required|exists:subscription_plans,slug',
            'group_name' => 'required|string|max:255',
            'group_registration_number' => 'required|string|max:255|unique:groups,registration_number',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'village_id' => 'required|exists:villages,id',
            'region' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'village' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        $plan = SubscriptionPlan::where('slug', $validated['subscription_plan'])->firstOrFail();

        $user = User::create([
            'first_name' => $validated['chairman_first_name'],
            'middle_name' => $validated['chairman_middle_name'],
            'last_name' => $validated['chairman_last_name'],
            'phone_number' => $validated['chairman_phone'],
            'email' => $validated['chairman_email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $user->assignRole('chairperson');

        $trialEndsAt = now()->addDays(14);

        $group = Group::create([
            'name' => $validated['group_name'],
            'registration_number' => $validated['group_registration_number'],
            'chairman_id' => $user->id,
            'region' => $validated['region'],
            'district' => $validated['district'],
            'ward' => $validated['ward'],
            'village' => $validated['village'],
            'region_id' => $validated['region_id'],
            'district_id' => $validated['district_id'],
            'ward_id' => $validated['ward_id'],
            'village_id' => $validated['village_id'],
            'subscription_plan_id' => $plan->id,
            'subscription_date' => now(),
            'trial_ends_at' => $trialEndsAt,
            'payment_status' => 'trial',
            'status' => 'active',
            'email' => $validated['chairman_email'],
            'phone_number' => $validated['chairman_phone'],
        ]);

        $user->groups()->attach($group->id, [
            'role_id' => \Spatie\Permission\Models\Role::where('name', 'chairperson')->first()->id,
            'is_primary_group' => true,
            'joined_at' => now(),
            'status' => 'active',
        ]);

        $defaultFunds = [
            ['name' => 'Hisa', 'slug' => 'hisa', 'fund_type' => 'savings', 'description' => 'Member share savings'],
            ['name' => 'Jamii', 'slug' => 'jamii', 'fund_type' => 'contribution', 'description' => 'Community contribution'],
            ['name' => 'Rejesho', 'slug' => 'rejesho', 'fund_type' => 'contribution', 'description' => 'Loan repayment contribution'],
            ['name' => 'Ada', 'slug' => 'ada', 'fund_type' => 'fee', 'description' => 'Membership fees'],
            ['name' => 'Faini', 'slug' => 'faini', 'fund_type' => 'fine', 'description' => 'Fines and penalties'],
            ['name' => 'Mradi', 'slug' => 'mradi', 'fund_type' => 'project', 'description' => 'Project contributions'],
        ];

        foreach ($defaultFunds as $fund) {
            \App\Models\CollectionFund::create(array_merge($fund, ['group_id' => $group->id]));
        }

        $calendarYear = \App\Models\CalendarYear::create([
            'group_id' => $group->id,
            'name' => date('Y') . ' Calendar Year',
            'year' => date('Y'),
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'status' => 'active',
            'is_current' => true,
        ]);

        Auth::login($user);
        session(['current_group_id' => $group->id]);

        if ($request->has('pay_now')) {
            $orderId = \App\Models\Payment::generateTransactionId();
            
            $payment = \App\Models\Payment::create([
                'group_id' => $group->id,
                'subscription_plan_id' => $plan->id,
                'transaction_id' => $orderId,
                'order_id' => $orderId,
                'amount' => $plan->price,
                'currency' => 'TZS',
                'status' => 'pending',
                'is_renewal' => false,
            ]);

            $result = $this->paymentService->createPaymentOrder([
                'order_id' => $orderId,
                'name' => $validated['group_name'],
                'email' => $validated['chairman_email'],
                'phone' => $validated['chairman_phone'],
                'amount' => $plan->price,
                'currency' => 'TZS',
                'return_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
            ]);

            if ($result['success'] && $result['payment_url']) {
                return redirect()->away($result['payment_url']);
            }

            return redirect()->route('dashboard')
                ->with('info', 'Group registered successfully! Your 14-day trial has started. You can make payment anytime.');
        }

        return redirect()->route('profile.wizard')
            ->with('success', 'Registration successful! Your 14-day trial has started. Please complete your profile.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }
}
