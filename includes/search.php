<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';

    if (strlen($query) < 2) {
        echo json_encode([]);
        exit;
    }

    // Search in services table
    $sql = "SELECT id, name, category, description, price 
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

    $search_term = "%{$query}%";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("sssss", 
        $search_term, 
        $search_term, 
        $search_term,
        $search_term,
        $search_term
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Database execute error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $services = $result->fetch_all(MYSQLI_ASSOC);

    // Format results
    $results = array_map(function($item) {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'category' => $item['category'],
            'description' => $item['description'],
            'price' => $item['price']
        ];
    }, $services);

    echo json_encode($results);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 