<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'email',
        'days',
        'price',
        'captured_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'captured_amount' => 'decimal:2',
        ];
    }
}
