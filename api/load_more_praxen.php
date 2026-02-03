<?php
require_once '../config.php';

header('Content-Type: application/json');

$conn = getDBConnection();

// Parameter auslesen
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 6; // Pro Ladung 6 Praxen
$filter_kategorie = isset($_GET['kategorie']) ? $_GET['kategorie'] : '';
$filter_spezialgebiet = isset($_GET['spezialgebiet']) ? $_GET['spezialgebiet'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Standort-Parameter für Entfernungsberechnung
$user_lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$user_lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
$use_location = ($user_lat !== null && $user_lng !== null);

// SQL-Query mit Filtern aufbauen
if ($use_location) {
    // Haversine-Formel für Entfernungsberechnung (in km)
    // CASE wird verwendet, um NULL zurückzugeben wenn Koordinaten fehlen
    $sql = "SELECT *, 
            CASE 
                WHEN latitude IS NOT NULL AND longitude IS NOT NULL THEN
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                    cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                    sin(radians(latitude))))
                ELSE NULL
            END AS distance 
            FROM praxen WHERE 1=1";
    $params = [$user_lat, $user_lng, $user_lat];
    $types = 'ddd';
} else {
    $sql = "SELECT * FROM praxen WHERE 1=1";
    $params = [];
    $types = '';
}

if (!empty($filter_kategorie)) {
    $sql .= " AND kategorie = ?";
    $params[] = $filter_kategorie;
    $types .= 's';
}

if (!empty($filter_spezialgebiet)) {
    $sql .= " AND spezialgebiet = ?";
    $params[] = $filter_spezialgebiet;
    $types .= 's';
}

if (!empty($search_query)) {
    $sql .= " AND (name LIKE ? OR beschreibung LIKE ? OR adresse LIKE ? OR spezialgebiet LIKE ?)";
    $search_param = "%{$search_query}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ssss';
}

// Sortierung
if ($use_location) {
    // Bei aktiviertem Standort immer nach Entfernung sortieren
    // NULL-Werte (Praxen ohne Koordinaten) kommen ans Ende
    $sql .= " ORDER BY distance IS NULL ASC, distance ASC";
} else {
    // Standard-Sortierung ohne Standort
    switch ($sort_by) {
        case 'name':
            $sql .= " ORDER BY name ASC";
            break;
        case 'kategorie':
            $sql .= " ORDER BY kategorie ASC, name ASC";
            break;
        case 'spezialgebiet':
            $sql .= " ORDER BY spezialgebiet ASC, name ASC";
            break;
        default:
            $sql .= " ORDER BY name ASC";
    }
}

// Gesamtanzahl zählen (ohne LIMIT und OFFSET)
$count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $total_count = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $total_count = $conn->query($count_sql)->fetch_assoc()['total'];
}

// Paginierung
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Query ausführen
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Praxen sammeln
$praxen = [];
while ($row = $result->fetch_assoc()) {
    // HTML-Entities für Sicherheit
    $praxData = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['name']),
        'beschreibung' => htmlspecialchars($row['beschreibung']),
        'adresse' => htmlspecialchars($row['adresse']),
        'telefon' => htmlspecialchars($row['telefon']),
        'kategorie' => htmlspecialchars($row['kategorie']),
        'spezialgebiet' => htmlspecialchars($row['spezialgebiet']),
        'bild_url' => htmlspecialchars($row['bild_url']),
        'versicherungsart' => isset($row['versicherungsart']) ? htmlspecialchars($row['versicherungsart']) : '',
        'accepting_bookings' => isset($row['accepting_bookings']) ? $row['accepting_bookings'] : 1
    ];
    
    // Entfernung hinzufügen wenn verfügbar
    if (isset($row['distance'])) {
        $praxData['distance'] = round($row['distance'], 1);
    }
    
    $praxen[] = $praxData;
}

$stmt->close();
$conn->close();

// Berechnung ob es mehr gibt
$loaded_so_far = $offset + count($praxen);
$has_more = $loaded_so_far < $total_count;
$remaining = $total_count - $loaded_so_far;

// JSON Response
echo json_encode([
    'success' => true,
    'praxen' => $praxen,
    'hasMore' => $has_more,
    'remaining' => $remaining,
    'total' => $total_count,
    'loaded' => $loaded_so_far
]);
?>
