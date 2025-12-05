<?php
    // Simple API endpoint to get payment methods and point prices
    // No authentication required - public data
    
    // Database connection using PDO
    $db_name = "mysql:host=localhost;dbname=webtoon2";
    $username = "root";
    $password = "";
    
    try {
        $conn = new PDO($db_name, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => 'Database connection failed'
        ];
        echo json_encode($response);
        exit;
    }

    // Get base URL for icon generation
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
    $base_url = $protocol . $host . dirname(dirname(dirname($_SERVER['PHP_SELF'])));

    // Function to generate icon URL based on payment type
    function getPaymentIconUrl($payment_type, $base_url) {
        // Normalize payment type name for icon filename
        $icon_name = strtolower(str_replace(' ', '-', trim($payment_type)));
        // Return the most likely path (jpg is most common)
        $icon_path = "/uploads/icons/payment-{$icon_name}.jpg";
        return $base_url . $icon_path;
    }

    $response = [
        'status' => 'success',
        'prices' => [],
        'payment_methods' => []
    ];

    // Fetch point prices from database
    try {
        $stmt = $conn->prepare("SELECT point, amount FROM `point_prices` ORDER BY point ASC");
        $stmt->execute();
        $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($prices as $price) {
            $response['prices'][] = [
                'point' => (int)$price['point'],
                'price' => (float)$price['amount']
            ];
        }
    } catch (Exception $e) {
        // If query fails, return empty array
        $response['prices'] = [];
    }

    // Fetch payment methods from database
    try {
        $stmt = $conn->prepare("SELECT payment_type, payment_number, account_name FROM `payment_methods` ORDER BY id ASC");
        $stmt->execute();
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($methods as $method) {
            $response['payment_methods'][] = [
                'payment_method' => $method['payment_type'],
                'phone' => $method['payment_number'],
                'icon' => getPaymentIconUrl($method['payment_type'], $base_url),
                'account_name' => $method['account_name']
            ];
        }
    } catch (Exception $e) {
        // If query fails, return empty array
        $response['payment_methods'] = [];
    }

    // Set JSON header
    header('Content-Type: application/json');
    echo json_encode($response);
?>

