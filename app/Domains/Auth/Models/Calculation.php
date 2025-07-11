<?php

namespace App\Domains\Auth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;
    protected $table = 'calculations';
    protected $fillable = ['number1', 'number2', 'operation', 'result'];
    protected $casts = [
        'number1' => 'integer',
        'number2' => 'integer',
        'result' => 'float',
    ];

}

