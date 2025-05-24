<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    $sql = "SELECT p.*, u.Mail, u.Nome, u.Cognome FROM prenotazioni p, utenti u WHERE u.Mail = p.Mail";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    if ($result->num_rows === 0) {
        echo "<div class='alert alert-info'>Nessuna prenotazione trovata.</div>";
    } else {
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Prenotazioni - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Prenotazioni</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Prenotazione</th>
                        <th>Mail</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Destinazione</th>
                        <th>Data Partenza</th>
                        <th>Data Ritorno</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Cod_prenotazione']; ?></td>
                        <td><?php echo $row['Mail']; ?></td>
                        <td><?php echo $row['Nome']; ?></td>
                        <td><?php echo $row['Cognome']; ?></td>
                        <td><?php echo $row['Destinazione']; ?></td>
                        <td><?php echo $row['Data_partenza']; ?></td>
                        <td><?php echo $row['Data_ritorno']; ?></td>
                        <td><?php echo $row['Stato']; ?></td>
                        <td>
                            <a href="modifica_prenotazione.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Modifica</a>
                            <a href="elimina_prenotazione.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questa prenotazione?')">Elimina</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <a href="amministratore.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
</body>
</html>
<?php } 
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Errore: " . $e->getMessage() . "</div>";
    echo "<a href='amministratore.php' class='btn btn-secondary mt-3'>Torna indietro</a>";
}
?>
