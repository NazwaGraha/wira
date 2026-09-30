<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'position',
        'address',
        'birth_place',
        'birth_date',
        'gender',
        'class_grade',
        'email',
        'phone',
        'motto',
        'vision',
        'mission',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];
}
