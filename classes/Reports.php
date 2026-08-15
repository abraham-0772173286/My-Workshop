<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/app.php';
require_once __DIR__ . '/../configs/database.php';

workshop_session_start();

header('Content-Type: application/json; charset=utf-8');

// Only admins and owners can access reports
if (!workshop_has_role('admin', 'owner')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Access denied. Admin or Owner privileges required.']);
    exit;
}

$f = (string) ($_GET['f'] ?? '');

try {
    $pdo = get_database_connection();
    
    switch ($f) {
        case 'summary_stats':
            getSummaryStats($pdo);
            break;
            
        case 'revenue_by_day':
            getRevenueByDay($pdo);
            break;
            
        case 'vehicles_worked_on':
            getVehiclesWorkedOn($pdo);
            break;
            
        case 'repair_categories':
            getRepairCategories($pdo);
            break;
            
        case 'customer_rankings':
            getCustomerRankings($pdo);
            break;
            
        case 'monthly_trends':
            getMonthlyTrends($pdo);
            break;
            
        case 'export_data':
            exportReportData($pdo);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid function specified.']);
    }
    
} catch (Exception $e) {
    error_log("Reports error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Internal server error.']);
}

function getSummaryStats(PDO $pdo): void {
    $period = $_GET['period'] ?? 'today';
    
    // Define date ranges
    $dateCondition = match($period) {
        'today' => "DATE(rj.created_at) = CURDATE()",
        'week' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)",
        'month' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
        'all' => "1=1",
        default => "DATE(rj.created_at) = CURDATE()"
    };
    
    // Total completed orders
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_orders
        FROM repair_jobs rj
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
    ");
    $stmt->execute();
    $totalOrders = $stmt->fetchColumn();
    
    // Total revenue
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as total_revenue
        FROM repair_jobs rj
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
    ");
    $stmt->execute();
    $totalRevenue = $stmt->fetchColumn();
    
    // Average order value
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    
    // Items sold (repair jobs completed)
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT rj.vehicle_id) as items_sold
        FROM repair_jobs rj
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
    ");
    $stmt->execute();
    $itemsSold = $stmt->fetchColumn();
    
    echo json_encode([
        'status' => 'success',
        'stats' => [
            'total_orders' => (int) $totalOrders,
            'total_revenue' => (float) $totalRevenue,
            'avg_order_value' => (float) $avgOrderValue,
            'items_sold' => (int) $itemsSold,
            'period' => $period
        ]
    ]);
}

function getRevenueByDay(PDO $pdo): void {
    $period = $_GET['period'] ?? 'week';
    
    $days = match($period) {
        'week' => 7,
        'month' => 30,
        default => 7
    };
    
    $stmt = $pdo->prepare("
        SELECT 
            DATE(rj.created_at) as date,
            COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as revenue,
            COUNT(*) as orders
        FROM repair_jobs rj
        WHERE rj.status = 'REPAIR DONE' 
        AND rj.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE(rj.created_at)
        ORDER BY date ASC
    ");
    $stmt->execute([$days]);
    $data = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
}

function getVehiclesWorkedOn(PDO $pdo): void {
    $period = $_GET['period'] ?? 'today';
    $limit = (int) ($_GET['limit'] ?? 50);
    
    $dateCondition = match($period) {
        'today' => "DATE(rj.created_at) = CURDATE()",
        'week' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)",
        'month' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
        'all' => "1=1",
        default => "DATE(rj.created_at) = CURDATE()"
    };
    
    $stmt = $pdo->prepare("
        SELECT 
            v.plate_number,
            COALESCE(v.model, 'Unknown Model') as model,
            c.fullname as customer_name,
            c.contact as customer_contact,
            rj.repair_type as category,
            COUNT(rj.id) as jobs_completed,
            COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as total_revenue,
            MAX(rj.created_at) as last_service_date
        FROM repair_jobs rj
        INNER JOIN vehicles v ON v.id = rj.vehicle_id
        INNER JOIN customers c ON c.id = v.customer_id
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
        GROUP BY v.id, v.plate_number, v.model, c.fullname, c.contact, rj.repair_type
        ORDER BY total_revenue DESC, jobs_completed DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    $vehicles = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'vehicles' => $vehicles,
        'period' => $period
    ]);
}

function getRepairCategories(PDO $pdo): void {
    $period = $_GET['period'] ?? 'month';
    
    $dateCondition = match($period) {
        'today' => "DATE(rj.created_at) = CURDATE()",
        'week' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)",
        'month' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
        'all' => "1=1",
        default => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"
    };
    
    $stmt = $pdo->prepare("
        SELECT 
            rj.repair_type as category,
            COUNT(*) as quantity_sold,
            COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as revenue
        FROM repair_jobs rj
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
        GROUP BY rj.repair_type
        ORDER BY revenue DESC, quantity_sold DESC
        LIMIT 20
    ");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'categories' => $categories
    ]);
}

function getCustomerRankings(PDO $pdo): void {
    $period = $_GET['period'] ?? 'all';
    
    $dateCondition = match($period) {
        'today' => "DATE(rj.created_at) = CURDATE()",
        'week' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)",
        'month' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
        'all' => "1=1",
        default => "1=1"
    };
    
    $stmt = $pdo->prepare("
        SELECT 
            c.fullname as customer_name,
            c.contact as customer_contact,
            COUNT(DISTINCT v.id) as vehicles_count,
            COUNT(rj.id) as total_jobs,
            COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as total_spent,
            MAX(rj.created_at) as last_visit
        FROM customers c
        INNER JOIN vehicles v ON v.customer_id = c.id
        INNER JOIN repair_jobs rj ON rj.vehicle_id = v.id
        WHERE rj.status = 'REPAIR DONE' AND $dateCondition
        GROUP BY c.id, c.fullname, c.contact
        ORDER BY total_spent DESC, total_jobs DESC
        LIMIT 50
    ");
    $stmt->execute();
    $customers = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'customers' => $customers
    ]);
}

function getMonthlyTrends(PDO $pdo): void {
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(rj.created_at, '%Y-%m') as month,
            COUNT(*) as total_jobs,
            COUNT(CASE WHEN rj.status = 'REPAIR DONE' THEN 1 END) as completed_jobs,
            COALESCE(SUM(CASE WHEN rj.status = 'REPAIR DONE' THEN rj.parts_cost + rj.labour_cost END), 0) as revenue
        FROM repair_jobs rj
        WHERE rj.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(rj.created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    $trends = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'trends' => $trends
    ]);
}

function exportReportData(PDO $pdo): void {
    $type = $_GET['type'] ?? 'vehicles';
    $period = $_GET['period'] ?? 'month';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="workshop_report_' . $type . '_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    switch ($type) {
        case 'vehicles':
            fputcsv($output, ['Plate Number', 'Model', 'Customer', 'Contact', 'Category', 'Jobs Completed', 'Revenue (UGX)', 'Last Service']);
            
            $dateCondition = match($period) {
                'today' => "DATE(rj.created_at) = CURDATE()",
                'week' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)",
                'month' => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)",
                'all' => "1=1",
                default => "rj.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"
            };
            
            $stmt = $pdo->prepare("
                SELECT 
                    v.plate_number,
                    COALESCE(v.model, 'Unknown') as model,
                    c.fullname,
                    c.contact,
                    rj.repair_type,
                    COUNT(rj.id) as jobs_completed,
                    COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) as revenue,
                    MAX(rj.created_at) as last_service
                FROM repair_jobs rj
                INNER JOIN vehicles v ON v.id = rj.vehicle_id
                INNER JOIN customers c ON c.id = v.customer_id
                WHERE rj.status = 'REPAIR DONE' AND $dateCondition
                GROUP BY v.id, rj.repair_type
                ORDER BY revenue DESC
            ");
            $stmt->execute();
            
            while ($row = $stmt->fetch()) {
                fputcsv($output, [
                    $row['plate_number'],
                    $row['model'],
                    $row['fullname'],
                    $row['contact'],
                    $row['repair_type'],
                    $row['jobs_completed'],
                    number_format($row['revenue'], 0),
                    $row['last_service']
                ]);
            }
            break;
    }
    
    fclose($output);
    exit;
}
?>