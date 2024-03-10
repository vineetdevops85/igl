<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;

// Fetch data from database
$conn = mysqli_connect("localhost", "root", "", "igl_gas_bill");
$sql = "SELECT * FROM meter_readings";
$result = mysqli_query($conn, $sql);

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGL PNG Gas Bill</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>IGL PNG Gas Bill</h2>
    <table>
        <tr>
            <th>Previous Reading</th>
            <th>Current Reading</th>
            <th>Reading Date</th>
            <th>Total Units</th>
            <th>Bill Amount</th>
        </tr>';

while($row = mysqli_fetch_assoc($result)) {
    $html .= '<tr>
                <td>'. $row['previous_reading'] .'</td>
                <td>'. $row['current_reading'] .'</td>
                <td>'. $row['reading_date'] .'</td>
                <td>'. calculateUnits($row['previous_reading'], $row['current_reading']) .'</td>
                <td>'. calculateBill(calculateUnits($row['previous_reading'], $row['current_reading'])) .'</td>
            </tr>';
}

$html .= '</table>
</body>
</html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("igl_gas_bill.pdf", ["Attachment" => false]);
