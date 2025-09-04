<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $customers = [
            // Individual customers
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'juan.delacruz@email.com',
                'phone' => '+63-912-345-6789',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'maria.santos@email.com',
                'phone' => '+63-918-765-4321',
            ],
            [
                'first_name' => 'Roberto',
                'last_name' => 'Garcia',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 0,
                'email' => 'roberto.garcia@email.com',
                'phone' => '+63-917-123-4567',
            ],
            
            // Company customers
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'ABC Trading Corporation',
                'type' => 'company',
                'share_holder' => 1,
                'email' => 'contact@abctrading.com',
                'phone' => '+63-2-8123-4567',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Philippine Shipping Lines Inc.',
                'type' => 'company',
                'share_holder' => 0,
                'email' => 'info@pslshipping.com',
                'phone' => '+63-2-8987-6543',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Metro Manila Transport Co.',
                'type' => 'company',
                'share_holder' => 1,
                'email' => 'operations@mmtransport.com',
                'phone' => '+63-2-8456-7890',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Everwin Maritime Services',
                'type' => 'company',
                'share_holder' => 0,
                'email' => 'contact@everwinmaritime.com',
                'phone' => '+63-2-8234-5678',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Pacific Cargo Solutions',
                'type' => 'company',
                'share_holder' => 1,
                'email' => 'info@pacificcargo.com',
                'phone' => '+63-2-8345-6789',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Luzon Freight Forwarders',
                'type' => 'company',
                'share_holder' => 1,
                'email' => 'logistics@luzonfreight.com',
                'phone' => '+63-2-8567-8901',
            ],
            [
                'first_name' => null,
                'last_name' => null,
                'company_name' => 'Mindanao Express Shipping',
                'type' => 'company',
                'share_holder' => 0,
                'email' => 'bookings@mindanaoexpress.com',
                'phone' => '+63-82-234-5678',
            ],
            
            // More individual customers
            [
                'first_name' => 'Carlos',
                'last_name' => 'Reyes',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'carlos.reyes@email.com',
                'phone' => '+63-919-876-5432',
            ],
            [
                'first_name' => 'Anna',
                'last_name' => 'Lim',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'anna.lim@email.com',
                'phone' => '+63-920-123-4567',
            ],
            [
                'first_name' => 'Jose',
                'last_name' => 'Tan',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 0,
                'email' => 'jose.tan@email.com',
                'phone' => '+63-921-987-6543',
            ],
            [
                'first_name' => 'Elena',
                'last_name' => 'Rodriguez',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'elena.rodriguez@email.com',
                'phone' => '+63-922-456-7890',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Castro',
                'company_name' => null,
                'type' => 'individual',
                'share_holder' => 1,
                'email' => 'miguel.castro@email.com',
                'phone' => '+63-923-321-6547',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
