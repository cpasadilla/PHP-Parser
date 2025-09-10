<?php

namespace App\Exports;

use App\Models\Crew;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CrewExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $crews;

    public function __construct(Collection $crews)
    {
        $this->crews = $crews;
    }

    public function collection()
    {
        return $this->crews->map(function ($crew, $index) {
            // Create department display based on division and department
            $departmentDisplay = ucfirst(str_replace('_', ' ', $crew->division ?? ''));
            if ($crew->department) {
                $departmentDisplay .= ' - ' . ucfirst($crew->department);
            }
            
            return [
                'no' => $index + 1,
                'employee_id' => $crew->employee_id,
                'full_name' => $crew->full_name,
                'position' => $crew->position,
                'department' => $departmentDisplay,
                'ship' => $crew->ship ? "MV EVERWIN STAR {$crew->ship->ship_number}" : 'Office/Shore',
                'contact_number' => $crew->contact_number,
                'email' => $crew->email,
                'address' => $crew->address,
                'date_hired' => $crew->date_hired ? $crew->date_hired->format('Y-m-d') : '',
                'status' => ucfirst($crew->employment_status ?? $crew->status ?? ''),
                'emergency_contact' => $crew->emergency_contact_name,
                'emergency_phone' => $crew->emergency_contact_phone
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Employee ID',
            'Full Name',
            'Position',
            'Department',
            'Ship Assignment',
            'Contact Number',
            'Email',
            'Address',
            'Date Hired',
            'Status',
            'Emergency Contact',
            'Emergency Phone'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Header styles
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Data rows
        if ($highestRow > 1) {
            $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);

            // Center align certain columns
            $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Auto-adjust row heights
        for ($row = 1; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No.
            'B' => 12,  // Employee ID
            'C' => 25,  // Full Name
            'D' => 20,  // Position
            'E' => 12,  // Department
            'F' => 18,  // Ship Assignment
            'G' => 15,  // Contact Number
            'H' => 25,  // Email
            'I' => 30,  // Address
            'J' => 12,  // Date Hired
            'K' => 10,  // Status
            'L' => 20,  // Emergency Contact
            'M' => 15   // Emergency Phone
        ];
    }

    public function title(): string
    {
        return 'Crew List';
    }
}
