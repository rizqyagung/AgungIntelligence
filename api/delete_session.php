<?php
header('Content-Type: application/json');

try {
    require_once '../config/database.php'; // <-- Ditambah ../ artinya naik 1 folder dulu

    // Ambil ID sesi yang mau dihapus dari parameter URL
    $sessionId = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

    if ($sessionId > 0 && isset($pdo)) {
        // Eksekusi hapus sesi chat
        $stmt = $pdo->prepare("DELETE FROM chat_sessions WHERE id = ?");
        $stmt->execute([$sessionId]);

        echo json_encode([
            "status" => "success",
            "message" => "Sesi chat berhasil dihapus"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID Sesi tidak valid atau database terputus"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>