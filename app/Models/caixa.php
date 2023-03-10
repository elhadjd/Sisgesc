<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'company_id',
        'password'
    ];
}
