<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'viaggi';

try {

$conn = new mysqli($host, $username, $password, $database);

$sql = "SELECT p.*, u.Mail FROM pagamenti p , utenti u WHERE p.Mail = u.Mail";
$result = $conn->query($sql);

if (!$result) {
    throw new Exception("Query failed: " . $conn->error);
}

if ($result->num_rows === 0) {
    echo "<div class='alert alert-info'>Nessun pagamento trovato.</div>";
} else {
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Pagamenti - Amministratore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <h1 class="mb-4">Pagamenti</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Pagamento</th>
                        <th>Mail</th>
                        <th>Importo</th>
                        <th>Data Pagamento</th>
                        <th>Metodo Pagamento</th>
                        <th>Stato</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['Codice_pagamento']; ?></td>
                        <td><?php echo $row['Mail']; ?></td>
                        <td><?php echo $row['Importo']; ?> €</td>
                        <td><?php echo $row['Data_pagamento']; ?></td>
                        <td><?php echo $row['Metodo_pagamento']; ?></td>
                        <td><?php echo $row['Stato']; ?></td>
                        <td>
                            <a href="modifica_pagamento.php?id=<?php echo $row['Codice_pagamento']; ?>" class="btn btn-sm btn-primary">Modifica</a>
                        </td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
        <a href="amministratore.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
</body>
</html>
<?php
}catch (Exception $e) {
    echo "<div class='alert alert-danger'>Errore: " . $e->getMessage() . "</div>";
    echo "<a href='amministratore.php' class='btn btn-secondary mt-3'>Torna indietro</a>";
}
?>

