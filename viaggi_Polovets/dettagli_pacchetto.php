<?php 
header('Content-Type: text/html; charset=UTF-8');
require_once 'header.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

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
    if ($user_role === 'admin') {
        header('Location: gestisci_viaggi.php');
        exit();
    }
    
    echo '<div class="text-center"><a href="proposte.php" class="btn btn-secondary mt-3">Torna indietro</a></div>';
    exit();
}

$codice_viaggio = $conn->real_escape_string($_GET['Codice_viaggio']);

$sql = "SELECT p.destinazione, p.prezzo,
               a.Nome as alloggio, 
               g.Cognome as guida_cognome, 
               g.Nome as guida_nome,
               g.Lingua as lingua,
               t.Tipo as tipo_trasporto,
               v.Data_partenza,
               v.Data_ritorno
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prenota'])) {
    if (!isset($_SESSION['mail'])) {
        header('Location: registrazione.php');
        exit();
    }

    $mail = $_SESSION['mail'];
    $data_partenza = $pacchetto['Data_partenza'];
    $data_ritorno = $pacchetto['Data_ritorno'];
    $destinazione = $pacchetto['destinazione'];
    $stato = 'In attesa';
    $codice_viaggio = $_GET['Codice_viaggio'];

    $check_sql = "SELECT * FROM prenotazioni WHERE Mail = '$mail' AND Destinazione = '$destinazione' AND Data_partenza = '$data_partenza'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $errore_prenotazione = 'Hai già una prenotazione per questo viaggio.';
    } else {
        $importo = $pacchetto['prezzo'];
        
        if ($importo) {
        
        $data_prenotazione = date('Y-m-d');
        $codice_pagamento = uniqid();
        
        $sql = "INSERT INTO prenotazioni (Mail, Codice_viaggio, Data_partenza, Data_ritorno, Stato, Destinazione) 
                VALUES ('$mail', '$codice_viaggio', '$data_partenza', '$data_ritorno', 'In attesa', '$destinazione')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success text-center'>";
                echo "Prenotazione registrata con successo! ";
                echo "<a href='visualizza_prenotazioni_ut.php' class='alert-link'>Vai alle tue prenotazioni</a> per procedere al pagamento.";
                echo "</div>";
            } else {
                echo "<div class='alert alert-danger'>Errore nell'inserimento della prenotazione: " . $conn->error . "</div>";
            }
        } else {
            echo "Errore: prezzo del viaggio non trovato.";
        }
    }
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
        <h1 class="mb-4 text-center">Prenota un viaggio</h1>
        <?php if (isset($errore_prenotazione)) { echo '<div class="alert alert-danger">'.$errore_prenotazione.'</div>'; } ?>
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
                            <li class="list-group-item"><strong>Data Partenza:</strong> <?php echo isset($pacchetto['Data_partenza']) ? $pacchetto['Data_partenza'] : 'Non specificata'; ?></li>
                            <li class="list-group-item"><strong>Data Ritorno:</strong> <?php echo isset($pacchetto['Data_ritorno']) ? $pacchetto['Data_ritorno'] : 'Non specificata'; ?></li>
                        </ul>
                        <form method="POST" action="">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="data_partenza">Data Partenza</label>
                                    <input type="date" class="form-control" name="data_partenza" id="data_partenza" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="data_ritorno">Data Ritorno</label>
                                    <input type="date" class="form-control" name="data_ritorno" id="data_ritorno" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <button type="submit" name="prenota" class="btn btn-success w-100">Prenota ora</button>
                        </form>
                    </div>
                </div>
                <div class="text-center">
                    <?php
                    if ($user_role === 'admin') {
                        header('Location: gestisci_viaggi.php');
                        exit();
                    }
                    ?>
                    <a href="proposte.php" class="btn btn-secondary">Torna indietro</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

