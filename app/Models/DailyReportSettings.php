<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportSettings extends Model
{
    protected $fillable = [
        'report_date',
        'report_type',
        'dccr_number',
        'add_collection',
        'collected_by_name',
        'collected_by_title'
    ];

    protected $casts = [
        'report_date' => 'date',
        'add_collection' => 'decimal:2'
    ];
}
