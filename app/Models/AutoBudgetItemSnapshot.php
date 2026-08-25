<?php

namespace App\Models;

use App\Models\AutoBudgetFieldSnapshot;
use Illuminate\Database\Eloquent\Model;

class AutoBudgetItemSnapshot extends Model
{
    protected $fillable = [
        'auto_budget_field_snapshot_id',
        'field_name',
        'item_name',
        'item_amount',
        'snapshot',
        'month',
    ];

    public function autoBudgetFieldSnapshot()
    {
        return $this->belongsTo(AutoBudgetFieldSnapshot::class);
    }
}
