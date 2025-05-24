<?php
require_once 'header.php';
require_once 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: gestisci_prenotazioni.php');
    exit();
}

$prenotazione_id = intval($_GET['id']);

$sql = "SELECT * FROM prenotazioni WHERE Cod_prenotazione = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prenotazione_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: gestisci_prenotazioni.php');
    exit();
}

$prenotazione = $result->fetch_assoc();
$stmt->close();

$sql_viaggio = "SELECT Nome_viaggio, Destinazione FROM viaggi WHERE Codice_viaggio = ?";
$stmt_viaggio = $conn->prepare($sql_viaggio);
$stmt_viaggio->bind_param("s", $prenotazione['Codice_viaggio']);
$stmt_viaggio->execute();
$result_viaggio = $stmt_viaggio->get_result();

if ($result_viaggio->num_rows > 0) {
    $viaggio = $result_viaggio->fetch_assoc();
    $prenotazione['Nome_viaggio'] = $viaggio['Nome_viaggio'];
    $prenotazione['Destinazione'] = $viaggio['Destinazione'];
}
$stmt_viaggio->close();

$sql_pacchetto = "SELECT prezzo FROM pacchetti WHERE Codice_pacchetto = ?";
$stmt_pacchetto = $conn->prepare($sql_pacchetto);
$stmt_pacchetto->bind_param("s", $prenotazione['Codice_pacchetto']);
$stmt_pacchetto->execute();
$result_pacchetto = $stmt_pacchetto->get_result();

if ($result_pacchetto->num_rows > 0) {
    $pacchetto = $result_pacchetto->fetch_assoc();
    $prenotazione['prezzo'] = $pacchetto['prezzo'];
}
$stmt_pacchetto->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_partenza = $conn->real_escape_string($_POST['data_partenza']);
    $data_ritorno = $conn->real_escape_string($_POST['data_ritorno']);
    $stato = $conn->real_escape_string($_POST['stato']);
    
    $sql_update = "UPDATE prenotazioni 
                   SET Data_partenza = ?, 
                       Data_ritorno = ?, 
                       Stato = ? 
                   WHERE Cod_prenotazione = ?";
    
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $data_partenza, $data_ritorno, $stato, $prenotazione_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = "Prenotazione modificata con successo!";
    } else {
        $_SESSION['error_message'] = "Errore durante la modifica della prenotazione: " . $conn->error;
    }
    
    $stmt_update->close();
    header('Location: gestisci_prenotazioni.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Prenotazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">Modifica Prenotazione</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="destinazione" class="form-label">Destinazione</label>
                                <input type="text" class="form-control" id="destinazione" 
                                       value="<?php echo htmlspecialchars($prenotazione['Destinazione']); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label for="data_partenza" class="form-label">Data Partenza</label>
                                <input type="date" class="form-control" id="data_partenza" name="data_partenza" 
                                       value="<?php echo $prenotazione['Data_partenza']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="data_ritorno" class="form-label">Data Ritorno</label>
                                <input type="date" class="form-control" id="data_ritorno" name="data_ritorno" 
                                       value="<?php echo $prenotazione['Data_ritorno']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="stato" class="form-label">Stato</label>
                                <select class="form-select" id="stato" name="stato" required>
                                    <option value="In attesa" <?php echo $prenotazione['Stato'] == 'In attesa' ? 'selected' : ''; ?>>In attesa</option>
                                    <option value="Pagato" <?php echo $prenotazione['Stato'] == 'Pagato' ? 'selected' : ''; ?>>Pagato</option>
                                    <option value="Annullato" <?php echo $prenotazione['Stato'] == 'Annullato' ? 'selected' : ''; ?>>Annullato</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="prezzo" class="form-label">Prezzo</label>
                                <input type="text" class="form-control" id="prezzo" 
                                       value="€<?php echo number_format($prenotazione['prezzo'], 2); ?>" 
                                       readonly>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                                <a href="gestisci_prenotazioni.php" class="btn btn-secondary">Annulla</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>