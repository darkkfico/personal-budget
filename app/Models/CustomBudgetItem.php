<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomBudgetItem extends Model
{
    protected $fillable = ['custom_budget_field_id', 'item_name', 'item_amount', 'field_name'];

    public function customBudgetField()
    {
        return $this->belongsTo(CustomBudgetField::class);
    }
}
