<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: #343a40;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .total-row {
            font-weight: bold;
            background-color: #f4f4f4;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Merek</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mergedDetails as $index => $d)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $d['kode_barang'] }}</td>
                <td>{{ $d['merek'] }}</td>
                <td>{{ number_format($d['jumlah'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>{{ number_format($gabungkan->total_ball, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
