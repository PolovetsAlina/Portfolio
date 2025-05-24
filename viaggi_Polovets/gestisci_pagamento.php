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

if (isset($_GET['codpr'])) {
    $cod_prenotazione = intval($_GET['codpr']);
    $mail = $_SESSION['mail'];
    val($_GET['codpr']);
    $cod_prenotazione = intval($_GET['codpr']);
    $mail = isset($_SESSION['mail']) ? $_SESSION['mail'] : '';

    $sql_check = "SELECT p.*, v.Codice_viaggio, pac.prezzo 
                  FROM prenotazioni p, viaggi v, pacchetti pac 
                  WHERE v.Codice_viaggio = p.Codice_viaggio 
                  AND pac.Codice_pacchetto = v.Codice_pacchetto 
                  AND p.Cod_prenotazione = $cod_prenotazione AND p.Mail = '$mail'";
    $result = $conn->query($sql_check);

    if ($result && $result->num_rows > 0) {
        $prenotazione = $result->fetch_assoc();
        
        if ($prenotazione['Stato'] == 'Pagato') {
            echo "<div class='alert alert-info text-center'>Questa prenotazione è già stata pagata.</div>";
            echo "<div class='text-center'><a href='visualizza_prenotazioni_ut.php' class='btn btn-primary'>Torna alle prenotazioni</a></div>";
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conferma_pagamento'])) {
            $codice_pagamento = uniqid();
            $metodo_pagamento = $conn->real_escape_string($_POST['metodo_pagamento']);
            $data_pagamento = date('Y-m-d');

            $sql_pagamento = "INSERT INTO pagamenti (Codice_pagamento, Mail, Importo, Data_pagamento, Metodo_pagamento, Stato) 
                              VALUES ('$codice_pagamento', '$mail', {$prenotazione['prezzo']}, '$data_pagamento', '$metodo_pagamento', 'Pagato')";

            if ($conn->query($sql_pagamento)) {
                $sql_update = "UPDATE prenotazioni 
                              SET Stato = 'Pagato', 
                                  Codice_pagamento = '$codice_pagamento' 
                              WHERE Cod_prenotazione = $cod_prenotazione";
                
                if ($conn->query($sql_update)) {
                    echo "<div class='alert alert-success text-center'>Pagamento completato con successo!</div>";
                    echo "<div class='text-center'><a href='visualizza_prenotazioni_ut.php' class='btn btn-primary'>Torna alle prenotazioni</a></div>";
                    exit();
                }
            }
            echo "<div class='alert alert-danger text-center'>Errore durante il pagamento. Riprova.</div>";
        }
        ?>
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pagamento Prenotazione</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title mb-0">Conferma Pagamento</h3>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <h5>Dettagli Prenotazione:</h5>
                                    <p><strong>Destinazione:</strong> <?php echo $prenotazione['Destinazione']; ?></p>
                                    <p><strong>Data Partenza:</strong> <?php echo $prenotazione['Data_partenza']; ?></p>
                                    <p><strong>Data Ritorno:</strong> <?php echo $prenotazione['Data_ritorno']; ?></p>
                                    <p><strong>Importo da pagare:</strong> €<?php echo number_format($prenotazione['prezzo'], 2); ?></p>
                                </div>

                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="metodo_pagamento" class="form-label">Metodo di Pagamento</label>
                                        <select class="form-select" id="metodo_pagamento" name="metodo_pagamento" required>
                                            <option value="">Seleziona metodo di pagamento</option>
                                            <option value="Carta">Carta di Credito/Debito</option>
                                            <option value="PayPal">PayPal</option>
                                            <option value="Bonifico">Bonifico Bancario</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="conferma_pagamento" class="btn btn-primary">Conferma Pagamento</button>
                                        <a href="visualizza_prenotazioni_ut.php" class="btn btn-secondary">Annulla</a>
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
    } else {
        echo "<div class='alert alert-danger text-center'>Prenotazione non trovata o non autorizzata.</div>";
        echo "<div class='text-center'><a href='visualizza_prenotazioni_ut.php' class='btn btn-primary'>Torna alle prenotazioni</a></div>";
    }
} else {
    echo "<div class='alert alert-danger text-center'>Parametri mancanti.</div>";
    echo "<div class='text-center'><a href='visualizza_prenotazioni_ut.php' class='btn btn-primary'>Torna alle prenotazioni</a></div>";
}
?>

