<?php
session_start();

if (!isset($_SESSION['mail'])) {
    header('Location: login.php');
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mail = $_SESSION['mail'];

try {
    $sql = "SELECT p.*, v.Codice_viaggio, pac.destinazione, pac.prezzo 
            FROM prenotazioni p, viaggi v, pacchetti pac 
            WHERE p.Codice_viaggio = v.Codice_viaggio 
            AND v.Codice_pacchetto = pac.Codice_pacchetto 
            AND p.Mail = '$mail' 
            ORDER BY p.Data_partenza DESC";
            
    if (!($result = $conn->query($sql))) {
        throw new Exception("Errore nella query: " . $conn->error);
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Mie Prenotazioni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Le Mie Prenotazioni</h1>
        </div>
        
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">Nessuna prenotazione trovata!</h4>
                <p>Non hai ancora effettuato nessuna prenotazione. Esplora i nostri pacchetti viaggio e prenota la tua prossima avventura!</p>
                <hr>
                <a href="pacchetti.php" class="btn btn-primary">Scopri i nostri pacchetti</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($row['destinazione']); ?></h5>
                                    <span class="badge bg-secondary">
                                        <?php echo $row['Cod_prenotazione']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-3">
                                    <li><strong>Data partenza:</strong> <?php echo date('d/m/Y', strtotime($row['Data_partenza'])); ?></li>
                                    <li><strong>Data ritorno:</strong> <?php echo date('d/m/Y', strtotime($row['Data_ritorno'])); ?></li>
                                    <li><strong>Stato:</strong> 
                                        <span class="badge <?php echo $row['Stato'] == 'Pagato' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo $row['Stato'] == 'Pagato' ? 'Pagato' : 'In attesa'; ?>
                                        </span>
                                    </li>
                                </ul>
                                
                                <div class="d-grid gap-2">
                                    <?php if ($row['Stato'] == 'In attesa'): ?>
                                        <a href="gestisci_pagamento.php?codpr=<?php echo ($row['Cod_prenotazione']); ?>" 
                                           class="btn btn-primary">Procedi al pagamento</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>            </div>
        <?php endif; ?>  
        <a href="utenti.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>";
        echo "<h4 class='alert-heading'>Errore!</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<hr>";
        echo "<a href='utenti.php' class='btn btn-primary'>Torna alla Home</a>";
        echo "</div>";
    }
    
    $conn->close();
?>
