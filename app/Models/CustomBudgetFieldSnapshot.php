<?php

namespace App\Models;

use App\Models\CustomBudgetSnapshot;
use Illuminate\Database\Eloquent\Model;

class CustomBudgetFieldSnapshot extends Model
{
    protected $fillable = ["custom_budget_snapshot_id", "field_name", "field_amount", "snapshot", "month"];

    public function customBudgetSnapshot()
    {
        return $this->belongsTo(CustomBudgetSnapshot::class);
    }

    public function customBudgetItemSnapshots()
    {
        return $this->hasMany(CustomBudgetItemSnapshot::class);
    }
}
