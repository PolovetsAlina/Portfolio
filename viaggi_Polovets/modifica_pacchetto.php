<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viaggi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['Codice_pacchetto'])) {
    $codice_pacchetto = $_GET['Codice_pacchetto'];
    $sql = "SELECT * FROM pacchetti WHERE Codice_pacchetto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codice_pacchetto);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $destinazione = $row['destinazione'];
        $descrizione = $row['descrizione'];
        $prezzo = $row['prezzo'];
        $durata = $row['durata'];
        $immagine = $row['immagine'];
    } else {
        die("Pacchetto non trovato");
    }
    $stmt->close();
} else {
    die("Codice pacchetto non specificato");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinazione = $_POST['destinazione'];
    $descrizione = $_POST['descrizione'];
    $prezzo = $_POST['prezzo'];
    $durata = $_POST['durata'];
    $sql = "UPDATE pacchetti SET destinazione=?, descrizione=?, prezzo=?, durata=? WHERE Codice_pacchetto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $destinazione, $descrizione, $prezzo, $durata, $codice_pacchetto);
    if ($stmt->execute()) {
        header("Location: visualizza_pacchetto.php");
        exit();
    } else {
        $error = "Errore durante la modifica: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Pacchetto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Modifica Pacchetto</h1>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <form method="POST">
            <div class="mb-3">
                <label for="destinazione" class="form-label">Destinazione</label>
                <input type="text" class="form-control" id="destinazione" name="destinazione" value="<?php echo htmlspecialchars($destinazione); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descrizione" class="form-label">Descrizione</label>
                <textarea class="form-control" id="descrizione" name="descrizione" required><?php echo htmlspecialchars($descrizione); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="prezzo" class="form-label">Prezzo</label>
                <input type="number" step="0.01" class="form-control" id="prezzo" name="prezzo" value="<?php echo htmlspecialchars($prezzo); ?>" required>
            </div>
            <div class="mb-3">
                <label for="durata" class="form-label">Durata</label>
                <input type="text" class="form-control" id="durata" name="durata" value="<?php echo htmlspecialchars($durata); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Salva modifiche</button>
            <a href="visualizza_pacchetto.php" class="btn btn-secondary">Annulla</a>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>
