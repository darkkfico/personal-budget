<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBudgetField extends Model
{
    protected $fillable = ['auto_budget_id', 'field_name', 'field_amount'];

    public function autoBudget()
    {
        return $this->belongsTo(AutoBudget::class);
    }

    public function autoBudgetItems()
    {
        return $this->hasMany(AutoBudgetItem::class);
    }
}
