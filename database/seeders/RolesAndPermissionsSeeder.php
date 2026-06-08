<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view_admin_dashboard',
            'view_group_dashboard',

            // Group Management (Super Admin)
            'manage_groups',
            'approve_groups',
            'suspend_groups',
            'view_group_subscriptions',
            'manage_subscriptions',
            'send_renewal_notifications',

            // Member Management
            'view_members',
            'create_members',
            'edit_members',
            'delete_members',
            'import_members',
            'export_members',

            // Collection Management
            'view_collections',
            'create_collections',
            'edit_collections',
            'delete_collections',
            'import_collections',

            // Loan Management
            'view_loans',
            'apply_loans',
            'approve_loans',
            'disburse_loans',
            'manage_loan_repayments',
            'edit_loans',
            'delete_loans',

            // Expenditure Management
            'view_expenditures',
            'create_expenditures',
            'approve_expenditures',
            'edit_expenditures',
            'delete_expenditures',

            // Reports
            'view_admin_reports',
            'view_group_reports',
            'export_reports',
            'view_revenue_reports',
            'view_subscription_reports',
            'view_collection_reports',
            'view_loan_reports',
            'view_yearly_reports',
            'view_financial_statements',

            // User Management
            'manage_system_users',
            'manage_group_users',
            'assign_roles',
            'view_activity_logs',

            // Settings
            'manage_subscription_plans',
            'manage_collection_funds',
            'manage_calendar_years',
            'manage_loan_types',
            'manage_group_settings',
            'manage_system_settings',

            // General
            'view_settings',
            'change_calendar_year',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'super-admin' => $permissions,
            'group-admin' => [
                'view_group_dashboard',
                'view_members', 'create_members', 'edit_members', 'delete_members',
                'view_collections', 'create_collections', 'edit_collections', 'delete_collections',
                'view_loans', 'apply_loans', 'approve_loans', 'disburse_loans', 'manage_loan_repayments', 'edit_loans',
                'view_expenditures', 'create_expenditures', 'edit_expenditures', 'approve_expenditures',
                'view_group_reports', 'export_reports', 'view_collection_reports', 'view_loan_reports', 'view_yearly_reports', 'view_financial_statements',
                'manage_group_users', 'view_activity_logs',
                'manage_collection_funds', 'manage_calendar_years', 'manage_loan_types', 'manage_group_settings',
                'view_settings', 'change_calendar_year',
            ],
            'chairperson' => [
                'view_group_dashboard',
                'view_members', 'create_members', 'edit_members', 'delete_members',
                'view_collections', 'create_collections', 'edit_collections', 'delete_collections',
                'view_loans', 'apply_loans', 'approve_loans', 'disburse_loans',
                'view_expenditures', 'create_expenditures', 'approve_expenditures', 'edit_expenditures', 'delete_expenditures',
                'view_group_reports', 'export_reports', 'view_collection_reports', 'view_loan_reports', 'view_yearly_reports', 'view_financial_statements',
                'manage_group_users', 'assign_roles', 'view_activity_logs',
                'manage_collection_funds', 'manage_calendar_years', 'manage_loan_types', 'manage_group_settings',
                'view_settings', 'change_calendar_year',
            ],
            'secretary' => [
                'view_group_dashboard',
                'view_members', 'create_members', 'edit_members',
                'view_collections', 'create_collections',
                'view_loans', 'apply_loans',
                'view_expenditures', 'create_expenditures',
                'view_group_reports', 'view_collection_reports', 'view_loan_reports',
                'view_activity_logs', 'change_calendar_year',
            ],
            'treasurer' => [
                'view_group_dashboard',
                'view_members',
                'view_collections', 'create_collections', 'edit_collections', 'delete_collections',
                'view_loans', 'disburse_loans', 'manage_loan_repayments', 'edit_loans',
                'view_expenditures', 'create_expenditures', 'approve_expenditures', 'edit_expenditures',
                'view_group_reports', 'view_collection_reports', 'view_loan_reports', 'view_financial_statements',
                'change_calendar_year',
            ],
            'member' => [
                'view_group_dashboard',
                'view_members',
                'view_collections',
                'view_loans', 'apply_loans',
                'view_group_reports',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
