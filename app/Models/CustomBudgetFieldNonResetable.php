<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomBudgetFieldNonResetable extends Model
{
    protected $fillable = ['custom_budget_field_id'];

    public function customBudgetField()
    {
        return $this->belongsTo(CustomBudgetField::class);
    }
}
