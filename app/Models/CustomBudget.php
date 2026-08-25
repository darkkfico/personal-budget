<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomBudget extends Model
{
    protected $fillable = [
        'user_id',
        'budget_amount',
        'currency',
        'reset_date',
    ];

    public function customBudgetFields()
    {
        return $this->hasMany(CustomBudgetField::class);
    }

    public function customBudgetItems()
    {
        return $this->hasManyThrough(CustomBudgetItem::class, CustomBudgetField::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
