<?php

header("Content-Type: application/json");

include '../db.php';

$q = $_GET['q'] ?? '';

try {
    if ($q) {
        /** * REMEDIATION: SQL INJECTION PREVENTION (OWASP A03:2021)
         * We replaced the vulnerable mysqli string with a 
         * PDO Prepared Statement. Using placeholders (?) ensures that 
         * malicious payloads like ' OR '1'='1 are treated as literal 
         * text, not executable SQL commands. 
         */
        $sql = "SELECT id, name, price FROM products WHERE name LIKE ?";
        $stmt = $pdo->prepare($sql);
        $searchTerm = "%$q%";
        $stmt->execute([$searchTerm]);
    } else {
        $stmt = $pdo->query("SELECT id, name, price FROM products");
    }

    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        /** * REMEDIATION: CROSS-SITE SCRIPTING (XSS) / OUTPUT SANITIZATION
         * Even in an API, we sanitize output to ensure that downstream 
         * applications (like a JavaScript frontend) do not accidentally 
         * execute malicious scripts stored in the database.
         */
        $products[] = [
            "id"    => htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'),
            "name"  => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
            "price" => htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8')
        ];
    }

    echo json_encode([
        "status" => "success",
        "count" => count($products),
        "products" => $products
    ]);

} catch (PDOException $e) {
    /** * REMEDIATION: INFORMATION DISCLOSURE PREVENTION
     * Technical database errors are caught and logged server-side. 
     * The API returns a generic failure message to prevent revealing 
     * the system's internal structure to an attacker.
     */
    error_log("API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "An internal system error occurred."
    ]);
}
