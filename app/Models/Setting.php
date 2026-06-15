<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function storeSettings(): array
    {
        $settings = static::query()
            ->where('group', 'store')
            ->pluck('value', 'key');

        $logo = $settings->get('store.logo');

        $receiptPaperSize = (string) $settings->get('receipt.paper_size');

        return [
            'name' => $settings->get('store.name') ?: 'ZEN POS',
            'address' => $settings->get('store.address') ?: 'Jl. Contoh No. 123',
            'phone' => $settings->get('store.phone') ?: '0812-3456-7890',
            'email' => $settings->get('store.email'),
            'logo' => $logo,
            'logo_url' => $logo ? asset('/storage/settings/'.$logo) : null,
            'receipt_paper_size' => in_array($receiptPaperSize, ['58', '80'], true)
                ? $receiptPaperSize
                : '58',
        ];
    }
}
