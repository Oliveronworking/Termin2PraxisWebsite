<?php
require_once 'config.php';

$conn = getDBConnection();

// Freie Termine laden
$sql = "SELECT * FROM appointments WHERE status = 'frei' ORDER BY date, time";
$freie_termine = $conn->query($sql);

// Wenn eingeloggt, "Meine Termine" anzeigen
$meine_termine = null;
$vergangene_termine = null;
$notification_count = 0;
if (isLoggedIn() && hasRole('patient')) {
    $user_id = $_SESSION['user_id'];
    // Nur aktuelle/zukünftige Termine
    $sql = "SELECT * FROM appointments WHERE user_id = ? AND status IN ('angefragt', 'bestätigt', 'abgelehnt', 'storniert') AND date >= CURDATE() ORDER BY date, time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $meine_termine = $stmt->get_result();
    
    // Vergangene Termine
    $sql_past = "SELECT * FROM appointments WHERE user_id = ? AND status IN ('angefragt', 'bestätigt', 'abgelehnt', 'storniert') AND date < CURDATE() ORDER BY date DESC, time DESC";
    $stmt_past = $conn->prepare($sql_past);
    $stmt_past->bind_param("i", $user_id);
    $stmt_past->execute();
    $vergangene_termine = $stmt_past->get_result();
    
    // Benachrichtigungen zählen (nur ungelesene bestätigte + abgelehnte + stornierte Termine)
    $sql_notifications = "SELECT COUNT(*) as count FROM appointments WHERE user_id = ? AND status IN ('bestätigt', 'abgelehnt', 'storniert') AND is_read = FALSE";
    $stmt_notif = $conn->prepare($sql_notifications);
    $stmt_notif->bind_param("i", $user_id);
    $stmt_notif->execute();
    $result_notif = $stmt_notif->get_result();
    $notification_count = $result_notif->fetch_assoc()['count'];
    $stmt_notif->close();
    
    $stmt->close();
    $stmt_past->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termin2Praxis - Termine buchen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
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
            <a class="navbar-brand" href="index.php">Termin2Praxis</a>
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
                                            <div class="dropdown-item <?php echo !$notif['is_read'] ? 'bg-light' : ''; ?> d-flex align-items-start justify-content-between">
                                                <a href="#meineTermine" class="flex-grow-1 text-decoration-none text-reset">
                                                    <div class="d-flex align-items-start">
                                                        <?php if (!$notif['is_read']): ?>
                                                            <span class="badge bg-danger me-2" style="font-size: 0.6rem; padding: 0.2em 0.4em;">NEU</span>
                                                        <?php endif; ?>
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
                                                <button class="btn btn-sm btn-link text-danger p-1 ms-2 delete-notification-btn" 
                                                        onclick="deleteNotification(<?php echo $notif['id']; ?>, event, this)" 
                                                        title="Benachrichtigung löschen" 
                                                        style="border-radius: 4px; transition: all 0.2s;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                    </svg>
                                                </button>
                                            </div>
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
            <p class="fs-5 text-muted mb-4">Sehen Sie sich die verfügbaren Termine an und buchen Sie direkt</p>
            <a href="#verfuegbareTermine" class="btn btn-primary btn-lg px-5 py-3 fs-4">
                Jetzt freie Termine ansehen
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
                    <?php if ($vergangene_termine && $vergangene_termine->num_rows > 0): ?>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#vergangeneTermineModal">
                            Vergangene Termine (<?php echo $vergangene_termine->num_rows; ?>)
                        </button>
                    <?php endif; ?>
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

        <!-- Modal für vergangene Termine -->
        <?php if (isLoggedIn() && hasRole('patient') && $vergangene_termine && $vergangene_termine->num_rows > 0): ?>
            <div class="modal fade" id="vergangeneTermineModal" tabindex="-1" aria-labelledby="vergangeneTermineModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title" id="vergangeneTermineModalLabel">Vergangene Termine</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Datum</th>
                                            <th>Uhrzeit</th>
                                            <th>Status</th>
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody id="vergangeneTermineTableBody">
                                        <?php 
                                        $vergangene_termine->data_seek(0); // Reset pointer
                                        while ($termin = $vergangene_termine->fetch_assoc()): 
                                        ?>
                                            <tr id="vergangener-termin-<?php echo $termin['id']; ?>">
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
                                                <td>
                                                    <button class="btn btn-sm btn-link text-danger p-1" 
                                                            onclick="deleteVergangenerTermin(<?php echo $termin['id']; ?>)" 
                                                            title="Termin löschen" 
                                                            style="border-radius: 4px; transition: all 0.2s;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" onclick="deleteAlleVergangeneTermine()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                                Alle vergangenen Termine löschen
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Verfügbare Termine (für alle sichtbar) -->
        <div class="card mb-5 shadow-lg" id="verfuegbareTermine">
            <div class="card-header bg-success text-white py-3">
                <h3 class="mb-0">📅 Verfügbare Termine</h3>
                <p class="mb-0 mt-2">Wählen Sie einen passenden Termin aus</p>
            </div>
            <div class="card-body p-4">
                <div id="verfuegbareTermine">
                    <?php if ($freie_termine->num_rows === 0): ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Benachrichtigungen als gelesen markieren, wenn Dropdown geöffnet wird
        <?php if (isLoggedIn() && hasRole('patient')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown) {
                notificationDropdown.addEventListener('click', function() {
                    // Benachrichtigungen als gelesen markieren
                    fetch('api/mark_notifications_read.php', {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Badge nach kurzer Verzögerung ausblenden
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

        // Funktion zum Löschen einer Benachrichtigung
        function deleteNotification(appointmentId, event, buttonElement) {
            event.preventDefault();
            event.stopPropagation();
            
            if (!confirm('Möchten Sie diese Benachrichtigung wirklich löschen?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            
            // Button während der Anfrage deaktivieren
            buttonElement.disabled = true;
            buttonElement.style.opacity = '0.5';
            
            fetch('api/delete_notification.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Element mit Animation entfernen
                    const listItem = buttonElement.closest('li');
                    listItem.style.transition = 'opacity 0.3s, height 0.3s';
                    listItem.style.opacity = '0';
                    listItem.style.height = listItem.offsetHeight + 'px';
                    
                    setTimeout(() => {
                        listItem.style.height = '0';
                        listItem.style.padding = '0';
                        listItem.style.margin = '0';
                        
                        setTimeout(() => {
                            listItem.remove();
                            
                            // Badge-Zähler aktualisieren
                            const badge = document.querySelector('#notificationDropdown .badge');
                            const dropdown = document.querySelector('#notificationDropdown + .dropdown-menu');
                            const remainingItems = dropdown.querySelectorAll('li a.dropdown-item').length;
                            
                            // Prüfen ob noch Benachrichtigungen vorhanden sind
                            if (remainingItems === 0) {
                                dropdown.innerHTML = '<li><h6 class="dropdown-header">Benachrichtigungen</h6></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-muted"><small>Noch keine Terminbestätigungen</small></a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-center text-primary" href="#meineTermine">Alle Termine anzeigen</a></li>';
                            }
                        }, 300);
                    }, 50);
                } else {
                    alert(data.message);
                    buttonElement.disabled = false;
                    buttonElement.style.opacity = '1';
                }
            })
            .catch(error => {
                alert('Fehler beim Löschen der Benachrichtigung');
                console.error('Error:', error);
                buttonElement.disabled = false;
                buttonElement.style.opacity = '1';
            });
        }

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
                    // Wenn nicht eingeloggt, zur Login-Seite weiterleiten
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

        // Einzelnen vergangenen Termin löschen
        function deleteVergangenerTermin(appointmentId) {
            if (!confirm('Möchten Sie diesen Termin wirklich löschen?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            
            fetch('api/delete_appointment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Zeile entfernen
                    const row = document.getElementById('vergangener-termin-' + appointmentId);
                    if (row) {
                        row.style.transition = 'opacity 0.3s';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            
                            // Prüfen ob noch Termine vorhanden sind
                            const tbody = document.getElementById('vergangeneTermineTableBody');
                            if (tbody && tbody.children.length === 0) {
                                // Modal schließen und Seite neu laden
                                location.reload();
                            }
                        }, 300);
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Fehler beim Löschen des Termins');
                console.error('Error:', error);
            });
        }

        // Alle vergangenen Termine löschen
        function deleteAlleVergangeneTermine() {
            if (!confirm('Möchten Sie wirklich ALLE vergangenen Termine löschen? Diese Aktion kann nicht rückgängig gemacht werden!')) {
                return;
            }
            
            fetch('api/delete_appointment.php', {
                method: 'POST',
                body: new URLSearchParams({
                    delete_all_past: 'true'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Fehler beim Löschen der Termine');
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
