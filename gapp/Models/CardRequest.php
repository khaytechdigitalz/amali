<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardRequest extends Model
{
    use HasFactory;

    protected $fillable = ['account_number', 'address', 'account_name', 'status'];
}
