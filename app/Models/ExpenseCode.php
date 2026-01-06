<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCode extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category',
    ];
}
