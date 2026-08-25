<?php

namespace App\Models;

use App\Models\CustomBudgetFieldSnapshot;
use Illuminate\Database\Eloquent\Model;

class CustomBudgetItemSnapshot extends Model
{
    protected $fillable = [
        'custom_budget_field_snapshot_id',
        'field_name',
        'item_name',
        'item_amount',
        'snapshot',
        'month',
    ];

    public function customBudgetFieldSnapshot()
    {
        return $this->belongsTo(CustomBudgetFieldSnapshot::class);
    }
}
