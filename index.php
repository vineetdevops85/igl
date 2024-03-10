<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "igl_gas_bill");

// Function to calculate total units
function calculateUnits($prevReading, $currReading) {
    return $currReading - $prevReading;
}

// Function to calculate bill amount
function calculateBill($units) {
    $rate_per_unit = 48.46;
    $bill = $units * $rate_per_unit;
    return round($bill, 2); // Round to two decimal places
}

// Handle delete operation
if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    
    // Perform delete operation in the database
    $sql = "DELETE FROM meter_readings WHERE id = '$delete_id'";
    mysqli_query($conn, $sql);
    
    // Refresh the page to reflect changes
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Form submission
if(isset($_POST['submit'])) {
    $prev_reading = $_POST['prev_reading'];
    $curr_reading = $_POST['curr_reading'];
    $reading_date = $_POST['reading_date'];

    // Calculate total units
    $total_units = calculateUnits($prev_reading, $curr_reading);

    // Insert data into database
    $sql = "INSERT INTO meter_readings (previous_reading, current_reading, reading_date, total_units) VALUES ('$prev_reading', '$curr_reading', '$reading_date', '$total_units')";
    mysqli_query($conn, $sql);
}

// Fetch data from database
$sql = "SELECT * FROM meter_readings";
$result = mysqli_query($conn, $sql);


// Pagination
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch data from database with pagination
$sql = "SELECT * FROM meter_readings LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Count total number of records
$total_records = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM meter_readings"));
$total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGL PNG Gas Bill Portal</title>
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
        
        form {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        
        input[type="number"],
        input[type="date"],
        button {
            padding: 10px;
            width: calc(100% - 20px);
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 5px;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #45a049;
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
        
        .footer {
            text-align: center;
            margin-top: 20px;
        }

    /* Pagination styles */
.pagination {
    margin-top: 20px;
    text-align: center;
}

.page-link {
    display: inline-block;
    padding: 5px 10px;
    margin: 0 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.3s, color 0.3s;
}

.page-link:hover {
    background-color: #f2f2f2;
}

.current-page {
    display: inline-block;
    padding: 5px 10px;
    margin: 0 5px;
    background-color: #007bff;
    color: #fff;
    border-radius: 3px;
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Enter Meter Readings</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="prev_reading">Previous Meter Reading:</label>
                <input type="number" step="0.001" id="prev_reading" name="prev_reading" required>
            </div>
            <div class="form-group">
                <label for="curr_reading">Current Meter Reading:</label>
                <input type="number" step="0.001" id="curr_reading" name="curr_reading" required>
            </div>
            <div class="form-group">
                <label for="reading_date">Meter Reading Date:</label>
                <input type="date" id="reading_date" name="reading_date" required>
            </div>
            <button type="submit" name="submit">Submit</button>
        </form>
        <hr>
        <h2>Bill Details</h2>
        <!-- Table displaying bill details -->
        <table>
            <tr>
                <th>Previous Reading</th>
                <th>Current Reading</th>
                <th>Reading Date</th>
                <th>Total Units</th>
                <th>Bill Amount</th>
                <th>Actions</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $prev_reading = $row['previous_reading'];
                    $curr_reading = $row['current_reading'];
                    $reading_date = $row['reading_date'];
                    $total_units = $row['total_units'];
                    $bill_amount = number_format(calculateBill($total_units), 2);
                ?>
                <tr>
                    <td><?php echo $prev_reading; ?></td>
                    <td><?php echo $curr_reading; ?></td>
                    <td><?php echo $reading_date; ?></td>
                    <td><?php echo $total_units; ?></td>
                    <td><?php echo $bill_amount; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this row?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <!-- Pagination links -->
<div class="pagination">
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
        <?php if($i == $page): ?>
            <span class="current-page"><?php echo $i; ?></span>
        <?php else: ?>
            <a href="?page=<?php echo $i; ?>" class="page-link"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>
    </div>
</body>
</html>
