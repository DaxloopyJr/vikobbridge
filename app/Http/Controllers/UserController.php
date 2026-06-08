<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Traits\DataTableTrait;

class UserController extends Controller
{
    use DataTableTrait;
    public function __construct()
    {
        $this->middleware('permission:manage_system_users')->only(['systemUsers', 'storeSystemUser', 'editSystemUser', 'updateSystemUser', 'destroySystemUser']);
        $this->middleware('permission:manage_group_users')->only(['groupUsers', 'storeGroupUser', 'editGroupUser', 'updateGroupUser', 'destroyGroupUser']);
        $this->middleware('permission:view_activity_logs')->only(['activityLogs']);
    }

    public function systemUsers(Request $request)
    {
        $query = User::with('roles')->whereHas('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->orWhereDoesntHave('groups');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::where('name', 'super-admin')->get();

        return view('users.system', compact('users', 'roles'));
    }

    public function systemUsersData(Request $request)
    {
        $query = User::with('roles')->whereHas('roles', function ($q) {
            $q->where('name', 'super-admin');
        })->orWhereDoesntHave('groups');

        $result = $this->processDataTable($request, $query, ['first_name', 'last_name', 'email']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $roleBadges = $item->roles && $item->roles->count() > 0 ? $item->roles->map(fn($r) => '<span class="badge bg-light text-dark">' . e($r->name) . '</span>')->implode(' ') : '<span class="text-muted">-</span>';
            $statusBadge = $item->status === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">' . ucfirst($item->status) . '</span>';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<a href="' . route('users.system.edit', $item->id) . '" class="btn btn-outline-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil"></i></a>';
            $actions .= '<form method="POST" action="' . route('users.system.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Delete?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Delete"><i class="bi bi-trash"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->first_name . ' ' . $item->last_name) . '</strong>',
                'email' => e($item->email),
                'phone_number' => e($item->phone_number ?? '-'),
                'roles' => $roleBadges,
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storeSystemUser(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'profile_completed' => true,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.system')->with('success', 'System user created successfully.');
    }

    public function editSystemUser(User $user)
    {
        $roles = Role::all();
        return view('users.edit_system', compact('user', 'roles'));
    }

    public function updateSystemUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.system')->with('success', 'System user updated successfully.');
    }

    public function destroySystemUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return redirect()->route('users.system')->with('success', 'System user deleted successfully.');
    }

    public function groupUsers(Request $request)
    {
        $groupId = session('current_group_id');
        $query = User::whereHas('groups', function ($q) use ($groupId) {
            $q->where('groups.id', $groupId);
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20);
        $group = Group::find($groupId);
        $members = Member::where('group_id', $groupId)->where('status', 'active')->get();
        $roles = Role::whereIn('name', ['chairperson', 'secretary', 'treasurer', 'group-admin', 'member'])->get();

        return view('users.group', compact('users', 'group', 'members', 'roles'));
    }

    public function groupUsersData(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $query = User::with(['groups' => function ($q) use ($groupId) {
            $q->where('groups.id', $groupId);
        }])->whereHas('groups', function ($q) use ($groupId) {
            $q->where('groups.id', $groupId);
        });

        $result = $this->processDataTable($request, $query, ['first_name', 'last_name', 'email']);
        $result['data'] = collect($result['data'])->map(function ($item) use ($groupId) {
            $groupPivot = $item->groups->first();
            $roleName = '<span class="badge bg-secondary">Member</span>';
            $pivotStatus = 'active';
            $pivotJoined = null;
            if ($groupPivot && $groupPivot->pivot) {
                $pivotStatus = $groupPivot->pivot->status ?? 'active';
                $pivotJoined = $groupPivot->pivot->joined_at;
                if ($groupPivot->pivot->role_id) {
                    $roleName = '<span class="badge bg-light text-dark">' . e(\Spatie\Permission\Models\Role::find($groupPivot->pivot->role_id)?->name ?? 'Member') . '</span>';
                }
            }
            $statusBadge = $pivotStatus === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">' . ucfirst($pivotStatus) . '</span>';
            $actions = '<div class="btn-group btn-group-sm">';
            $actions .= '<form method="POST" action="' . route('users.group.destroy', $item->id) . '" class="d-inline" onsubmit="return confirm(\'Remove from group?\')"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Remove"><i class="bi bi-person-x"></i></button></form>';
            $actions .= '</div>';
            return [
                'id' => $item->id,
                'name' => '<strong>' . e($item->first_name . ' ' . $item->last_name) . '</strong>',
                'email' => e($item->email),
                'role' => $roleName,
                'status' => $statusBadge,
                'joined_at' => $pivotJoined ? '<small>' . \Carbon\Carbon::parse($pivotJoined)->format('M d, Y') . '</small>' : '<small class="text-muted">-</small>',
                'actions' => $actions,
            ];
        })->toArray();
        return response()->json($result);
    }

    public function storeGroupUser(Request $request)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $user = $member->user;

        if (!$user) {
            $user = User::create([
                'first_name' => $member->first_name,
                'middle_name' => $member->middle_name,
                'last_name' => $member->last_name,
                'email' => $member->email ?? 'user' . $member->id . '@vicobridge.local',
                'phone_number' => $member->phone_number,
                'password' => Hash::make('password123'),
                'status' => 'active',
                'profile_completed' => true,
            ]);

            $member->update(['user_id' => $user->id]);
        }

        $role = Role::where('name', $validated['role'])->first();
        
        if (!$user->groups()->where('groups.id', $groupId)->exists()) {
            $user->groups()->attach($groupId, [
                'role_id' => $role->id,
                'joined_at' => now(),
                'status' => $validated['status'],
            ]);
        } else {
            $user->groups()->updateExistingPivot($groupId, [
                'role_id' => $role->id,
                'status' => $validated['status'],
            ]);
        }

        $user->assignRole($validated['role']);

        return redirect()->route('users.group')->with('success', 'Group user added successfully.');
    }

    public function updateGroupUser(Request $request, User $user)
    {
        $groupId = session('current_group_id');

        $validated = $request->validate([
            'role' => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $role = Role::where('name', $validated['role'])->first();

        $user->groups()->updateExistingPivot($groupId, [
            'role_id' => $role->id,
            'status' => $validated['status'],
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.group')->with('success', 'Group user updated successfully.');
    }

    public function destroyGroupUser(User $user)
    {
        $groupId = session('current_group_id');
        $user->groups()->detach($groupId);

        return redirect()->route('users.group')->with('success', 'User removed from group successfully.');
    }

    public function activityLogs(Request $request)
    {
        $groupId = session('current_group_id');
        $query = ActivityLog::with(['user'])->where('group_id', $groupId);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(50);
        $users = User::whereHas('groups', function ($q) use ($groupId) {
            $q->where('groups.id', $groupId);
        })->get();

        return view('users.activity_logs', compact('logs', 'users'));
    }

    public function activityLogsData(Request $request)
    {
        $groupId = $this->currentGroupId();
        if (!$groupId) return response()->json(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);

        $query = ActivityLog::with(['user'])->where('group_id', $groupId);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);

        $result = $this->processDataTable($request, $query, ['action', 'description']);
        $result['data'] = collect($result['data'])->map(function ($item) {
            $userName = $item->user ? e($item->user->first_name . ' ' . $item->user->last_name) : '<span class="text-muted">System</span>';
            $actionBadge = match($item->action) {
                'create' => '<span class="badge bg-success">Create</span>',
                'update' => '<span class="badge bg-warning text-dark">Update</span>',
                'delete' => '<span class="badge bg-danger">Delete</span>',
                'login' => '<span class="badge bg-info">Login</span>',
                'logout' => '<span class="badge bg-secondary">Logout</span>',
                'approve' => '<span class="badge bg-primary">Approve</span>',
                'reject' => '<span class="badge bg-dark">Reject</span>',
                default => '<span class="badge bg-light text-dark">' . ucfirst($item->action) . '</span>',
            };
            return [
                'id' => $item->id,
                'user' => $userName,
                'action' => $actionBadge,
                'description' => e($item->description ?? '-'),
                'ip_address' => '<code class="small">' . e($item->ip_address ?? '-') . '</code>',
                'created_at' => '<small>' . \Carbon\Carbon::parse($item->created_at)->format('M d, Y H:i') . '</small>',
            ];
        })->toArray();
        return response()->json($result);
    }
}
