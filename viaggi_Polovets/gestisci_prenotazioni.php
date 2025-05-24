<?php
require_once 'header.php';
checkPageAccess('user');

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mail = $_SESSION['mail'];

$sql = "SELECT p.*, v.Nome_viaggio, v.Destinazione, u.Nome, u.Cognome, pac.prezzo 
        FROM prenotazioni p, viaggi v, utenti u, pacchetti pac 
        WHERE p.Codice_viaggio = v.Codice_viaggio 
        AND p.Mail = u.Mail 
        AND pac.Codice_pacchetto = v.Codice_pacchetto
        AND p.Mail = ?
        ORDER BY p.Data_partenza DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("s", $mail);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prenotazioni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestione Prenotazioni</h1>
            <a href="utenti.php" class="btn btn-outline-primary">Torna alla Home</a>
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
                        <div class="card h-100 shadow">
                            <div class="status-badge">
                                <span class="badge <?php echo $row['Stato'] == 'Pagato' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo ($row['Stato']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ($row['Nome_viaggio']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo ($row['Destinazione']); ?></h6>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Dettagli prenotazione:</small>
                                    <ul class="list-unstyled">
                                        <li><strong>Data partenza:</strong> <?php echo ($row['Data_partenza']); ?></li>
                                        <li><strong>Data ritorno:</strong> <?php echo ($row['Data_ritorno']); ?></li>
                                        <li><strong>Prezzo:</strong> €<?php echo number_format($row['prezzo'], 2); ?></li>
                                    </ul>
                                </div>

                                <?php if ($row['Stato'] == 'In attesa'): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <small>Questa prenotazione richiede il pagamento per essere confermata.</small>
                                    </div>
                                    <div class="d-grid">
                                        <a href="gestisci_pagamento.php?codpr=<?php echo $row['Cod_prenotazione']; ?>" 
                                           class="btn btn-primary">Procedi al pagamento</a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-success" role="alert">
                                        <small>Prenotazione confermata e pagata</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Prenotazione #<?php echo $row['Cod_prenotazione']; ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
