<!-- bill_details.php -->
<?php
    // Define the calculateBill() function
    function calculateBill($units) {
        $rate_per_unit = 48.46;
        return round($units * $rate_per_unit, 2);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bill Details</h2>
        <table>
            <tr>
                <th>Previous Reading</th>
                <th>Current Reading</th>
                <th>Reading Date</th>
                <th>Total Units</th>
                <th>Bill Amount</th>
            </tr>
            <?php
                // Database connection
                $conn = mysqli_connect("localhost", "root", "", "igl_gas_bill");

                // Fetch data from database
                $sql = "SELECT * FROM meter_readings";
                $result = mysqli_query($conn, $sql);

                while($row = mysqli_fetch_assoc($result)) {
                    $prev_reading = $row['previous_reading'];
                    $curr_reading = $row['current_reading'];
                    $reading_date = $row['reading_date'];
                    $total_units = $row['total_units'];
                    $bill_amount = number_format(calculateBill($total_units), 2);
                    
                    echo "<tr>";
                    echo "<td>$prev_reading</td>";
                    echo "<td>$curr_reading</td>";
                    echo "<td>$reading_date</td>";
                    echo "<td>$total_units</td>";
                    echo "<td>$bill_amount</td>";
                    echo "</tr>";
                }
            ?>
        </table>
        <a href="index.php"><button>Go to Home</button></a>
    </div>
    </div>
</body>
</html>
