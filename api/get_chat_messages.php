<?php
header('Content-Type: application/json');

try {
    // Panggil koneksi terpusat dari folder config
    require_once '../config/database.php'; // <-- Ditambah ../ artinya naik 1 folder dulu

    // Ambil ID sesi dari parameter URL (contoh: get_chat_messages.php?session_id=1)
    $sessionId = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

    if ($sessionId > 0 && isset($pdo)) {
        // Ambil semua pesan dalam sesi ini diurutkan dari yang paling lama (kronologis)
        $stmt = $pdo->prepare("SELECT sender, model_used, message FROM chat_messages WHERE session_id = ? ORDER BY id ASC");
        $stmt->execute([$sessionId]);
        $messages = $stmt->fetchAll();
        
        echo json_encode($messages);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode([]);
}
?>