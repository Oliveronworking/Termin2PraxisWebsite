<?php
require_once 'config.php';

$conn = getDBConnection();

// Alle Arztpraxen laden
$sql = "SELECT * FROM praxen ORDER BY name";
$praxen = $conn->query($sql);

// Benachrichtigungen zählen (global für alle Praxen)
$notification_count = 0;
$meine_termine = null;
if (isLoggedIn() && hasRole('patient')) {
    $user_id = $_SESSION['user_id'];
    
    // Benachrichtigungen zählen (nur ungelesene bestätigte + abgelehnte + stornierte Termine)
    $sql_notifications = "SELECT COUNT(*) as count FROM appointments WHERE user_id = ? AND status IN ('bestätigt', 'abgelehnt', 'storniert') AND is_read = FALSE";
    $stmt_notif = $conn->prepare($sql_notifications);
    $stmt_notif->bind_param("i", $user_id);
    $stmt_notif->execute();
    $result_notif = $stmt_notif->get_result();
    $notification_count = $result_notif->fetch_assoc()['count'];
    $stmt_notif->close();
    
    // Alle aktuellen Termine des Patienten laden (über alle Praxen)
    $sql = "SELECT a.*, p.name as praxis_name FROM appointments a 
            LEFT JOIN praxen p ON a.praxis_id = p.id 
            WHERE a.user_id = ? AND a.status IN ('angefragt', 'bestätigt', 'abgelehnt', 'storniert') AND a.date >= CURDATE() 
            ORDER BY a.date, a.time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $meine_termine = $stmt->get_result();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termin2Praxis - Termine buchen</title>
    <link rel="icon" type="image/svg+xml" href="assets/T2P_transparent_2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .praxis-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            height: 100%;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .praxis-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .praxis-card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
        .praxis-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.95);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .delete-notification-btn:hover {
            background-color: rgba(220, 53, 69, 0.1) !important;
            transform: scale(1.1);
        }
        .delete-notification-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/T2P_transparent_2.svg" alt="T2P Logo" style="height: 45px; margin-right: 10px;">
                <span>Termin2Praxis</span>
            </a>
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
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header">Benachrichtigungen</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <?php 
                                // Nur bestätigte, abgelehnte und stornierte Termine für Benachrichtigungen abrufen
                                $user_id_notif = $_SESSION['user_id'];
                                $conn_notif = getDBConnection();
                                $sql_all = "SELECT * FROM appointments WHERE user_id = ? AND status IN ('bestätigt', 'abgelehnt', 'storniert') ORDER BY is_read ASC, date DESC, time DESC LIMIT 10";
                                $stmt_all = $conn_notif->prepare($sql_all);
                                $stmt_all->bind_param("i", $user_id_notif);
                                $stmt_all->execute();
                                $all_termine = $stmt_all->get_result();
                                
                                if ($all_termine->num_rows === 0): 
                                ?>
                                    <li><a class="dropdown-item text-muted"><small>Noch keine Terminbestätigungen</small></a></li>
                                <?php else: ?>
                                    <?php 
                                    $count = 0;
                                    $total = $all_termine->num_rows;
                                    while ($notif = $all_termine->fetch_assoc()): 
                                        $count++;
                                    ?>
                                        <li>
                                            <a class="dropdown-item" href="#meineTermine">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong><?php echo date('d.m.Y', strtotime($notif['date'])); ?></strong> um <?php echo date('H:i', strtotime($notif['time'])); ?> Uhr<br>
                                                        <?php if ($notif['status'] === 'bestätigt'): ?>
                                                            <span class="badge bg-success">✓ Bestätigt</span>
                                                            <small class="text-success">Ihr Termin wurde vom Arzt bestätigt</small>
                                                        <?php elseif ($notif['status'] === 'abgelehnt'): ?>
                                                            <span class="badge bg-danger">✗ Abgelehnt</span>
                                                            <small class="text-danger">Ihr Termin wurde leider abgelehnt</small>
                                                        <?php elseif ($notif['status'] === 'storniert'): ?>
                                                            <span class="badge bg-secondary">⛔ Storniert</span>
                                                            <small class="text-danger">Ihr Termin wurde vom Arzt storniert. Bitte buchen Sie einen neuen Termin.</small>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark">⏳ Angefragt</span>
                                                            <small class="text-muted">Wartet auf Bestätigung</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php if ($count < $total): ?>
                                            <li><hr class="dropdown-divider"></li>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                <?php 
                                endif;
                                $stmt_all->close();
                                $conn_notif->close();
                                ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center text-primary" href="#meineTermine">Alle Termine anzeigen</a></li>
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

    <div class="container mt-5">
        <!-- Hero Section -->
        <div class="text-center mb-5 py-5">
            <h1 class="display-3 fw-bold mb-4">Online ganz einfach Termin machen</h1>
            <p class="lead fs-3 mb-4">Ohne Anrufen - Direkt online buchen!</p>
            <p class="fs-5 text-muted mb-4">Wählen Sie eine Arztpraxis aus und buchen Sie Ihren Wunschtermin</p>
            <a href="#praxenUebersicht" class="btn btn-primary btn-lg px-5 py-3 fs-4">
                Arztpraxen ansehen
            </a>
        </div>

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
                <div class="card-header bg-info text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">✓ Meine aktuellen Termine 
                        <?php if ($notification_count > 0): ?>
                            <span class="badge bg-danger ms-2"><?php echo $notification_count; ?> neue Updates</span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Praxis</th>
                                    <th>Datum</th>
                                    <th>Uhrzeit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($termin = $meine_termine->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($termin['praxis_name']); ?></strong></td>
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

        <!-- Arztpraxen Übersicht -->
        <div class="mb-5" id="praxenUebersicht">
            <div class="text-center mb-4">
                <h2 class="display-5 fw-bold">🏥 Unsere Arztpraxen</h2>
                <p class="lead text-muted">Wählen Sie eine Praxis aus, um verfügbare Termine zu sehen</p>
            </div>
            
            <?php if ($praxen->num_rows === 0): ?>
                <div class="alert alert-warning text-center">
                    <h5>Derzeit sind keine Arztpraxen verfügbar.</h5>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php while ($praxis = $praxen->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card praxis-card shadow-sm" onclick="window.location.href='praxis_termine.php?id=<?php echo $praxis['id']; ?>'">
                                <div class="position-relative">
                                    <?php if (!empty($praxis['bild_url']) && $praxis['bild_url'] !== 'https://via.placeholder.com/400x300?text=Arztpraxis'): ?>
                                        <img src="<?php echo htmlspecialchars($praxis['bild_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($praxis['name']); ?>" 
                                             class="praxis-card-img">
                                    <?php else: ?>
                                        <div class="praxis-card-img d-flex align-items-center justify-content-center bg-light">
                                            <div class="text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#6c757d" viewBox="0 0 16 16">
                                                    <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
                                                    <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2Zm10.798 11c-.453-1.27-1.76-3-4.798-3-3.037 0-4.345 1.73-4.798 3H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-1.202Z"/>
                                                </svg>
                                                <p class="mb-0 mt-2 text-muted fw-bold">Foto folgt</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <span class="praxis-badge text-primary">
                                        <?php echo htmlspecialchars($praxis['spezialgebiet']); ?>
                                    </span>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-3"><?php echo htmlspecialchars($praxis['name']); ?></h5>
                                    <p class="card-text text-muted mb-3"><?php echo htmlspecialchars(substr($praxis['beschreibung'], 0, 120)) . '...'; ?></p>
                                    <div class="d-flex flex-column gap-2 small text-muted">
                                        <div>
                                            <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($praxis['adresse']); ?>
                                        </div>
                                        <div>
                                            <i class="bi bi-telephone-fill"></i> <?php echo htmlspecialchars($praxis['telefon']); ?>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary w-100 mt-4">
                                        Termine ansehen →
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Benachrichtigungen als gelesen markieren, wenn Dropdown geöffnet wird
        <?php if (isLoggedIn() && hasRole('patient')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function() {
                    fetch('api/mark_notifications_read.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setTimeout(function() {
                                const badge = notificationDropdown.querySelector('.badge');
                                if (badge) {
                                    badge.style.transition = 'opacity 0.3s';
                                    badge.style.opacity = '0';
                                    setTimeout(function() {
                                        badge.style.display = 'none';
                                    }, 300);
                                }
                            }, 500);
                        }
                    })
                    .catch(error => {
                        console.error('Fehler beim Markieren der Benachrichtigungen:', error);
                    });
                });
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
