<?php
// 1. SET HEADER JSON & ERROR HANDLING KETAT
header('Content-Type: application/json');
error_reporting(0); // Matikan display error mentah agar tidak merusak format JSON response

// 2. LOAD KONEKSI DATABASE
try {
    require_once '../config/database.php'; // <-- Ditambah ../ artinya naik 1 folder dulu
} catch (Exception $e) {
    $pdo = null;
}

$apiKey = "AIzaSyBUoIeyxcXMNeBeOoGuLZCHfIb9YKT6oJY"; 

// 3. PROSES REQUEST POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    $userMessage = $input['message'] ?? '';
    $modelType = $input['model'] ?? 'gemini-flash'; // Default ke flash jika kosong
    $sessionId = isset($input['session_id']) ? intval($input['session_id']) : 1;

    if (empty($userMessage)) {
        echo json_encode(['reply' => 'Pesan kosong']);
        exit;
    }

    // Pemetaan Model Resmi Google API v1beta
    if ($modelType === 'gemini-pro') {
        $googleModel = "gemini-2.5-pro";
    } else {
        $googleModel = "gemini-2.5-flash"; 
    }

    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/" . $googleModel . ":generateContent?key=" . $apiKey;

$data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $userMessage]
                ]
            ]
        ],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 8192 // <-- UBAH KE 8192 AGAR JAWABAN PANJANG TIDAK TERPOTONG
        ]
    ];

    // 4. EKSEKUSI CURL KE GEMINI DENGAN TIMEOUT
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Beri batas waktu 20 detik agar tidak gantung jika internet drop
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Lewati validasi SSL lokal jika sertifikat Ubuntu bermasalah

    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo json_encode(['reply' => 'Gagal terhubung ke API Gemini (Timeout/Internet terputus).']);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $result = json_decode($response, true);
    
    // Cek detail error dari Google API Studio jika kuota habis atau model overload
    if (isset($result['error'])) {
        echo json_encode(['reply' => 'Google API Error: ' . ($result['error']['message'] ?? 'Unknown error')]);
        exit;
    }

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $aiResponse = 'Maaf, Agung Intelligence gagal mendapatkan respon yang valid dari model.';
    }
    
    // 5. PROSES SIMPAN KE DATABASE VIA PDO (Ditaruh paling akhir setelah AI sukses merespon)
    if (isset($pdo) && $pdo !== null && !empty($aiResponse) && strpos($aiResponse, 'Maaf, Agung Intelligence') === false) {
        try {
            // Simpan pesan User
            $stmtUser = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, model_used, message) VALUES (?, 'user', ?, ?)");
            $stmtUser->execute([$sessionId, $modelType, $userMessage]);

            // Simpan pesan AI
            $stmtAi = $pdo->prepare("INSERT INTO chat_messages (session_id, sender, model_used, message) VALUES (?, 'ai', ?, ?)");
            $stmtAi->execute([$sessionId, $modelType, $aiResponse]);

            // Update judul sesi jika judulnya masih bawaan "Chat Baru"
            $stmtCheck = $pdo->prepare("SELECT title FROM chat_sessions WHERE id = ?");
            $stmtCheck->execute([$sessionId]);
            $currentTitle = $stmtCheck->fetchColumn();

            if ($currentTitle === "Chat Baru") {
                $newTitle = mb_strimwidth($userMessage, 0, 25, "...");
                $stmtUpdate = $pdo->prepare("UPDATE chat_sessions SET title = ? WHERE id = ?");
                $stmtUpdate->execute([$newTitle, $sessionId]);
            }
        } catch (Exception $dbEx) {
            // Abaikan error log query
        }
    }

    // 6. KEMBALIKAN HASIL KE FRONTEND
    echo json_encode(['reply' => $aiResponse]);
}
?>