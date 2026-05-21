<?php
header('Content-Type: application/json');

try {
    require_once '../config/database.php'; // <-- Ditambah ../ artinya naik 1 folder dulu

    if (isset($pdo)) {
        // Buat sesi baru dengan nama default "Chat Baru"
        $stmt = $pdo->prepare("INSERT INTO chat_sessions (title) VALUES ('Chat Baru')");
        $stmt->execute();
        
        $newSessionId = $pdo->lastInsertId();

        echo json_encode([
            "status" => "success",
            "session_id" => intval($newSessionId)
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database terputus"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>