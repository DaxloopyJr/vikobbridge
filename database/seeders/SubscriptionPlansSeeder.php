<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Monthly Plan',
                'slug' => 'monthly',
                'description' => 'Perfect for small groups getting started with digital management',
                'price' => 15000,
                'billing_cycle' => 'monthly',
                'duration_days' => 30,
                'features' => [
                    'Up to 50 members',
                    'Full dashboard access',
                    'Collection management',
                    'Loan management',
                    'Basic reports',
                    'Email support',
                ],
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Quarterly Plan',
                'slug' => 'quarterly',
                'description' => 'Ideal for growing groups with expanding membership',
                'price' => 40000,
                'billing_cycle' => 'quarterly',
                'duration_days' => 90,
                'features' => [
                    'Up to 150 members',
                    'Full dashboard access',
                    'Collection management',
                    'Loan management with EMI',
                    'Advanced reports',
                    'Priority support',
                    'SMS notifications',
                    'Data export',
                ],
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Annual Plan',
                'slug' => 'annually',
                'description' => 'Best value for established groups with comprehensive needs',
                'price' => 140000,
                'billing_cycle' => 'annually',
                'duration_days' => 365,
                'features' => [
                    'Unlimited members',
                    'Full dashboard access',
                    'Collection management',
                    'Advanced loan management',
                    'Complete reports & analytics',
                    'Priority 24/7 support',
                    'SMS & Email notifications',
                    'Data export & backup',
                    'Financial statements',
                    'Multi-year calendar',
                    'Custom branding',
                ],
                'is_active' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
