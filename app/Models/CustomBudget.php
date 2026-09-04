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
        'reset_carry_answered_on',
        'pending_reset_leftover',
        'reset_leftover_captured_on',
    ];

    protected function casts(): array
    {
        return [
            'reset_carry_answered_on' => 'date',
            'reset_leftover_captured_on' => 'date',
            'pending_reset_leftover' => 'float',
        ];
    }

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
