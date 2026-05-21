<?php
header('Content-Type: application/json');

try {
    require_once '../config/database.php'; // <-- Ditambah ../ artinya naik 1 folder dulu

    if (isset($pdo)) {
        // Ambil semua sesi dari database
        $stmt = $pdo->query("SELECT * FROM chat_sessions ORDER BY id DESC");
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Jika kosong, kembalikan array kosong [], JANGAN buat data dummy zombi!
        if (!$sessions) {
            echo json_encode([]);
        } else {
            echo json_encode($sessions);
        }
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode([]);
}
?>