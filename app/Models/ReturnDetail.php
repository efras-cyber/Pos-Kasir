<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnDetail extends Model
{

    protected function casts(): array
    {
        return [
            'qty'      => 'integer',
            'price'    => 'integer',
            'subtotal' => 'integer',
            'restock'  => 'boolean',
        ];
    }

    protected $fillable = [
        'return_transaction_id',
        'product_id',
        'qty',
        'price',
        'subtotal',
        'restock',
    ];

    public function returnTransaction(): BelongsTo
    {
        return $this->belongsTo(ReturnTransaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
