<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reduction extends Model
{
    use HasFactory;

    protected $table = 'reductions';
    protected $primaryKey = 'REDUC_ID';
    public $timestamps = false;

    protected $fillable = [
        'REDUC_CODE',
        'REDUC_DESC',
        'REDUC_VALUE',
    ];
}