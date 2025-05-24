<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viaggi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST["codice_pacchetto"])){$codice_pacchetto=$_POST["codice_pacchetto"];} else{ $codice_pacchetto=null;};
    if(!empty($_POST["destinazione"])){$destinazione=$_POST["destinazione"];} else{ $destinazione=null;};
    if(!empty($_POST["descrizione"])){$descrizione=$_POST["descrizione"];} else{ $descrizione=null;};
    if(!empty($_POST["prezzo"])){$prezzo=$_POST["prezzo"];} else{ $prezzo=null;};
    if(!empty($_POST["durata"])){$durata=$_POST["durata"];} else{ $durata=null;};
    
    $target_dir = "img/";
    $target_file = $target_dir . basename($_FILES['immagine']['name']);
    
    if (move_uploaded_file($_FILES['immagine']['tmp_name'], $target_file)) {
        $immagine = basename($_FILES['immagine']['name']);
    } else {
        $immagine = null;
        $error = "Errore nel caricamento dell'immagine";
    }
    
    $sql = "INSERT INTO pacchetti (Codice_pacchetto, destinazione, descrizione, prezzo, durata, immagine) 
            VALUES ('$codice_pacchetto', '$destinazione', '$descrizione', '$prezzo', '$durata', '$immagine')";
    
    if ($conn->query($sql) === TRUE) {
        header('Location: amministratore.php');
        exit();
    } else {
        $error = "Errore: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Pacchetto - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Aggiungi Nuovo Pacchetto</h1>
        
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="codice_pacchetto" class="form-label">Codice Pacchetto</label>
                        <input type="text" class="form-control" id="codice_pacchetto" name="codice_pacchetto" required>
                    </div>
                    <div class="mb-3">
                        <label for="destinazione" class="form-label">Destinazione</label>
                        <input type="text" class="form-control" id="destinazione" name="destinazione" required>
                    </div>
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="prezzo" class="form-label">Prezzo (€)</label>
                        <input type="number" class="form-control" id="prezzo" name="prezzo" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="durata" class="form-label">Durata (giorni)</label>
                        <input type="number" class="form-control" id="durata" name="durata" required>
                    </div>
                    <div class="mb-3">
                        <label for="immagine" class="form-label">Immagine</label>
                        <input type="file" class="form-control" id="immagine" name="immagine" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Aggiungi Pacchetto</button>
                    <a href="amministratore.php" class="btn btn-secondary">Torna Indietro</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
