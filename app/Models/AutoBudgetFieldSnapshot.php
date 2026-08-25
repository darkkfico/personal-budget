<?php

namespace App\Models;

use App\Models\AutoBudgetItemSnapshot;
use App\Models\AutoBudgetSnapshot;
use Illuminate\Database\Eloquent\Model;

class AutoBudgetFieldSnapshot extends Model
{
    protected $fillable = [
        'auto_budget_snapshot_id',
        'field_name',
        'field_amount',
        'snapshot',
        'month',
    ];

    public function autoBudgetSnapshot()
    {
        return $this->belongsTo(AutoBudgetSnapshot::class);
    }

    public function autoBudgetItemSnapshots()
    {
        return $this->hasMany(AutoBudgetItemSnapshot::class);
    }
}
