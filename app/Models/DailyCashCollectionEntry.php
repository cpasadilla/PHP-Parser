<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCashCollectionEntry extends Model
{
    protected $fillable = [
        'type',
        'entry_date',
        'dccr_number',
        'ar',
        'or',
        'customer_name',
        'customer_id',
        'gravel_sand',
        'chb',
        'other_income_cement',
        'other_income_df',
        'others',
        'interest',
        'vessel',
        'container_parcel',
        'payment_method',
        'status',
        'mv_everwin_star_1',
        'mv_everwin_star_2',
        'mv_everwin_star_3',
        'mv_everwin_star_4',
        'mv_everwin_star_5',
        'mv_everwin_star_1_other',
        'mv_everwin_star_2_other',
        'mv_everwin_star_3_other',
        'mv_everwin_star_4_other',
        'mv_everwin_star_5_other',
        'wharfage_payables',
        'total',
        'remark'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'gravel_sand' => 'decimal:2',
        'chb' => 'decimal:2',
        'other_income_cement' => 'decimal:2',
        'other_income_df' => 'decimal:2',
        'others' => 'decimal:2',
        'interest' => 'decimal:2',
        'mv_everwin_star_1' => 'decimal:2',
        'mv_everwin_star_2' => 'decimal:2',
        'mv_everwin_star_3' => 'decimal:2',
        'mv_everwin_star_4' => 'decimal:2',
        'mv_everwin_star_5' => 'decimal:2',
        'mv_everwin_star_1_other' => 'decimal:2',
        'mv_everwin_star_2_other' => 'decimal:2',
        'mv_everwin_star_3_other' => 'decimal:2',
        'mv_everwin_star_4_other' => 'decimal:2',
        'mv_everwin_star_5_other' => 'decimal:2',
        'wharfage_payables' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
