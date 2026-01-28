<?php
require_once 'config.php';
requireRole('arzt');

$conn = getDBConnection();

// Alle Termine laden (gruppiert nach Status)
$freie_termine = [];
$angefragte_termine = [];
$bestaetigte_termine = [];
$bestaetigte_termine_vergangen = [];

$sql = "SELECT a.*, u.name as patient_name, u.email as patient_email, 
        arzt.name as confirmed_by_name, a.confirmed_at
        FROM appointments a 
        LEFT JOIN users u ON a.user_id = u.id 
        LEFT JOIN users arzt ON a.confirmed_by = arzt.id
        ORDER BY a.date DESC, a.time DESC";
$result = $conn->query($sql);

$heute = date('Y-m-d');

while ($row = $result->fetch_assoc()) {
    if ($row['status'] === 'frei') {
        $freie_termine[] = $row;
    } elseif ($row['status'] === 'angefragt') {
        $angefragte_termine[] = $row;
    } elseif ($row['status'] === 'bestätigt') {
        // Vergangene und zukünftige Termine trennen
        if ($row['date'] < $heute) {
            $bestaetigte_termine_vergangen[] = $row;
        } else {
            $bestaetigte_termine[] = $row;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arzt Dashboard - Termin2Praxis</title>
    <link rel="icon" type="image/svg+xml" href="assets/T2P_transparent_2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/T2P_transparent_2.svg" alt="T2P Logo" style="height: 45px; margin-right: 10px;">
                <span>Termin2Praxis - Arzt</span>
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Willkommen, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Abmelden</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Neuen Termin erstellen -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>Neuen freien Termin erstellen</h5>
            </div>
            <div class="card-body">
                <form id="createAppointmentForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Datum</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="time" class="form-label">Uhrzeit</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Termin erstellen</button>
                </form>
                <div id="createMessage" class="mt-3"></div>
            </div>
        </div>

        <!-- Angefragte Termine -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5>Angefragte Termine (<?php echo count($angefragte_termine); ?>)</h5>
            </div>
            <div class="card-body">
                <div id="angefragteTermine">
                    <?php if (empty($angefragte_termine)): ?>
                        <p class="text-muted">Keine angefragten Termine vorhanden.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Uhrzeit</th>
                                        <th>Patient</th>
                                        <th>E-Mail</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($angefragte_termine as $termin): ?>
                                        <tr id="termin-<?php echo $termin['id']; ?>">
                                            <td><?php echo date('d.m.Y', strtotime($termin['date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</td>
                                            <td><?php echo htmlspecialchars($termin['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($termin['patient_email']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-success me-2" onclick="confirmAppointment(<?php echo $termin['id']; ?>)">
                                                    Bestätigen
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="rejectAppointment(<?php echo $termin['id']; ?>)">
                                                    Ablehnen
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bestätigte Termine -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">⭐ AKTUELLE TERMINE ⭐ (<?php echo count($bestaetigte_termine); ?>)</h5>
                <?php if (count($bestaetigte_termine_vergangen) > 0): ?>
                    <button class="btn btn-outline-light btn-sm" onclick="toggleVergangeneTermine()" id="toggleBtn">
                        + <?php echo count($bestaetigte_termine_vergangen); ?> vergangene Termine anzeigen
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($bestaetigte_termine)): ?>
                    <p class="text-muted">Keine aktuellen Termine vorhanden.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Uhrzeit</th>
                                    <th>Patient</th>
                                    <th>E-Mail</th>
                                    <th>Bestätigt von</th>
                                    <th>Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bestaetigte_termine as $termin): ?>
                                    <tr id="termin-<?php echo $termin['id']; ?>">
                                        <td><?php echo date('d.m.Y', strtotime($termin['date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</td>
                                        <td><?php echo htmlspecialchars($termin['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($termin['patient_email']); ?></td>
                                        <td>
                                            <?php if ($termin['confirmed_by_name']): ?>
                                                <span class="badge bg-success">Bestätigt von <?php echo htmlspecialchars($termin['confirmed_by_name']); ?></span><br>
                                                <small class="text-muted"><?php echo date('d.m.Y H:i', strtotime($termin['confirmed_at'])); ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Keine Info</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger" onclick="deleteAppointment(<?php echo $termin['id']; ?>)">
                                                Löschen
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <!-- Vergangene Termine (versteckt) -->
                <?php if (!empty($bestaetigte_termine_vergangen)): ?>
                    <div id="vergangeneTermine" style="display: none;">
                        <hr>
                        <h6 class="text-muted mb-3">
                            📦 Vergangene Termine (<?php echo count($bestaetigte_termine_vergangen); ?>)
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Uhrzeit</th>
                                        <th>Patient</th>
                                        <th>E-Mail</th>
                                        <th>Bestätigt von</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bestaetigte_termine_vergangen as $termin): ?>
                                        <tr id="termin-<?php echo $termin['id']; ?>" class="text-muted">
                                            <td><?php echo date('d.m.Y', strtotime($termin['date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</td>
                                            <td><?php echo htmlspecialchars($termin['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($termin['patient_email']); ?></td>
                                            <td>
                                                <?php if ($termin['confirmed_by_name']): ?>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($termin['confirmed_by_name']); ?></span><br>
                                                    <small class="text-muted"><?php echo date('d.m.Y', strtotime($termin['confirmed_at'])); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteAppointment(<?php echo $termin['id']; ?>)">
                                                    Löschen
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Freie Termine -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5>Freie Termine (<?php echo count($freie_termine); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($freie_termine)): ?>
                    <p class="text-muted">Keine freien Termine vorhanden.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Uhrzeit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($freie_termine as $termin): ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y', strtotime($termin['date'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</td>
                                        <td><span class="badge bg-secondary">Frei</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/arzt.js"></script>
</body>
</html>
