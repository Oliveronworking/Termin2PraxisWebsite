<?php
require_once 'config.php';
requireRole('arzt');

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];

// Praxen des eingeloggten Arztes laden
$sql = "SELECT * FROM praxen WHERE owner_id = ? ORDER BY name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meine_praxen = $stmt->get_result();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Praxen - Termin2Praxis</title>
    <link rel="icon" type="image/svg+xml" href="assets/T2P_transparent_2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .praxis-management-card {
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            cursor: pointer;
        }
        .praxis-management-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-color: #667eea;
        }
        .praxis-img {
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/T2P_transparent_2.svg" alt="T2P Logo" style="height: 45px; margin-right: 10px;">
                <span>Termin2Praxis - Praxisverwaltung</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link me-3" href="dashboard_arzt.php">Terminverwaltung</a>
                <span class="navbar-text me-3">
                    Willkommen, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Abmelden</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 mb-2">Meine Praxen</h1>
                <p class="lead text-muted">Verwalten Sie hier Ihre Arztpraxen. Klicken Sie auf eine Praxis, um die Details zu bearbeiten.</p>
            </div>
        </div>

        <?php if ($meine_praxen->num_rows === 0): ?>
            <div class="alert alert-info">
                <h5>Keine Praxen gefunden</h5>
                <p>Sie sind aktuell keiner Praxis als Besitzer zugeordnet. Bitte kontaktieren Sie den Administrator.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php while ($praxis = $meine_praxen->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card praxis-management-card h-100" onclick="window.location.href='edit_praxis.php?id=<?php echo $praxis['id']; ?>'">
                            <img src="<?php echo htmlspecialchars($praxis['bild_url'] ?? 'https://via.placeholder.com/400x300?text=Arztpraxis'); ?>" 
                                 class="card-img-top praxis-img" 
                                 alt="<?php echo htmlspecialchars($praxis['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($praxis['name']); ?></h5>
                                
                                <?php if (!empty($praxis['spezialgebiet'])): ?>
                                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($praxis['spezialgebiet']); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($praxis['kategorie'])): ?>
                                    <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($praxis['kategorie']); ?></span>
                                <?php endif; ?>
                                
                                <p class="card-text text-muted small">
                                    <?php 
                                    $beschreibung = $praxis['beschreibung'] ?? '';
                                    echo htmlspecialchars(strlen($beschreibung) > 100 ? substr($beschreibung, 0, 100) . '...' : $beschreibung); 
                                    ?>
                                </p>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> 
                                        <?php 
                                        if (!empty($praxis['adresse'])) {
                                            echo htmlspecialchars($praxis['adresse']);
                                        } else {
                                            echo 'Keine Adresse angegeben';
                                        }
                                        ?>
                                    </small>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="edit_praxis.php?id=<?php echo $praxis['id']; ?>" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-pencil"></i> Bearbeiten
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
