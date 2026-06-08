<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Group;
use App\Models\SubscriptionPlan;
use App\Services\SelcomPaymentService;

class PaymentController extends Controller
{
    protected SelcomPaymentService $paymentService;

    public function __construct(SelcomPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(Request $request)
    {
        $groupId = session('current_group_id');
        $group = Group::findOrFail($groupId);
        $plan = $group->subscriptionPlan;

        $orderId = Payment::generateTransactionId();

        $payment = Payment::create([
            'group_id' => $groupId,
            'subscription_plan_id' => $plan->id,
            'transaction_id' => $orderId,
            'order_id' => $orderId,
            'amount' => $plan->price,
            'currency' => 'TZS',
            'status' => 'pending',
            'is_renewal' => $group->payment_status === 'expired' || $group->payment_status === 'paid',
        ]);

        $result = $this->paymentService->createPaymentOrder([
            'order_id' => $orderId,
            'name' => $group->name,
            'email' => $group->email,
            'phone' => $group->phone_number,
            'amount' => $plan->price,
            'currency' => 'TZS',
            'return_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
        ]);

        if ($result['success'] && $result['payment_url']) {
            return redirect()->away($result['payment_url']);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Failed to initiate payment. Please try again.');
    }

    public function controlNumber(Request $request)
    {
        $groupId = session('current_group_id');
        $group = Group::findOrFail($groupId);
        $plan = $group->subscriptionPlan;

        $orderId = Payment::generateTransactionId();

        $payment = Payment::create([
            'group_id' => $groupId,
            'subscription_plan_id' => $plan->id,
            'transaction_id' => $orderId,
            'order_id' => $orderId,
            'amount' => $plan->price,
            'currency' => 'TZS',
            'status' => 'pending',
            'is_renewal' => false,
        ]);

        $result = $this->paymentService->generateControlNumber([
            'order_id' => $orderId,
            'name' => $group->name,
            'email' => $group->email,
            'phone' => $group->phone_number,
            'amount' => $plan->price,
            'currency' => 'TZS',
        ]);

        if ($result['success']) {
            $payment->update(['control_number' => $result['control_number']]);
            return redirect()->route('dashboard')
                ->with('success', 'Control number generated: ' . $result['control_number']);
        }

        return redirect()->route('dashboard')
            ->with('error', 'Failed to generate control number.');
    }

    public function callback(Request $request)
    {
        $processed = $this->paymentService->processCallback($request->all());

        if ($processed) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    public function success(Request $request)
    {
        return redirect()->route('dashboard')
            ->with('success', 'Payment processed successfully. Your subscription is now active.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('dashboard')
            ->with('info', 'Payment was cancelled. You can try again anytime.');
    }

    public function expired()
    {
        $groupId = session('current_group_id');
        $group = Group::find($groupId);
        $plans = SubscriptionPlan::active()->get();

        return view('payment.expired', compact('group', 'plans'));
    }

    public function history()
    {
        $groupId = session('current_group_id');
        $payments = Payment::where('group_id', $groupId)->latest()->paginate(20);

        return view('payment.history', compact('payments'));
    }
}
