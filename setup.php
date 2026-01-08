<?php
require_once 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    // Alte Benutzer löschen
    $conn->query("DELETE FROM users");
    
    // Passwort im Klartext (nur für Demo/Schulprojekt!)
    $password = 'password123';
    
    // Neue Benutzer erstellen
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    
    // Arzt
    $name = 'Dr Albrecht';
    $email = 'arzt@termin2praxis.de';
    $role = 'arzt';
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
    
    // Patient
    $name = 'Max Mustermann';
    $email = 'patient@termin2praxis.de';
    $role = 'patient';
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    $message = 'Setup erfolgreich! Sie können sich jetzt einloggen.';
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Termin2Praxis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Termin2Praxis Setup</h2>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                                <br><br>
                                <a href="login.php" class="btn btn-primary">Zum Login</a>
                            </div>
                        <?php else: ?>
                            <p>Klicken Sie auf den Button, um die Demo-Benutzer zu erstellen:</p>
                            <form method="POST">
                                <button type="submit" class="btn btn-primary w-100">Setup starten</button>
                            </form>
                            
                            <div class="mt-4">
                                <small class="text-muted">
                                    Dies erstellt folgende Accounts:<br>
                                    - Arzt: arzt@termin2praxis.de<br>
                                    - Patient: patient@termin2praxis.de<br>
                                    Passwort für beide: password123
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
