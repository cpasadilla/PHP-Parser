<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Crew;
use App\Models\Ship;
use App\Models\CrewLeave;

class CrewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get ships
        $ships = Ship::all();
        
        if ($ships->isEmpty()) {
            // Create sample ships if they don't exist
            $ships = collect([
                Ship::create(['ship_number' => 'I']),
                Ship::create(['ship_number' => 'II']),
                Ship::create(['ship_number' => 'III']),
                Ship::create(['ship_number' => 'IV']),
                Ship::create(['ship_number' => 'V']),
            ]);
        }

        $sampleCrews = [
            [
                'employee_id' => 'EMP-001',
                'first_name' => 'Juan',
                'last_name' => 'Cruz',
                'middle_name' => 'Santos',
                'position' => 'Captain',
                'department' => 'ship_crew',
                'ship_id' => $ships->random()->id,
                'hire_date' => '2020-01-15',
                'employment_status' => 'active',
                'phone' => '+639123456789',
                'email' => 'juan.cruz@everwin.com',
                'address' => '123 Main St, Manila, Philippines',
                'emergency_contact_name' => 'Maria Cruz',
                'emergency_contact_phone' => '+639987654321',
                'seaman_book_number' => 'SB-2020-001',
                'passport_number' => 'P123456789',
                'basic_safety_training' => '2024-12-31',
                'medical_certificate' => '2024-10-15',
                'contract_expiry' => '2025-01-15',
                'notes' => 'Experienced captain with 15 years of service.'
            ],
            [
                'employee_id' => 'EMP-002',
                'first_name' => 'Pedro',
                'last_name' => 'Garcia',
                'middle_name' => 'Lopez',
                'position' => 'Chief Engineer',
                'department' => 'ship_crew',
                'ship_id' => $ships->random()->id,
                'hire_date' => '2019-06-01',
                'employment_status' => 'active',
                'phone' => '+639111222333',
                'email' => 'pedro.garcia@everwin.com',
                'address' => '456 Port Ave, Cebu, Philippines',
                'emergency_contact_name' => 'Ana Garcia',
                'emergency_contact_phone' => '+639444555666',
                'seaman_book_number' => 'SB-2019-002',
                'passport_number' => 'P987654321',
                'basic_safety_training' => '2024-11-30',
                'medical_certificate' => '2024-09-20',
                'contract_expiry' => '2024-12-01',
                'notes' => 'Certified marine engineer.'
            ],
            [
                'employee_id' => 'EMP-003',
                'first_name' => 'Maria',
                'last_name' => 'Reyes',
                'middle_name' => 'Santos',
                'position' => 'HR Manager',
                'department' => 'office_staff',
                'ship_id' => null,
                'hire_date' => '2021-03-10',
                'employment_status' => 'active',
                'phone' => '+639777888999',
                'email' => 'maria.reyes@everwin.com',
                'address' => '789 Business St, Makati, Philippines',
                'emergency_contact_name' => 'Jose Reyes',
                'emergency_contact_phone' => '+639333444555',
                'notes' => 'Handles all HR operations and crew management.'
            ],
            [
                'employee_id' => 'EMP-004',
                'first_name' => 'Roberto',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Manila',
                'position' => 'Dock Worker',
                'department' => 'laborer',
                'ship_id' => null,
                'hire_date' => '2022-08-15',
                'employment_status' => 'active',
                'phone' => '+639222333444',
                'address' => '321 Pier St, Manila, Philippines',
                'emergency_contact_name' => 'Carmen Dela Cruz',
                'emergency_contact_phone' => '+639666777888',
                'notes' => 'Experienced in cargo handling and dock operations.'
            ],
            [
                'employee_id' => 'EMP-005',
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'middle_name' => 'Torres',
                'position' => 'Able Seaman',
                'department' => 'ship_crew',
                'ship_id' => $ships->random()->id,
                'hire_date' => '2023-02-20',
                'employment_status' => 'active',
                'phone' => '+639555666777',
                'email' => 'carlos.mendoza@everwin.com',
                'address' => '654 Coastal Rd, Bataan, Philippines',
                'emergency_contact_name' => 'Elena Mendoza',
                'emergency_contact_phone' => '+639888999000',
                'seaman_book_number' => 'SB-2023-003',
                'passport_number' => 'P456789123',
                'basic_safety_training' => '2025-02-20',
                'medical_certificate' => '2024-08-20',
                'contract_expiry' => '2025-02-20',
                'notes' => 'Recently promoted to Able Seaman.'
            ]
        ];

        foreach ($sampleCrews as $crewData) {
            $crew = Crew::create($crewData);
            
            // Create default leave credits for each crew member
            $currentYear = date('Y');
            
            CrewLeave::create([
                'crew_id' => $crew->id,
                'leave_type' => 'vacation',
                'credits' => 15,
                'year' => $currentYear,
                'notes' => 'Annual vacation leave allocation'
            ]);

            CrewLeave::create([
                'crew_id' => $crew->id,
                'leave_type' => 'sick',
                'credits' => 7,
                'year' => $currentYear,
                'notes' => 'Annual sick leave allocation'
            ]);
        }
    }
}
