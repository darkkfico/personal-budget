<?php

namespace App\Models;

use App\Models\CustomBudgetFieldSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CustomBudgetSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'budget_amount',
        'currency',
        'reset_date',
        'snapshot',
        'month',
    ];

    public function customBudgetFieldSnapshots()
    {
        return $this->hasMany(CustomBudgetFieldSnapshot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
