<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Services\SelcomPaymentService;
use App\Traits\DataTableTrait;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    use DataTableTrait;

    protected SelcomPaymentService $paymentService;

    public function __construct(SelcomPaymentService $paymentService)
    {
        $this->middleware('permission:manage_groups')->only(['index', 'approve', 'suspend', 'renewals', 'sendNotification']);
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        
        if ($tab === 'pending') {
            $groups = Group::with(['chairman', 'subscriptionPlan'])
                ->whereIn('status', ['pending', 'active'])
                ->where(function ($q) {
                    $q->where('payment_status', 'trial')
                      ->orWhere('payment_status', 'pending');
                })
                ->latest()
                ->paginate(20);
        } elseif ($tab === 'approved') {
            $groups = Group::with(['chairman', 'subscriptionPlan'])
                ->where('status', 'active')
                ->where('payment_status', 'paid')
                ->latest()
                ->paginate(20);
        } elseif ($tab === 'expired') {
            $groups = Group::with(['chairman', 'subscriptionPlan'])
                ->where(function ($q) {
                    $q->where('status', 'expired')
                      ->orWhere(function ($sub) {
                          $sub->whereNotNull('subscription_end_date')
                              ->where('subscription_end_date', '<', now());
                      });
                })
                ->latest()
                ->paginate(20);
        } else {
            $groups = Group::with(['chairman', 'subscriptionPlan'])
                ->latest()
                ->paginate(20);
        }

        return view('groups.index', compact('groups', 'tab'));
    }

    /**
     * Server-side DataTables endpoint for groups
     */
    public function data(Request $request)
    {
        $tab = $request->input('tab', 'all');

        $query = Group::with(['chairman', 'subscriptionPlan']);

        if ($tab === 'pending') {
            $query->whereIn('status', ['pending', 'active'])
                ->where(function ($q) {
                    $q->where('payment_status', 'trial')
                      ->orWhere('payment_status', 'pending');
                });
        } elseif ($tab === 'approved') {
            $query->where('status', 'active')->where('payment_status', 'paid');
        } elseif ($tab === 'expired') {
            $query->where(function ($q) {
                $q->where('status', 'expired')
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('subscription_end_date')
                          ->where('subscription_end_date', '<', now());
                  });
            });
        }

        $result = $this->processDataTable($request, $query, ['name', 'registration_number']);

        $result['data'] = collect($result['data'])->map(function ($item) {
            $statusBadge = match($item->status) {
                'active' => '<span class="badge bg-success">Active</span>',
                'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                'suspended' => '<span class="badge bg-danger">Suspended</span>',
                'expired' => '<span class="badge bg-secondary">Expired</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->status) . '</span>',
            };
            $paymentBadge = match($item->payment_status) {
                'trial' => '<span class="badge bg-info">Trial</span>',
                'paid' => '<span class="badge bg-success">Paid</span>',
                'expired' => '<span class="badge bg-danger">Expired</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->payment_status) . '</span>',
            };

            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . route('groups.show', $item->id) . '" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></a>';
            $actions .= '<a href="' . route('groups.edit', $item->id) . '" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>';
            if ($item->status === 'pending') {
                $actions .= '<form method="POST" action="' . route('groups.approve', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Approve this group?\')">' . csrf_field() . '<button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Approve"><i class="bi bi-check-lg"></i></button></form>';
            }
            $actions .= '<form method="POST" action="' . route('groups.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete this group?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';

            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->name) . '</strong><br><small class="text-muted">' . e($item->registration_number) . '</small>',
                'chairman' => $item->chairman ? e($item->chairman->full_name) : '<span class="text-muted">-</span>',
                'plan' => '<span class="badge bg-light text-dark">' . ($item->subscriptionPlan->name ?? 'N/A') . '</span>',
                'status' => $statusBadge . ' ' . $paymentBadge,
                'subscription_end_date' => $item->subscription_end_date ? '<small>' . \Carbon\Carbon::parse($item->subscription_end_date)->format('M d, Y') . '</small>' : '<small class="text-muted">N/A</small>',
                'actions' => $actions,
            ];
        })->toArray();

        return response()->json($result);
    }

    public function approve(Group $group)
    {
        $group->update([
            'status' => 'active',
            'payment_status' => 'paid',
        ]);

        if (!$group->subscription_end_date) {
            $plan = $group->subscriptionPlan;
            $group->update([
                'subscription_end_date' => now()->addDays($plan->duration_days),
            ]);
        }

        return redirect()->back()->with('success', 'Group approved successfully.');
    }

    public function suspend(Group $group, Request $request)
    {
        $request->validate(['reason' => 'required|string']);
        
        $group->update([
            'status' => 'suspended',
        ]);

        return redirect()->back()->with('success', 'Group suspended: ' . $request->reason);
    }

    public function renewals()
    {
        $expiringSoon = Group::with(['chairman', 'subscriptionPlan'])
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->whereNotNull('subscription_end_date')
            ->where('subscription_end_date', '<=', now()->addDays(14))
            ->where('subscription_end_date', '>=', now())
            ->latest()
            ->paginate(20);

        $expired = Group::with(['chairman', 'subscriptionPlan'])
            ->where(function ($q) {
                $q->where('status', 'expired')
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('subscription_end_date')
                          ->where('subscription_end_date', '<', now());
                  });
            })
            ->latest()
            ->paginate(20);

        return view('groups.renewals', compact('expiringSoon', 'expired'));
    }

    public function sendNotification(Request $request, Group $group)
    {
        $request->validate([
            'message' => 'required|string|max:480',
        ]);

        return redirect()->back()->with('success', 'Notification sent to ' . $group->name);
    }

    public function show(Group $group)
    {
        $group->load(['chairman', 'subscriptionPlan', 'members', 'calendarYears']);
        $payments = $group->payments()->latest()->paginate(10);
        
        return view('groups.show', compact('group', 'payments'));
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $group->update($validated);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }
}
