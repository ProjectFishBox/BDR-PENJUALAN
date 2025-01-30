<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            width: 100%;
            margin-bottom: 2rem;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .table thead th {
            background-color: #343a40;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .table tbody td {
            padding: 10px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        h2 {
            color: #343a40;
            font-weight: bold;
            margin-bottom: 30px;
            border-bottom: 2px solid #343a40;
            padding-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Data Gabungkan</h2>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10%">No</th>
                    <th width="30%">Kode Barang</th>
                    <th width="40%">Merek</th>
                    <th width="20%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detailGabungkan as $index => $d)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $d->kode_barang }}</td>
                    <td>{{ $d->merek }}</td>
                    <td style="text-align: right;">{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($gabungkan->total_ball, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
