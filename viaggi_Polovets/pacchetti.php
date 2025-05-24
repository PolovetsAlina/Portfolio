<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

try {
    $sql = "SELECT * FROM pacchetti ORDER BY prezzo";
    if (!($result = $conn->query($sql))) {
        throw new Exception("Errore nella query: " . $conn->error);
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacchetti Viaggio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Agenzia Viaggi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pacchetti.php">Pacchetti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="proposte.php">Proposte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contatti.php">Contatti</a>
                    </li>
                    <?php if (isset($_SESSION['mail'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="utenti.php">Area Personale</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Accedi</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="py-5 text-center" style="background-image: url('https://source.unsplash.com/1600x400/?travel');">
        <div class="container">
            <h1 class="display-4">I Nostri Pacchetti Viaggio</h1>
            <p class="lead">Scopri le nostre destinazioni esclusive e inizia la tua prossima avventura</p>
        </div>
    </header>

    <section class="section">
        <div class="container">
            <div class="row g-4">
                <?php while ($pacchetto = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="https://source.unsplash.com/400x300/?<?php echo urlencode($pacchetto['destinazione']); ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($pacchetto['destinazione']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pacchetto['destinazione']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($pacchetto['descrizione'] ?? 'Scopri questa meravigliosa destinazione!'); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0">€<?php echo number_format($pacchetto['prezzo'], 2); ?></span>
                                <a href="dettaglio_pacchetto.php?id=<?php echo $pacchetto['Codice_pacchetto']; ?>" 
                                   class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } catch (Exception $e) {
        echo "<div class='container mt-4'>";
        echo "<div class='alert alert-danger'>";
        echo "<h4 class='alert-heading'>Errore!</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
    
    $conn->close();
?>

                                <a href="dettagli_pacchetto.php?Codice_viaggio=<?php echo $pacchetto['Codice_viaggio']; ?>" 
                                   class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

