<?php
require_once 'config.php';
requireRole('patient');

$conn = getDBConnection();

// Freie Termine laden
$sql = "SELECT * FROM appointments WHERE status = 'frei' ORDER BY date, time";
$freie_termine = $conn->query($sql);

// Termine des Patienten laden (angefragt und bestätigt)
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM appointments WHERE user_id = ? AND status IN ('angefragt', 'bestätigt') ORDER BY date, time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meine_termine = $stmt->get_result();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patienten Dashboard - Termin2Praxis</title>
    <link rel="icon" type="image/svg+xml" href="assets/T2P_transparent_2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/T2P_transparent_2.svg" alt="T2P Logo" style="height: 45px; margin-right: 10px;">
                <span>Termin2Praxis - Patient</span>
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
        <!-- Meine Termine -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>Meine Termine</h5>
            </div>
            <div class="card-body">
                <div id="meineTermine">
                    <?php if ($meine_termine->num_rows === 0): ?>
                        <p class="text-muted">Sie haben noch keine Termine gebucht.</p>
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
                                    <?php while ($termin = $meine_termine->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d.m.Y', strtotime($termin['date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</td>
                                            <td>
                                                <?php if ($termin['status'] === 'angefragt'): ?>
                                                    <span class="badge bg-warning text-dark">Angefragt</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Bestätigt</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Verfügbare Termine -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>Verfügbare Termine buchen</h5>
            </div>
            <div class="card-body">
                <div id="verfuegbareTermine">
                    <?php if ($freie_termine->num_rows === 0): ?>
                        <p class="text-muted">Derzeit sind keine freien Termine verfügbar.</p>
                    <?php else: ?>
                        <div class="row">
                            <?php while ($termin = $freie_termine->fetch_assoc()): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <?php echo date('d.m.Y', strtotime($termin['date'])); ?>
                                            </h6>
                                            <p class="card-text">
                                                <strong><?php echo date('H:i', strtotime($termin['time'])); ?> Uhr</strong>
                                            </p>
                                            <button class="btn btn-primary btn-sm" onclick="bookAppointment(<?php echo $termin['id']; ?>)">
                                                Termin buchen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/patient.js"></script>
</body>
</html>
