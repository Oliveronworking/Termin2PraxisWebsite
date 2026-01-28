<?php
require_once 'config.php';

$conn = getDBConnection();

// SQL-Datei einlesen
$sql = file_get_contents('update_database_praxen.sql');

// SQL-Befehle in einzelne Statements aufteilen
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
$errors = [];

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    if (!$conn->query($statement)) {
        $success = false;
        $errors[] = "Fehler: " . $conn->error . " bei Statement: " . substr($statement, 0, 100);
    }
}

$conn->close();

if ($success) {
    echo "✅ Datenbank erfolgreich aktualisiert!\n\n";
    echo "Folgende Änderungen wurden vorgenommen:\n";
    echo "- Tabelle 'praxen' erstellt\n";
    echo "- Spalte 'praxis_id' zu 'users' hinzugefügt\n";
    echo "- Spalte 'praxis_id' zu 'appointments' hinzugefügt\n";
    echo "- 3 Beispiel-Arztpraxen eingefügt\n";
    echo "- Bestehende Daten mit Praxis-IDs verknüpft\n\n";
    echo "Du kannst jetzt die neue Ansicht auf index.php testen!\n";
} else {
    echo "❌ Fehler beim Aktualisieren der Datenbank:\n\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}
?>
