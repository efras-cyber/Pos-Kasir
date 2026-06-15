<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .print-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            justify-content: center;
        }
        .barcode-item {
            border: 1px dashed #ccc;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            page-break-inside: avoid;
            background: #fff;
        }
        .barcode-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .barcode-price {
            font-size: 13px;
            color: #333;
            margin-bottom: 10px;
        }
        .barcode-img {
            max-width: 100%;
            height: 50px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .barcode-item {
                border: 1px solid #000;
            }
        }
        .print-btn-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-print {
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-print:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>

    <div class="no-print print-btn-container">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Barcode Sekarang</button>
    </div>

    <div class="print-container">
        @foreach($products as $product)
        <div class="barcode-item">
            <div class="barcode-title">{{ $product->title }}</div>
            <div class="barcode-price">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
            <img class="barcode-img" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $product->barcode }}&scale=2&height=10&includetext" alt="{{ $product->barcode }}">
        </div>
        @endforeach
    </div>

</body>
</html>
