<?php

namespace App\Models;

use App\Models\AutoBudgetFieldSnapshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AutoBudgetSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'budget_amount',
        'currency',
        'reset_date',
        'snapshot',
        'month',
    ];

    public function autoBudgetFieldSnapshots()
    {
        return $this->hasMany(AutoBudgetFieldSnapshot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
