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

$search_term = isset($_GET['search']) ? $_GET['search'] : '';

$result = null;
if (!empty($search_term)) {
    $sql = "SELECT * FROM utenti WHERE Mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM utenti";
    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestisci Utenti - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Gestisci Utenti</h1>

        <div class="mb-4">
            <form action="" method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="search" placeholder="Cerca per nome o cognome..." value="<?php echo ($search_term); ?>">
                <button type="submit" class="btn btn-primary">Cerca</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mail</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Ruolo</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['Mail']; ?></td>
                                <td><?php echo $row['Nome']; ?></td>
                                <td><?php echo $row['Cognome']; ?></td>
                                <td><?php echo $row['Ruolo']; ?></td>
                                <td>
                                    <a href="modifica_utente.php?Mail=<?php echo urlencode($row['Mail']); ?>" class="btn btn-sm btn-primary">Modifica</a>
                                    <a href="elimina_utente.php?Mail=<?php echo urlencode($row['Mail']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo utente?')">Elimina</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nessun utente trovato<?php echo (!empty($search_term)) ? " per la ricerca: <strong>".$search_term."</strong>" : ""; ?>.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="amministratore.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
</body>
</html>
