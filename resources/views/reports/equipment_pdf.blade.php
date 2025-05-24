<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Equipment Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Equipment Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Equipment Name</th>
                <th>Initial Quantity</th>
                <th>Remaining Quantity</th>
                <th>Available</th>
                <th>Borrowed</th>
                <th>Under Maintenance</th>
                <th>Retired</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($equipment as $index => $equip)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $equip->name }}</td>
                    <td>{{ $equip->quantity }}</td>
                    <td>{{ $equip->remaining_quantity }}</td>
                    <td>{{ $equip->serials->where('status', 'Available')->count() }}</td>
                    <td>{{ $equip->serials->where('status', 'Borrowed')->count() }}</td>
                    <td>{{ $equip->serials->where('status', 'Under Maintenance')->count() }}</td>
                    <td>{{ $equip->serials->where('status', 'Retired')->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
