<?php
require_once '../config.php';
requireRole('arzt');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? 0;
    $action = $_POST['action'] ?? 'delete'; // 'delete' oder 'reject'
    
    if (empty($appointment_id)) {
        echo json_encode(['success' => false, 'message' => 'Termin-ID fehlt']);
        exit();
    }
    
    $conn = getDBConnection();
    
    if ($action === 'reject') {
        // Termin ablehnen - Status auf 'abgelehnt' setzen
        $stmt = $conn->prepare("UPDATE appointments SET status = 'abgelehnt' WHERE id = ? AND status IN ('angefragt', 'bestätigt')");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Termin wurde abgelehnt. Patient wird benachrichtigt.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Termin konnte nicht abgelehnt werden.']);
        }
        $stmt->close();
    } else {
        // Termin stornieren (nicht löschen, damit Patient Benachrichtigung sieht)
        // Zuerst prüfen, ob der Termin existiert
        $check_stmt = $conn->prepare("SELECT id, status FROM appointments WHERE id = ?");
        $check_stmt->bind_param("i", $appointment_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Termin wurde nicht gefunden.']);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        
        $appointment = $check_result->fetch_assoc();
        $check_stmt->close();
        
        // Jetzt stornieren
        $stmt = $conn->prepare("UPDATE appointments SET status = 'storniert' WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Termin wurde storniert. Patient wird benachrichtigt.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Termin konnte nicht storniert werden. Aktueller Status: ' . $appointment['status']]);
        }
        $stmt->close();
    }
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
}
?>
