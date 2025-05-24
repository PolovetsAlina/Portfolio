<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viaggi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST["Codice_pacchetto"])){$codice_pacchetto=$_POST["Codice_pacchetto"];} else{ $codice_pacchetto=null;};
    if(!empty($_POST["destinazione"])){$destinazione=$_POST["destinazione"];} else{ $destinazione=null;};
    if(!empty($_POST["descrizione"])){$descrizione=$_POST["descrizione"];} else{ $descrizione=null;};
    if(!empty($_POST["prezzo"])){$prezzo=$_POST["prezzo"];} else{ $prezzo=null;};
    if(!empty($_POST["durata"])){$durata=$_POST["durata"];} else{ $durata=null;};
    
    $sql = "UPDATE pacchetti SET destinazione=?, descrizione=?, prezzo=?, durata=? WHERE Codice_pacchetto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $destinazione, $descrizione, $prezzo, $durata, $current_image, $codice_pacchetto);
    
    if ($stmt->execute()) {
        header('Location: amministratore.php');
        exit();
    } else {
        $error = "Errore: " . $conn->error;
    }
}

$codice_pacchetto = '';
$destinazione = '';
$descrizione = '';
$prezzo = '';
$durata = '';
$immagine = '';

if (isset($_GET['Codice_pacchetto'])) {
    $codice_pacchetto = $_GET['Codice_pacchetto'];
    
    $sql = "SELECT * FROM pacchetti WHERE Codice_pacchetto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codice_pacchetto);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $codice_pacchetto = $row['Codice_pacchetto'];
        $destinazione = $row['destinazione'];
        $descrizione = $row['descrizione'];
        $prezzo = $row['prezzo'];
        $durata = $row['durata'];
        $immagine = $row['immagine'];
    } else {
        $error = "Pacchetto non trovato";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Pacchetto - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Modifica Pacchetto</h1>
        
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <div class="card">
        <div class="card-body">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
        <div class="container py-4">
        <h1 class="mb-4">Pacchetti</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pacchetto</th>
                        <th>Destinazione</th>
                        <th>Descrizione</th>
                        <th>Prezzo</th>
                        <th>Durata</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM pacchetti";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo $row['Codice_pacchetto']; ?></td>
                        <td><?php echo $row['destinazione']; ?></td>
                        <td><?php echo $row['descrizione']; ?></td>
                        <td><?php echo $row['prezzo']; ?></td>
                        <td><?php echo $row['durata']; ?></td>
                        <td>
                            <a href="modifica_pacchetto.php?Codice_pacchetto=<?php echo urlencode($row['Codice_pacchetto']); ?>" class="btn btn-sm btn-primary">Modifica</a>
                            <a href="elimina_pacchetto.php?Codice_pacchetto=<?php echo urlencode($row['Codice_pacchetto']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo pacchetto?')">Elimina</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6'>Nessun pacchetto trovato o errore nella query: " . $conn->error . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <a href="amministratore.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>