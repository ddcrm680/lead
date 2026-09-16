<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'setting_group',
    'key',
    'value',
    'type',
])]
class Setting extends Model
{
    use HasFactory;
}