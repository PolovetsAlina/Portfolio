<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaggi - Esplora il Mondo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #4a6480, #2b3d4f);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .dashboard-header .welcome-message {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .dashboard-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .action-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-card i {
            font-size: 2.5rem;
            color: #4a6480;
            margin-bottom: 1rem;
        }

        .action-card h3 {
            margin: 1rem 0;
            color: #333;
        }

        .search-container {
            margin: 2rem 0;
            text-align: center;
        }

        .search-container input {
            padding: 1rem;
            border-radius: 25px;
            border: 2px solid #e0e0e0;
            width: 80%;
            max-width: 500px;
        }

        .search-container input:focus {
            border-color: #4a6480;
            box-shadow: 0 0 0 3px rgba(74, 100, 128, 0.1);
        }

        .card {
            border: none;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn-primary {
            background-color: #4a6480;
            border-color: #4a6480;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #334d66;
            border-color: #334d66;
            transform: translateY(-2px);
        }

        footer {
            background: linear-gradient(135deg, #2b3d4f, #4a6480);
            color: white;
        }

        footer p {
            margin: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-light">
 
    <main class="container py-4">
    <div class="container mt-4">
    <div class="mb-4 text-center">
        <input type="text" id="searchInput" class="form-control w-50 mx-auto" placeholder="Cerca destinazioni...">
    </div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="destinazioniContainer">
    <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viaggi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT viaggi.Codice_viaggio AS Viaggio,
               pacchetti.Codice_pacchetto,
               pacchetti.destinazione,
               pacchetti.descrizione,
               pacchetti.prezzo AS prezzo,
               pacchetti.immagine AS img,
               viaggi.Data_partenza
        FROM viaggi, pacchetti
        WHERE viaggi.Codice_pacchetto = pacchetti.Codice_pacchetto
        AND viaggi.Data_partenza > CURDATE()
        ORDER BY viaggi.Data_partenza ASC
        LIMIT 3";

$result = $conn->query($sql);

$destinazioni = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['id'] = $row['Viaggio'];  
        $row['img'] = (strpos($row['img'], 'img/') === 0) ? $row['img'] : 'img/' . $row['img'];
        $row['prezzo'] = strpos($row['prezzo'], '&euro;') === false ? $row['prezzo'] . '&euro;' : $row['prezzo'];
        $destinazioni[] = $row;
    }
}

$conn->close();

foreach ($destinazioni as $destinazione) {
    echo "<div class='col' data-title='{$destinazione['destinazione']}'>
            <div class='card h-100 shadow-sm'>
                <img src='{$destinazione['img']}' class='card-img-top' alt='{$destinazione['destinazione']}'>
                <div class='card-body text-center'>
                    <h5 class='card-title'>{$destinazione['destinazione']}</h5>
                    <p class='card-text'>{$destinazione['descrizione']}</p>
                    <p class='fw-bold text-primary'>{$destinazione['prezzo']}</p>
                </div>
                <div class='text-center mb-3'>
                    <a href='dettagli_pacchetto.php?Codice_viaggio={$destinazione['Viaggio']}' class='btn btn-primary'>Dettagli</a>
                </div>
            </div>
        </div>";
}
?>

    </div>
</div>
<script>
    document.getElementById("searchInput").addEventListener("input", function() {
        let filter = this.value.toLowerCase();
        let cards = document.querySelectorAll("#destinazioniContainer .col");
        
        cards.forEach(card => {
            let title = card.getAttribute("data-title").toLowerCase();
            if (title.includes(filter)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
</script>
    </main>
    <footer class="bg-dark text-center py-3">
        <p class="mb-0 text-white">&copy; 2025 Viaggi - Tutti i diritti riservati</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
