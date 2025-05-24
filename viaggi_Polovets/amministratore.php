<?php
require_once 'header.php';
checkPageAccess('admin');

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viaggi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amministratore - Viaggi</title>
    <link rel="stylesheet" href="style/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 bg-dark text-white">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="index.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">Amministratore</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start">
                        <li class="nav-item">
                            <a href="visualizza_prenotazioni.php" class="nav-link text-white">
                                <i class="fas fa-calendar-check"></i>
                                <span class="ms-1 d-none d-sm-inline">Prenotazioni</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="visualizza_pagamenti.php" class="nav-link text-white">
                                <i class="fas fa-money-bill-wave"></i>
                                <span class="ms-1 d-none d-sm-inline">Pagamenti</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="gestisci_utenti.php" class="nav-link text-white">
                                <i class="fas fa-users"></i>
                                <span class="ms-1 d-none d-sm-inline">Utenti</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="aggiungi_pacchetto.php" class="nav-link text-white">
                                <i class="fas fa-plus-circle"></i>
                                <span class="ms-1 d-none d-sm-inline">Nuovo Pacchetto</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="aggiungi_viaggio.php" class="nav-link text-white">
                                <i class="fas fa-plane"></i>
                                <span class="ms-1 d-none d-sm-inline">Nuovo Viaggio</span>
                            </a>
                        </li>
                      
                    </ul>
                    <hr class="text-white">
                    <div class="dropdown pb-4">
                        <a href="?logout=1" class="nav-link text-white">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="ms-1 d-none d-sm-inline">Esci</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="mt-4">Benvenuto, <?php echo getUserName(); ?></h1>
                            <p class="lead">Seleziona una sezione per gestire i dati del sito.</p>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Prenotazioni Totali</h5>
                                    <p class="card-text display-4">
                                        <?php
                                        $sql_prenotazioni = "SELECT COUNT(*) as totale FROM prenotazioni";
                                        $result_prenotazioni = $conn->query($sql_prenotazioni);
                                        $prenotazioni = $result_prenotazioni->fetch_assoc();
                                        echo $prenotazioni['totale'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Utenti Registrati</h5>
                                    <p class="card-text display-4">
                                        <?php
                                        $sql_utenti = "SELECT COUNT(*) as totale FROM utenti";
                                        $result_utenti = $conn->query($sql_utenti);
                                        $utenti = $result_utenti->fetch_assoc();
                                        echo $utenti['totale'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Pacchetti Disponibili</h5>
                                    <p class="card-text display-4">
                                        <?php
                                        $sql_pacchetti = "SELECT COUNT(*) as totale FROM pacchetti";
                                        $result_pacchetti = $conn->query($sql_pacchetti);
                                        $pacchetti = $result_pacchetti->fetch_assoc();
                                        echo $pacchetti['totale'];
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-4">Pacchetti Viaggio</h2>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Destinazione</th>
                                            <th>Prezzo</th>
                                            <th>Azioni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT pacchetti.Codice_pacchetto, pacchetti.destinazione, pacchetti.prezzo, pacchetti.immagine
                                                FROM pacchetti";
                                        $result = $conn->query($sql);

                                        if ($result && $result->num_rows > 0) {
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . ($row['destinazione']) . "</td>";
                                                $prezzo = preg_replace('/[^0-9.]/', '', $row['prezzo']);
                                                $prezzo_formattato = number_format($prezzo, 2, ',', '.');
                                                echo "<td>" . $prezzo_formattato . " &#x20AC;</td>";
                                                echo "<td>";
                                                echo "<a href='modifica_pacchetto.php?cod=" . htmlspecialchars($row['Codice_pacchetto']) . "' class='btn btn-primary btn-sm'>Modifica</a>";
                                                echo "<form action='elimina_pacchetto.php' method='POST' class='d-inline' onsubmit='return confirm(\"Sei sicuro di voler eliminare questo pacchetto?\")'>";
                                                echo "<input type='hidden' name='cod' value='" . htmlspecialchars($row['Codice_pacchetto']) . "'>";
                                                echo "<button type='submit' class='btn btn-danger btn-sm'>Elimina</button>";
                                                echo "</form>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-code.js"></script>
</body>
</html>

<?php
$conn->close();
?>
