<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'onboarding_step',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function customBudget()
    {
        return $this->belongsTo(CustomBudget::class);
    }

    public function autoBudget()
    {
        return $this->belongsTo(AutoBudget::class);
    }
}