<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoBudget extends Model
{
    protected $fillable = [
        'user_id',
        'budget_amount',
        'currency',
        'reset_date',
        'reset_carry_answered_on',
    ];

    protected function casts(): array
    {
        return [
            'reset_carry_answered_on' => 'date',
        ];
    }

    public function autoBudgetFields()
    {
        return $this->hasMany(AutoBudgetField::class);
    }

    public function autoBudgetItems()
    {
        return $this->hasManyThrough(AutoBudgetItem::class, AutoBudgetField::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
