// JavaScript für Arzt Dashboard

// Neuen Termin erstellen
document.getElementById('createAppointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const messageDiv = document.getElementById('createMessage');
    
    const formData = new FormData();
    formData.append('date', date);
    formData.append('time', time);
    
    fetch('api/create_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            // Formular zurücksetzen
            document.getElementById('createAppointmentForm').reset();
            // Seite nach 2 Sekunden neu laden
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        messageDiv.innerHTML = `<div class="alert alert-danger">Fehler beim Erstellen des Termins</div>`;
        console.error('Error:', error);
    });
});

// Termin bestätigen
function confirmAppointment(appointmentId) {
    if (!confirm('Möchten Sie diesen Termin wirklich bestätigen?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('appointment_id', appointmentId);
    
    fetch('api/confirm_appointment.php', {
        method: 'POST',
        body: formData
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
        alert('Fehler beim Bestätigen des Termins');
        console.error('Error:', error);
    });
}

// Datum-Feld auf heute als Minimum setzen
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
});

// Termin ablehnen
function rejectAppointment(appointmentId) {
    if (!confirm('Möchten Sie diesen Termin wirklich ablehnen und löschen?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('appointment_id', appointmentId);
    formData.append('action', 'reject');
    
    fetch('api/delete_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Zeile sofort aus der Tabelle entfernen
            const row = document.getElementById('termin-' + appointmentId);
            if (row) {
                row.remove();
            }
            alert(data.message);
            // Seite neu laden, um Zähler zu aktualisieren
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Fehler beim Ablehnen des Termins');
        console.error('Error:', error);
    });
}

// Termin löschen
function deleteAppointment(appointmentId) {
    if (!confirm('Möchten Sie diesen Termin wirklich stornieren und löschen?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('appointment_id', appointmentId);
    formData.append('action', 'delete');
    
    fetch('api/delete_appointment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Zeile sofort aus der Tabelle entfernen
            const row = document.getElementById('termin-' + appointmentId);
            if (row) {
                row.remove();
            }
            alert(data.message);
            // Seite neu laden, um Zähler zu aktualisieren
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        alert('Fehler beim Löschen des Termins');
        console.error('Error:', error);
    });
}

// Toggle für vergangene Termine
function toggleVergangeneTermine() {
    const vergangeneDiv = document.getElementById('vergangeneTermine');
    const toggleBtn = document.getElementById('toggleBtn');
    
    if (vergangeneDiv.style.display === 'none') {
        vergangeneDiv.style.display = 'block';
        toggleBtn.innerHTML = '- Vergangene Termine ausblenden';
    } else {
        vergangeneDiv.style.display = 'none';
        const count = document.querySelectorAll('#vergangeneTermine tbody tr').length;
        toggleBtn.innerHTML = '+ ' + count + ' vergangene Termine anzeigen';
    }
}
