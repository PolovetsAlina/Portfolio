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

// Recupera tutti i pacchetti disponibili per la selezione
$sql_pacchetti = "SELECT Codice_pacchetto, destinazione FROM pacchetti";
$result_pacchetti = $conn->query($sql_pacchetti);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST["codice_viaggio"])){$codice_viaggio=$_POST["codice_viaggio"];} else{ $codice_viaggio=null;};
    if(!empty($_POST["codice_pacchetto"])){$codice_pacchetto=$_POST["codice_pacchetto"];} else{ $codice_pacchetto=null;};
    if(!empty($_POST["data_partenza"])){$data_partenza=$_POST["data_partenza"];} else{ $data_partenza=null;};
    if(!empty($_POST["data_ritorno"])){$data_ritorno=$_POST["data_ritorno"];} else{ $data_ritorno=null;};
    
    $sql = "INSERT INTO viaggi (Codice_viaggio, Codice_pacchetto, data_partenza, data_ritorno) 
            VALUES ('$codice_viaggio', '$codice_pacchetto', '$data_partenza', '$data_ritorno')";
    
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
    <title>Aggiungi Viaggio - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Aggiungi Nuovo Viaggio</h1>
        
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <div class="card">
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                        <label for="codice_viaggio" class="form-label">Codice Viaggio</label>
                        <input type="text" class="form-control" id="codice_viaggio" name="codice_viaggio" required>
                    </div>
                    <div class="mb-3">
                        <label for="codice_pacchetto" class="form-label">Pacchetto</label>
                        <select class="form-select" id="codice_pacchetto" name="codice_pacchetto" required>
                            <option value="">Seleziona un pacchetto</option>
                            <?php
                            if ($result_pacchetti->num_rows > 0) {
                                while($row = $result_pacchetti->fetch_assoc()) {
                                    echo "<option value='" . $row['Codice_pacchetto'] . "'>" . $row['destinazione'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data_partenza" class="form-label">Data Partenza</label>
                        <input type="date" class="form-control" id="data_partenza" name="data_partenza" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_ritorno" class="form-label">Data Ritorno</label>
                        <input type="date" class="form-control" id="data_ritorno" name="data_ritorno" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Aggiungi Viaggio</button>
                    <a href="amministratore.php" class="btn btn-secondary">Torna Indietro</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
