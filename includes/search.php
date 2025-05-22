<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (strlen($query) < 2) {
        echo json_encode([]);
        exit;
    }

    $search_term = "%{$query}%";
    $results = [];

    // Search in services table
    $services_sql = "SELECT id, name, category, description, price, 'service' as type 
                    FROM services 
                    WHERE name LIKE ? 
                    OR description LIKE ? 
                    OR category LIKE ?
                    ORDER BY 
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN category LIKE ? THEN 2
                            ELSE 3
                        END,
                        name ASC
                    LIMIT 10";

    $stmt = $conn->prepare($services_sql);
    $stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $services = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $results = array_merge($results, $services);

    // Search in trainers table
    $trainers_sql = "SELECT id, name, specialization as category, bio as description, 'trainer' as type 
                    FROM trainers 
                    WHERE name LIKE ? 
                    OR specialization LIKE ? 
                    OR bio LIKE ?
                    ORDER BY 
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN specialization LIKE ? THEN 2
                            ELSE 3
                        END,
                        name ASC
                    LIMIT 10";

    $stmt = $conn->prepare($trainers_sql);
    $stmt->bind_param("sssss", $search_term, $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $trainers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $results = array_merge($results, $trainers);

    // Format all results consistently
    $formatted_results = array_map(function($item) {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'category' => $item['category'],
            'description' => $item['description'],
            'type' => $item['type'],
            'price' => $item['price'] ?? null // Only services have price
        ];
    }, $results);

    echo json_encode($formatted_results);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}