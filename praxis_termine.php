<?php
require_once 'config.php';

// Praxis ID aus URL holen
$praxis_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($praxis_id <= 0) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// Praxis-Informationen laden (inklusive accepting_bookings Status)
$sql = "SELECT * FROM praxen WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $praxis_id);
$stmt->execute();
$praxis = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$praxis) {
    header('Location: index.php');
    exit;
}

// Freie Termine für diese Praxis laden
$sql = "SELECT * FROM appointments WHERE praxis_id = ? AND status = 'frei' AND date >= CURDATE() ORDER BY date, time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $praxis_id);
$stmt->execute();
$freie_termine = $stmt->get_result();
$stmt->close();

// Wenn eingeloggt, "Meine Termine" für diese Praxis anzeigen
$meine_termine = null;
$vergangene_termine = null;
$notification_count = 0;
if (isLoggedIn() && hasRole('patient')) {
    $user_id = $_SESSION['user_id'];
    
    // Nur aktuelle/zukünftige Termine
    $sql = "SELECT * FROM appointments WHERE user_id = ? AND praxis_id = ? AND status IN ('angefragt', 'bestätigt', 'abgelehnt', 'storniert') AND date >= CURDATE() ORDER BY date, time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $praxis_id);
    $stmt->execute();
    $meine_termine = $stmt->get_result();
    $stmt->close();
    
    // Vergangene Termine
    $sql_past = "SELECT * FROM appointments WHERE user_id = ? AND praxis_id = ? AND status IN ('angefragt', 'bestätigt', 'abgelehnt', 'storniert') AND date < CURDATE() ORDER BY date DESC, time DESC";
    $stmt_past = $conn->prepare($sql_past);
    $stmt_past->bind_param("ii", $user_id, $praxis_id);
    $stmt_past->execute();
    $vergangene_termine = $stmt_past->get_result();
    $stmt_past->close();
    
    // Benachrichtigungen zählen (nur ungelesene für diese Praxis)
    $sql_notifications = "SELECT COUNT(*) as count FROM appointments WHERE user_id = ? AND praxis_id = ? AND status IN ('bestätigt', 'abgelehnt', 'storniert') AND is_read = FALSE";
    $stmt_notif = $conn->prepare($sql_notifications);
    $stmt_notif->bind_param("ii", $user_id, $praxis_id);
    $stmt_notif->execute();
    $result_notif = $stmt_notif->get_result();
    $notification_count = $result_notif->fetch_assoc()['count'];
    $stmt_notif->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($praxis['name']); ?> - Termin2Praxis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .praxis-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .praxis-info-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">← Zurück zur Übersicht</a>
            <div class="navbar-nav ms-auto align-items-center">
                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole('patient')): ?>
                        <!-- Benachrichtigungs-Glocke -->
                        <div class="dropdown me-3">
                            <button class="btn btn-link nav-link position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5rem; text-decoration: none; color: white;">
                                🔔
                                <?php if ($notification_count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $notification_count; ?>
                                    </span>
                                <?php endif; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 350px;">
                                <li><h6 class="dropdown-header">Benachrichtigungen</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-muted"><small>Benachrichtigungen für diese Praxis</small></a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <span class="navbar-text me-3">
                        Willkommen, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                    <?php if (hasRole('arzt')): ?>
                        <a class="btn btn-outline-light btn-sm me-2" href="dashboard_arzt.php">Arzt Dashboard</a>
                    <?php endif; ?>
                    <a class="btn btn-outline-light btn-sm" href="logout.php">Abmelden</a>
                <?php else: ?>
                    <a class="btn btn-outline-light btn-sm" href="login.php">Anmelden</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Praxis Header -->
    <div class="praxis-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <?php if (!empty($praxis['bild_url']) && $praxis['bild_url'] !== 'https://via.placeholder.com/400x300?text=Arztpraxis'): ?>
                        <img src="<?php echo htmlspecialchars($praxis['bild_url']); ?>" 
                             alt="<?php echo htmlspecialchars($praxis['name']); ?>" 
                             class="img-fluid rounded shadow"
                             style="max-height: 200px; width: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" style="height: 200px;">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#6c757d" viewBox="0 0 16 16">
                                    <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                    <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2Zm10.798 11c-.453-1.27-1.76-3-4.798-3-3.037 0-4.345 1.73-4.798 3H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1.202Z"/>
                                </svg>
                                <p class="mb-0 mt-2 text-muted small fw-bold">Foto folgt</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($praxis['name']); ?></h1>
                    <p class="lead mb-2"><?php echo htmlspecialchars($praxis['beschreibung']); ?></p>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark me-2">
                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($praxis['adresse']); ?>
                        </span>
                        <span class="badge bg-light text-dark me-2">
                            <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($praxis['telefon']); ?>
                        </span>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($praxis['email']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <!-- Info für nicht eingeloggte Benutzer -->
        <?php if (!isLoggedIn()): ?>
            <div class="alert alert-info text-center mb-5">
                <i class="bi bi-info-circle"></i> 
                <strong>Hinweis:</strong> Zum Buchen eines Termins müssen Sie sich <a href="login.php" class="alert-link fw-bold">anmelden</a> oder registrieren.
            </div>
        <?php endif; ?>

        <?php if ($meine_termine && $meine_termine->num_rows > 0): ?>
            <!-- Meine Termine (nur für eingeloggte Patienten) -->
            <div class="card mb-5 shadow" id="meineTermine">
                <div class="card-header bg-info text-white py-3">
                    <h4 class="mb-0">✓ Meine aktuellen Termine bei <?php echo htmlspecialchars($praxis['name']); ?></h4>
                </div>
                <div class="card-body p-4">
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
                                            <?php elseif ($termin['status'] === 'abgelehnt'): ?>
                                                <span class="badge bg-danger">Abgelehnt</span>
                                            <?php elseif ($termin['status'] === 'storniert'): ?>
                                                <span class="badge bg-secondary">Storniert</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Bestätigt</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hinweis wenn Buchungen deaktiviert sind -->
        <?php if (isset($praxis['accepting_bookings']) && !$praxis['accepting_bookings']): ?>
            <div class="alert alert-warning shadow-lg text-center mb-5">
                <h4><i class="bi bi-exclamation-triangle"></i> Terminbuchung derzeit nicht möglich</h4>
                <p class="mb-3">Diese Praxis ist derzeit überlaufen und nimmt keine neuen Terminanfragen an.</p>
                <p class="mb-0">Bitte wählen Sie eine andere Praxis in Ihrer Nähe oder versuchen Sie es später erneut.</p>
                <a href="index.php" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Zurück zur Praxisübersicht
                </a>
            </div>
        <?php endif; ?>

        <!-- Verfügbare Termine -->
        <div class="card mb-5 shadow-lg">
            <div class="card-header bg-success text-white py-3">
                <h3 class="mb-0">📅 Verfügbare Termine</h3>
                <p class="mb-0 mt-2">Wählen Sie einen passenden Termin aus</p>
            </div>
            <div class="card-body p-4">
                <?php if (isset($praxis['accepting_bookings']) && !$praxis['accepting_bookings']): ?>
                    <div class="alert alert-danger text-center">
                        <h5><i class="bi bi-pause-circle"></i> Terminbuchung gestoppt</h5>
                        <p class="mb-0">Die Praxis nimmt derzeit keine neuen Terminanfragen entgegen.</p>
                    </div>
                <?php elseif ($freie_termine->num_rows === 0): ?>
                    <div class="alert alert-warning text-center">
                        <h5>Derzeit sind keine freien Termine verfügbar.</h5>
                        <p class="mb-0">Bitte schauen Sie später noch einmal vorbei.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php while ($termin = $freie_termine->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-4">
                                        <div class="display-6 mb-3">📅</div>
                                        <h5 class="card-title fw-bold mb-3">
                                            <?php echo date('d.m.Y', strtotime($termin['date'])); ?>
                                        </h5>
                                        <p class="card-text fs-4 fw-bold text-primary mb-4">
                                            <?php echo date('H:i', strtotime($termin['time'])); ?> Uhr
                                        </p>
                                        <button class="btn btn-success btn-lg w-100" onclick="bookAppointment(<?php echo $termin['id']; ?>)">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Termin buchen
        function bookAppointment(appointmentId) {
            if (!confirm('Möchten Sie diesen Termin buchen?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            
            fetch('api/book_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                alert('Fehler beim Buchen des Termins');
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
