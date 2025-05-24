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
    $sql = "DELETE FROM pacchetti WHERE Codice_pacchetto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codice_pacchetto);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: visualizza_pacchetto.php');
        exit();
    } else {
        $error = "Errore durante l'eliminazione: " . $conn->error;
    }
    $stmt->close();
} else {
    $error = "Codice pacchetto non specificato.";
}
$conn->close();
if (isset($error)) {
    echo "<div class='alert alert-danger'>" . $error . "</div>";
    echo "<a href='visualizza_pacchetto.php' class='btn btn-secondary mt-3'>Torna indietro</a>";
}
?>
