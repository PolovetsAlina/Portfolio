<?php 
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die('Connessione fallita: ' . $conn->connect_error);
}

if (!isset($_GET['Codice_viaggio'])) {
    echo '<div class="alert alert-danger text-center mt-5">Viaggio non trovato: parametro mancante.</div>';
    echo '<div class="text-center"><a href="utenti.php" class="btn btn-secondary mt-3">Torna indietro</a></div>';
    exit();
}

$codice_viaggio = $conn->real_escape_string($_GET['Codice_viaggio']);

$sql = "SELECT p.destinazione, p.prezzo,
               a.Nome AS alloggio, 
               g.Cognome AS guida_cognome, 
               g.Nome AS guida_nome,
               g.Lingua AS lingua,
               t.Tipo AS tipo_trasporto
        FROM viaggi v, pacchetti p, alloggi a, guide g, trasporti t
        WHERE v.Codice_viaggio = '$codice_viaggio'
        AND v.Codice_pacchetto = p.Codice_pacchetto
        AND p.Codice_alloggio = a.Codice_alloggio
        AND p.Codice_guida = g.Codice_guida
        AND p.Codice_trasporto = t.Codice_trasporto";

$result = $conn->query($sql);

if (!$result) {
    die("Errore nella query SQL: " . $conn->error);
}

$pacchetto = $result->fetch_assoc();

if (!$pacchetto) {
    echo '<div class="alert alert-danger text-center mt-5">Pacchetto non trovato.</div>';
    echo '<div class="text-center"><a href="utenti.php" class="btn btn-secondary mt-3">Torna indietro</a></div>';
    exit();
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Pacchetto - <?php echo($pacchetto['destinazione']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            max-height: 200px;
            object-fit: cover;
        }
        .card {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center"><?php echo ($pacchetto['destinazione']); ?></h3>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item"><strong>Destinazione:</strong> <?php echo ($pacchetto['destinazione']); ?></li>
                            <li class="list-group-item"><strong>Prezzo:</strong> <?php echo ($pacchetto['prezzo']); ?> €</li>                                       
                            <li class="list-group-item"><strong>Alloggio:</strong> <?php echo ($pacchetto['alloggio']); ?></li>
                            <li class="list-group-item"><strong>Trasporto:</strong> <?php echo ($pacchetto['tipo_trasporto']); ?></li>
                            <li class="list-group-item"><strong>Guida:</strong> <?php echo ($pacchetto['guida_nome'] . ' ' . $pacchetto['guida_cognome']); ?></li>    
                            <li class="list-group-item"><strong>Lingua:</strong> <?php echo ($pacchetto['lingua']); ?></li>         
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="utenti.php" class="btn btn-secondary">Torna indietro</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

