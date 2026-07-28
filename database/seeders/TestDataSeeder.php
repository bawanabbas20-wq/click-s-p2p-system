<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PurchaseRequest;
use App\Models\RequestLog;
use App\Models\Offer;
use App\Models\Budget;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create test users for each role
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@click.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $bawan = User::create([
            'name' => 'Bawan Abbas',
            'email' => 'bawanabbas20@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@click.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $finance = User::create([
            'name' => 'Finance User',
            'email' => 'finance@click.com',
            'password' => Hash::make('password'),
            'role' => 'finance',
        ]);

        $procurement = User::create([
            'name' => 'Procurement User',
            'email' => 'procurement@click.com',
            'password' => Hash::make('password'),
            'role' => 'procurement',
        ]);

        $employees = [];
        for ($i = 1; $i <= 5; $i++) {
            $employees[] = User::create([
                'name' => "Employee $i",
                'email' => "employee$i@click.com",
                'password' => Hash::make('password'),
                'role' => 'employee',
            ]);
        }
        // 2. Create exchange rate setting
        Setting::create([
            'key' => 'exchange_rate_usd_to_iqd',
            'value' => '1450',
        ]);

        // 3. Create budgets for current month
        Budget::create([
            'year' => now()->year,
            'month' => now()->month,
            'budget_amount_iqd' => 5000000, // 5M IQD
            'budget_amount_usd' => 3000,    // 3K USD
        ]);

        // 4. Create completed purchase requests (for analytics)
        $completedItems = [
            ['Office Chairs', 450000, 'IQD', 'New office chairs for the team'],
            ['Laptop Dell XPS', 1200, 'USD', 'Development laptop for new hire'],
            ['Printer HP LaserJet', 350, 'USD', 'Office printer replacement'],
            ['Office Supplies', 125000, 'IQD', 'Monthly office supplies'],
            ['Software License', 500, 'USD', 'Adobe Creative Suite license'],
            ['Desk Accessories', 75000, 'IQD', 'Monitor stands and organizers'],
            ['Conference Phone', 800, 'USD', 'Meeting room phone system'],
            ['Stationery Items', 95000, 'IQD', 'Pens, papers, folders'],
        ];

        foreach ($completedItems as $index => $item) {
            $employee = $employees[array_rand($employees)];
            $createdDate = now()->subDays(rand(5, 30));
            
            $request = PurchaseRequest::create([
                'user_id' => $employee->id,
                'item_name' => $item[0],
                'estimated_price' => $item[1],
                'estimated_currency' => $item[2],
                'date_wanted' => $createdDate->addDays(rand(3, 10)),
                'justification' => $item[3],
                'status' => 'Completed',
                'created_at' => $createdDate,
                'updated_at' => $createdDate->addDays(rand(1, 5)),
            ]);

            // Create request logs for completed items
            RequestLog::create([
                'purchase_request_id' => $request->id,
                'user_id' => $employee->id,
                'old_status' => 'New',
                'new_status' => 'Pending Procurement',
                'comment' => 'Request submitted by employee.',
                'created_at' => $createdDate,
            ]);

            RequestLog::create([
                'purchase_request_id' => $request->id,
                'user_id' => $procurement->id,
                'old_status' => 'Pending Procurement',
                'new_status' => 'Pending Finance',
                'comment' => 'Escalated to Finance for approval.',
                'created_at' => $createdDate->addHours(2),
            ]);

            RequestLog::create([
                'purchase_request_id' => $request->id,
                'user_id' => $finance->id,
                'old_status' => 'Pending Finance',
                'new_status' => 'Approved for Purchase',
                'comment' => 'Approved by Finance team.',
                'created_at' => $createdDate->addHours(4),
            ]);

            // Create offers (quotations)
            $finalPrice = $item[1] + rand(-50, 100); // Slight variation from estimate
            
            // Create a chosen offer
            Offer::create([
                'purchase_request_id' => $request->id,
                'vendor_name' => 'Vendor ' . chr(65 + $index), // Vendor A, B, C, etc.
                'price' => max($finalPrice, 1),
                'currency' => $item[2],
                'is_chosen' => true,
            ]);

            // Create 1-2 rejected offers
            for ($k = 0; $k < rand(1, 2); $k++) {
                 Offer::create([
                    'purchase_request_id' => $request->id,
                    'vendor_name' => 'Other Vendor ' . ($k + 1),
                    'price' => max($finalPrice + rand(10, 500), 1),
                    'currency' => $item[2],
                    'is_chosen' => false,
                ]);
            }
        }

        // 5. Create pending requests (for approval queues)
        $pendingItems = [
            ['New Monitors', 600, 'USD', 'Pending Finance', 'Dual monitors for developers'],
            ['Office Furniture', 250000, 'IQD', 'Pending Procurement', 'Desks and chairs for new office'],
            ['Server Hardware', 2500, 'USD', 'Pending Manager', 'Database server upgrade'],
            ['Marketing Materials', 180000, 'IQD', 'Pending Finance', 'Brochures and business cards'],
            ['Software Tools', 150, 'USD', 'Pending Procurement', 'Development tools subscription'],
        ];

        foreach ($pendingItems as $item) {
            $employee = $employees[array_rand($employees)];
            $createdDate = now()->subDays(rand(1, 7));
            
            $request = PurchaseRequest::create([
                'user_id' => $employee->id,
                'item_name' => $item[0],
                'estimated_price' => $item[1],
                'estimated_currency' => $item[2],
                'date_wanted' => now()->addDays(rand(5, 15)),
                'justification' => $item[4],
                'status' => $item[3],
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ]);

            // Create initial log
            RequestLog::create([
                'purchase_request_id' => $request->id,
                'user_id' => $employee->id,
                'old_status' => 'New',
                'new_status' => $item[3],
                'comment' => 'Request submitted and routed for approval.',
                'created_at' => $createdDate,
            ]);
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@click.com / password');
        $this->command->info('Manager: manager@click.com / password');
        $this->command->info('Finance: finance@click.com / password');
        $this->command->info('Procurement: procurement@click.com / password');
        $this->command->info('Employees: employee1@click.com to employee5@click.com / password');
    }
}
