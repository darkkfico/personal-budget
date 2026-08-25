<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'item_name',
        'item_amount',
        'user_id',
        'field_name',
    ];

    public function customBudget()
    {
        return $this->belongsTo(CustomBudget::class);
    }

    public function autoBudget()
    {
        return $this->belongsTo(AutoBudget::class);
    }
}
