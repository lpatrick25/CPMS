<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Items Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Stock Items Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Item Description</th>
                <th>Unit</th>
                <th>Initial Quantity</th>
                <th>Remaining Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->remaining_quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
