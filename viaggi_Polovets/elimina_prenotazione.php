<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || empty($_POST['id'])) {
    header('Location: visualizza_prenotazioni.php');
    exit();
}

$booking_id = $_POST['id'];


$sql = "DELETE FROM prenotazioni WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Prenotazione eliminata con successo!";
} else {
    $_SESSION['error_message'] = "Errore durante l'eliminazione della prenotazione: " . $conn->error;
}

$stmt->close();
$conn->close();

header('Location: visualizza_prenotazioni.php');
exit();
?>