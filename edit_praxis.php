<?php
require_once 'config.php';
requireRole('arzt');

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$praxis_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prüfen, ob die Praxis dem Arzt gehört
$sql = "SELECT * FROM praxen WHERE id = ? AND owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $praxis_id, $user_id);
$stmt->execute();
$praxis = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$praxis) {
    header('Location: dashboard_praxisbesitzer.php');
    exit;
}

$success = '';
$error = '';

// Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $beschreibung = trim($_POST['beschreibung'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $plz = trim($_POST['plz'] ?? '');
    $stadt = trim($_POST['stadt'] ?? '');
    $telefon = trim($_POST['telefon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $spezialgebiet = trim($_POST['spezialgebiet'] ?? '');
    $kategorie = trim($_POST['kategorie'] ?? '');
    $bild_url = trim($_POST['bild_url'] ?? '');
    
    if (empty($name)) {
        $error = 'Der Name der Praxis ist erforderlich.';
    } else {
        $update_sql = "UPDATE praxen SET 
            name = ?, 
            beschreibung = ?, 
            adresse = ?, 
            plz = ?, 
            stadt = ?, 
            telefon = ?, 
            email = ?, 
            spezialgebiet = ?, 
            kategorie = ?,
            bild_url = ?
            WHERE id = ? AND owner_id = ?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssssssssii", $name, $beschreibung, $adresse, $plz, $stadt, $telefon, $email, $spezialgebiet, $kategorie, $bild_url, $praxis_id, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Praxisdaten erfolgreich aktualisiert!';
            // Praxisdaten neu laden
            $sql = "SELECT * FROM praxen WHERE id = ? AND owner_id = ?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ii", $praxis_id, $user_id);
            $stmt2->execute();
            $praxis = $stmt2->get_result()->fetch_assoc();
            $stmt2->close();
        } else {
            $error = 'Fehler beim Aktualisieren der Praxisdaten: ' . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($praxis['name']); ?> bearbeiten - Termin2Praxis</title>
    <link rel="icon" type="image/svg+xml" href="assets/T2P_transparent_2.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="dashboard_praxisbesitzer.php">
                <img src="assets/T2P_transparent_2.svg" alt="T2P Logo" style="height: 45px; margin-right: 10px;">
                <span>← Zurück zu Meine Praxen</span>
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

    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Praxis bearbeiten</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Erfolg!</strong> <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Fehler!</strong> <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Grundinformationen</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Praxisname <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($praxis['name']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="beschreibung" class="form-label">Beschreibung</label>
                                    <textarea class="form-control" id="beschreibung" name="beschreibung" rows="4" 
                                              placeholder="Beschreiben Sie Ihre Praxis..."><?php echo htmlspecialchars($praxis['beschreibung'] ?? ''); ?></textarea>
                                    <small class="text-muted">Eine aussagekräftige Beschreibung hilft Patienten, Ihre Praxis besser kennenzulernen.</small>
                                </div>

                                <div class="mb-3">
                                    <label for="bild_url" class="form-label">Bild-URL</label>
                                    <input type="url" class="form-control" id="bild_url" name="bild_url" 
                                           value="<?php echo htmlspecialchars($praxis['bild_url'] ?? ''); ?>"
                                           placeholder="https://beispiel.de/bild.jpg">
                                    <small class="text-muted">URL zu einem Bild Ihrer Praxis (optional)</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Kategorisierung</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="spezialgebiet" class="form-label">Spezialgebiet</label>
                                        <input type="text" class="form-control" id="spezialgebiet" name="spezialgebiet" 
                                               value="<?php echo htmlspecialchars($praxis['spezialgebiet'] ?? ''); ?>"
                                               placeholder="z.B. Allgemeinmedizin, Orthopädie">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="kategorie" class="form-label">Kategorie</label>
                                        <select class="form-select" id="kategorie" name="kategorie">
                                            <option value="">Bitte wählen</option>
                                            <option value="Allgemeinmedizin" <?php echo ($praxis['kategorie'] ?? '') === 'Allgemeinmedizin' ? 'selected' : ''; ?>>Allgemeinmedizin</option>
                                            <option value="Facharzt" <?php echo ($praxis['kategorie'] ?? '') === 'Facharzt' ? 'selected' : ''; ?>>Facharzt</option>
                                            <option value="Zahnarzt" <?php echo ($praxis['kategorie'] ?? '') === 'Zahnarzt' ? 'selected' : ''; ?>>Zahnarzt</option>
                                            <option value="Psychotherapie" <?php echo ($praxis['kategorie'] ?? '') === 'Psychotherapie' ? 'selected' : ''; ?>>Psychotherapie</option>
                                            <option value="Physiotherapie" <?php echo ($praxis['kategorie'] ?? '') === 'Physiotherapie' ? 'selected' : ''; ?>>Physiotherapie</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="border-bottom pb-2">Kontaktdaten</h5>
                                
                                <div class="mb-3">
                                    <label for="adresse" class="form-label">Straße und Hausnummer</label>
                                    <input type="text" class="form-control" id="adresse" name="adresse" 
                                           value="<?php echo htmlspecialchars($praxis['adresse'] ?? ''); ?>"
                                           placeholder="Hauptstraße 123">
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="plz" class="form-label">PLZ</label>
                                        <input type="text" class="form-control" id="plz" name="plz" 
                                               value="<?php echo htmlspecialchars($praxis['plz'] ?? ''); ?>"
                                               placeholder="12345">
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="stadt" class="form-label">Stadt</label>
                                        <input type="text" class="form-control" id="stadt" name="stadt" 
                                               value="<?php echo htmlspecialchars($praxis['stadt'] ?? ''); ?>"
                                               placeholder="Berlin">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telefon" class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" id="telefon" name="telefon" 
                                               value="<?php echo htmlspecialchars($praxis['telefon'] ?? ''); ?>"
                                               placeholder="+49 30 12345678">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">E-Mail</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($praxis['email'] ?? ''); ?>"
                                               placeholder="info@praxis.de">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <a href="dashboard_praxisbesitzer.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Abbrechen
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Änderungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Vorschau Card -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Vorschau (so sehen Patienten Ihre Praxis)</h5>
                    </div>
                    <div class="card-body">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($praxis['bild_url'] ?? 'https://via.placeholder.com/400x300?text=Arztpraxis'); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($praxis['name']); ?>"
                                 style="max-height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($praxis['name']); ?></h5>
                                
                                <?php if (!empty($praxis['spezialgebiet'])): ?>
                                    <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($praxis['spezialgebiet']); ?></span>
                                <?php endif; ?>
                                
                                <?php if (!empty($praxis['kategorie'])): ?>
                                    <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($praxis['kategorie']); ?></span>
                                <?php endif; ?>
                                
                                <p class="card-text"><?php echo htmlspecialchars($praxis['beschreibung'] ?? 'Keine Beschreibung vorhanden'); ?></p>
                                
                                <hr>
                                
                                <p class="mb-1"><strong>Adresse:</strong> 
                                    <?php 
                                    $adresse_teile = [];
                                    if (!empty($praxis['adresse'])) $adresse_teile[] = $praxis['adresse'];
                                    if (!empty($praxis['plz']) || !empty($praxis['stadt'])) {
                                        $ort = trim(($praxis['plz'] ?? '') . ' ' . ($praxis['stadt'] ?? ''));
                                        if ($ort) $adresse_teile[] = $ort;
                                    }
                                    echo !empty($adresse_teile) ? htmlspecialchars(implode(', ', $adresse_teile)) : 'Keine Adresse angegeben';
                                    ?>
                                </p>
                                
                                <?php if (!empty($praxis['telefon'])): ?>
                                    <p class="mb-1"><strong>Telefon:</strong> <?php echo htmlspecialchars($praxis['telefon']); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($praxis['email'])): ?>
                                    <p class="mb-1"><strong>E-Mail:</strong> <?php echo htmlspecialchars($praxis['email']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
