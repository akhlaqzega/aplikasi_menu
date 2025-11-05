<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 40px;
        }

        h1 {
            font-size: 20px;
            color: #4338CA;
            border-bottom: 2px solid #EEE;
            padding-bottom: 5px;
        }

        h2 {
            font-size: 14px;
            margin-top: 20px;
            color: #333;
        }

        .header-info {
            margin-bottom: 20px;
            font-size: 11px;
        }

        .header-info strong {
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #DDD;
            padding: 8px 6px;
            text-align: left;
        }

        th {
            background-color: #F0F4FF;
            color: #4338CA;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-completed {
            color: green;
            font-weight: bold;
        }

        .status-canceled {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Laporan Detail Transaksi CafeKU</h1>

    <div class="header-info">
        Tanggal Dicetak: {{ now()->format('d F Y H:i') }}<br>
        Periode Laporan:
        <strong>{{ $startDate->format('d F Y') }}</strong> hingga
        <strong>{{ $endDate->format('d F Y') }}</strong>
    </div>

    <h2>Ringkasan Keuangan</h2>
    <div class="header-info">
        Total Pendapatan Bersih (Completed):
        <strong style="font-size: 16px; color: green;">
            Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
        </strong>
    </div>

    <h2>Detail Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Pesanan</th>
                <th>Waktu Pesanan</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th>Pelanggan</th>
                <th>Meja</th>
                <th class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>#{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('d M H:i') }}</td>
                    <td>
                        <span
                            class="{{ $order->status === 'completed' ? 'status-completed' : ($order->status === 'canceled' ? 'status-canceled' : '') }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td>{{ $order->payment->method ?? 'Tunai' }}</td>
                    <td>{{ $order->customer_name ?? 'Anonim' }} </td>
                    <td>{{ $order->table->name ?? 'Dine In' }}</td>
                    <td class="text-right">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada transaksi yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
