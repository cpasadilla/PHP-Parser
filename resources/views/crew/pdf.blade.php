<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .department-group {
            background-color: #e6f3ff;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            font-size: 11px;
        }
        .summary table {
            width: 300px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on: {{ date('F d, Y H:i:s') }}</p>
        <p>Total Crew Members: {{ $crews->count() }}</p>
    </div>

    @if($crews->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 12%;">Employee ID</th>
                    <th style="width: 25%;">Full Name</th>
                    <th style="width: 15%;">Position</th>
                    <th style="width: 15%;">Department</th>
                    <th style="width: 12%;">Ship</th>
                    <th style="width: 13%;">Contact</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentDepartment = '';
                    $counter = 1;
                @endphp
                @foreach($crews->groupBy(function($crew) { return $crew->division . '_' . $crew->department; }) as $groupKey => $departmentCrews)
                    @php
                        $firstCrew = $departmentCrews->first();
                        $divisionName = ucfirst(str_replace('_', ' ', $firstCrew->division ?? ''));
                        $departmentName = ucfirst($firstCrew->department ?? '');
                        $displayName = $divisionName . ($departmentName ? ' - ' . $departmentName : '');
                    @endphp
                    <tr class="department-group">
                        <td colspan="7" style="font-weight: bold; background-color: #e6f3ff;">
                            {{ strtoupper($displayName) }} ({{ $departmentCrews->count() }} members)
                        </td>
                    </tr>
                    @foreach($departmentCrews as $crew)
                        <tr>
                            <td class="text-center">{{ $counter++ }}</td>
                            <td>{{ $crew->employee_id }}</td>
                            <td>{{ $crew->full_name }}</td>
                            <td>{{ $crew->position }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $crew->division ?? '')) . ($crew->department ? ' - ' . ucfirst($crew->department) : '') }}</td>
                            <td class="text-center">
                                @if($crew->ship)
                                    MV EVERWIN STAR {{ $crew->ship->ship_number }}
                                @else
                                    Office/Shore
                                @endif
                            </td>
                            <td>{{ $crew->contact_number }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary">
            <h3>Summary by Department</h3>
            <table>
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($crews->groupBy(function($crew) { return $crew->division . '_' . $crew->department; }) as $groupKey => $departmentCrews)
                        @php
                            $firstCrew = $departmentCrews->first();
                            $divisionName = ucfirst(str_replace('_', ' ', $firstCrew->division ?? ''));
                            $departmentName = ucfirst($firstCrew->department ?? '');
                            $displayName = $divisionName . ($departmentName ? ' - ' . $departmentName : '');
                        @endphp
                        <tr>
                            <td>{{ $displayName }}</td>
                            <td class="text-center">{{ $departmentCrews->count() }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background-color: #f5f5f5;">
                        <td>Total</td>
                        <td class="text-center">{{ $crews->count() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($crews->groupBy('ship_id')->count() > 1)
            <div class="summary">
                <h3>Summary by Ship</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Ship</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($crews->groupBy('ship_id') as $shipId => $shipCrews)
                            <tr>
                                <td>
                                    @if($shipId)
                                        MV EVERWIN STAR {{ $shipCrews->first()->ship->ship_number }}
                                    @else
                                        Office/Shore Personnel
                                    @endif
                                </td>
                                <td class="text-center">{{ $shipCrews->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else
        <div class="no-data">
            <p>No crew members found matching the selected criteria.</p>
        </div>
    @endif
 
    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>This is a computer-generated document. No signature required.</p>
        <p>St. Francis Xavier Star Shipping Inc.</p>
    </div>
</body>
</html>
