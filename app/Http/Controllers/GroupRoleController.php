<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class GroupRoleController extends Controller
{
    /**
     * Group-level roles that can be managed (excluding super-admin)
     */
    protected array $groupRoles = ['group-admin', 'chairperson', 'secretary', 'treasurer', 'member'];

    /**
     * System-level permissions that should NOT be assignable to group roles
     */
    protected array $systemPermissions = [
        'view_admin_dashboard',
        'manage_groups',
        'approve_groups',
        'suspend_groups',
        'view_group_subscriptions',
        'manage_subscriptions',
        'send_renewal_notifications',
        'manage_system_users',
        'manage_system_settings',
        'manage_subscription_plans',
        'view_admin_reports',
        'view_revenue_reports',
        'view_subscription_reports',
    ];

    /**
     * Display all group roles and their permissions
     */
    public function index()
    {
        $roles = Role::whereIn('name', $this->groupRoles)
            ->with('permissions')
            ->get();

        $permissions = Permission::whereNotIn('name', $this->systemPermissions)
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                // Group permissions by category based on name prefix
                $name = $permission->name;
                return match(true) {
                    str_starts_with($name, 'view_') => 'View',
                    str_starts_with($name, 'create_') => 'Create',
                    str_starts_with($name, 'edit_') => 'Edit',
                    str_starts_with($name, 'delete_') => 'Delete',
                    str_starts_with($name, 'manage_') => 'Manage',
                    str_starts_with($name, 'approve_') => 'Approve',
                    str_starts_with($name, 'assign_') => 'Assign',
                    str_starts_with($name, 'import_') => 'Import',
                    str_starts_with($name, 'export_') => 'Export',
                    str_starts_with($name, 'change_') => 'Change',
                    str_starts_with($name, 'disburse_') => 'Disburse',
                    default => 'Other',
                };
            });

        // Build permission categories for organized display
        $permissionCategories = [
            'Members' => ['view_members', 'create_members', 'edit_members', 'delete_members', 'import_members', 'export_members'],
            'Collections' => ['view_collections', 'create_collections', 'edit_collections', 'delete_collections', 'import_collections'],
            'Loans' => ['view_loans', 'apply_loans', 'approve_loans', 'disburse_loans', 'manage_loan_repayments', 'edit_loans', 'delete_loans'],
            'Expenditures' => ['view_expenditures', 'create_expenditures', 'approve_expenditures', 'edit_expenditures', 'delete_expenditures'],
            'Reports' => ['view_group_reports', 'export_reports', 'view_collection_reports', 'view_loan_reports', 'view_yearly_reports', 'view_financial_statements'],
            'Users' => ['manage_group_users', 'assign_roles', 'view_activity_logs'],
            'Settings' => ['manage_collection_funds', 'manage_calendar_years', 'manage_loan_types', 'manage_group_settings', 'view_settings', 'change_calendar_year'],
        ];

        // Get available permissions organized by category
        $categorizedPermissions = [];
        foreach ($permissionCategories as $category => $permissionNames) {
            $perms = Permission::whereIn('name', $permissionNames)
                ->whereNotIn('name', $this->systemPermissions)
                ->get();
            if ($perms->count() > 0) {
                $categorizedPermissions[$category] = $perms;
            }
        }

        return view('users.roles', compact('roles', 'categorizedPermissions'));
    }

    /**
     * Update permissions for a group role
     */
    public function update(Request $request, Role $role)
    {
        // Prevent modification of super-admin
        if ($role->name === 'super-admin') {
            return redirect()->back()->with('error', 'Super Admin role cannot be modified.');
        }

        // Only allow modifying group-level roles
        if (!in_array($role->name, $this->groupRoles)) {
            return redirect()->back()->with('error', 'This role cannot be modified.');
        }

        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $permissions = $validated['permissions'] ?? [];

        // Filter out system permissions - safety check
        $permissions = array_diff($permissions, $this->systemPermissions);

        // Sync permissions (replaces all existing with new set)
        $role->syncPermissions($permissions);

        return redirect()->back()->with('success', 'Permissions for "' . ucfirst($role->name) . '" updated successfully.');
    }

    /**
     * Get role data for AJAX (used by DataTable or dynamic loading)
     */
    public function getRolePermissions(Role $role)
    {
        if ($role->name === 'super-admin' || !in_array($role->name, $this->groupRoles)) {
            return response()->json(['error' => 'Role not found or not accessible.'], 403);
        }

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    /**
     * Assign a role to a group user
     */
    public function assignRoleToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:' . implode(',', $this->groupRoles),
            'group_id' => 'required|exists:groups,id',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);
        $role = Role::where('name', $validated['role'])->firstOrFail();

        // Remove existing group role first
        foreach ($this->groupRoles as $r) {
            $user->removeRole($r);
        }

        // Assign new role
        $user->assignRole($role);

        // Update group_user pivot
        DB::table('group_user')
            ->where('user_id', $validated['user_id'])
            ->where('group_id', $validated['group_id'])
            ->update(['role_id' => $role->id, 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Role assigned to user successfully.');
    }
}
