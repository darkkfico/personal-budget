<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBudgetItem extends Model
{
    protected $fillable = ['auto_budget_field_id', 'item_name', 'field_name', 'item_amount'];

    public function autoBudgetField()
    {
        return $this->belongsTo(AutoBudgetField::class);
    }
}
