<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Ledger PDF {{ $customer->name }}</title>
    <style>
        table tr th,
        table tr td {
            padding: 4px;
            font-size: 12px;
        }

        h3 {
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h3 class="text-center">Customer Ledger for {{ $customer->name }}</h3>
    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Sl</th>
                <th>Date</th>
                <th>Particular</th>
                <th>Total Amount</th>
                <th>Paid Amount</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ledger as $key => $entry)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $entry->date ?? null }}</td>
                    <td>
                        @if ($entry->invoice_id == null)
                           Due Payment ({{ $entry->paid_status ?? null }})
                        @else
                            Sales ({{ $entry->paid_status ?? null }})
                        @endif
                    </td>
                    <td>{{ $entry->total_amount ?? '0.00' }}</td>
                    <td>{{ $entry->paid_amount ?? '0.00' }}</td>
                    <td>
                        {{ $entry->balance ?? '0.00' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
