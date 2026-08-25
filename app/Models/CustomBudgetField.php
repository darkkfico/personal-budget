<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomBudgetField extends Model
{
    protected $fillable = ['custom_budget_id', 'field_name', 'field_amount'];

    public function customBudget()
    {
        return $this->belongsTo(CustomBudget::class);
    }

    public function customBudgetItems()
    {
        return $this->hasMany(CustomBudgetItem::class);
    }
}
