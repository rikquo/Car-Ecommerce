<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['user_role'] !== 'admin') {
    header('Location: /pages/login.php');
    exit();
}
require_once '../../config/dbcon.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_type = $_POST['report_type'] ?? '';
    $format = $_POST['format'] ?? 'pdf';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $include_charts = isset($_POST['include_charts']);
    $include_details = isset($_POST['include_details']);
    $include_commission = isset($_POST['include_commission']);
    if (empty($report_type)) {
        $_SESSION['error'] = 'Report type is required.';
        header('Location: /admin/sales.php');
        exit();
    }
    try {
        $date_filter = "";
        $params = [];
        switch ($report_type) {
            case 'daily':
                $date_filter = "AND DATE(o.order_date) = CURDATE()";
                break;
            case 'weekly':
                $date_filter = "AND YEARWEEK(o.order_date) = YEARWEEK(CURDATE())";
                break;
            case 'monthly':
                $date_filter = "AND MONTH(o.order_date) = MONTH(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
                break;
            case 'quarterly':
                $date_filter = "AND QUARTER(o.order_date) = QUARTER(CURDATE()) AND YEAR(o.order_date) = YEAR(CURDATE())";
                break;
            case 'yearly':
                $date_filter = "AND YEAR(o.order_date) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (!empty($start_date) && !empty($end_date)) {
                    $date_filter = "AND DATE(o.order_date) BETWEEN ? AND ?";
                    $params = [$start_date, $end_date];
                }
                break;
        }

        // Get sales data
        $sql = "SELECT o.order_id, o.user_id, o.total_amount, o.order_date,
                       u.username, u.email,
                       od.car_id, od.price_at_purchase,
                       c.name as car_name, c.series_id,
                       cs.name as series_name
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.user_id 
                LEFT JOIN order_details od ON o.order_id = od.order_id
                LEFT JOIN cars c ON od.car_id = c.car_id
                LEFT JOIN car_series cs ON c.series_id = cs.series_id
                WHERE o.status = 'Completed' $date_filter
                ORDER BY o.order_date DESC";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        switch ($format) {
            case 'csv':
                generateCSVReport($sales_data, $report_type);
                break;
            case 'excel':
                generateExcelReport($sales_data, $report_type);
                break;
        }

        $_SESSION['message'] = 'Sales report generated successfully.';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
}

header('Location: /admin/sales.php');
exit();

function generateCSVReport($data, $report_type)
{
    $filename = "sales_report_" . $report_type . "_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, ['Sale ID', 'Customer', 'Email', 'Vehicle', 'Series', 'Amount', 'Commission', 'Date']);

    foreach ($data as $row) {
        $commission = $row['price_at_purchase'] * 0.05;
        fputcsv($output, [
            'SALE-' . str_pad($row['order_id'], 3, '0', STR_PAD_LEFT),
            $row['username'],
            $row['email'],
            $row['car_name'],
            $row['series_name'],
            '$' . number_format($row['price_at_purchase'], 2),
            '$' . number_format($commission, 2),
            date('M d, Y', strtotime($row['order_date']))
        ]);
    }

    fclose($output);
    exit();
}

function generateExcelReport($data, $report_type)
{
    // For Excel generation
    $_SESSION['message'] = 'Excel report generation feature coming soon.';
}
