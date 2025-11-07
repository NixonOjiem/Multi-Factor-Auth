<?php

namespace App\Models;

// 1. IMPORT THE CORRECT AUTHENTICATABLE
use MongoDB\Laravel\Auth\User as Authenticatable; // <-- Change this line

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

// 2. THIS "Authenticatable" IS NOW THE MONGODB VERSION
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 3. ADD THESE TWO LINES
    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // This will work
        ];
    }
}
