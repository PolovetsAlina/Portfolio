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
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><?php echo $row['Mail']; ?></h5>
                            <p class="card-text text-muted small">
                                Importo: <?php echo $row['Importo']; ?> €
                            </p>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-3">
                                <li><strong>Data pagamento:</strong> <?php echo $row['Data_pagamento']; ?></li>
                                <li><strong>Metodo:</strong> <?php echo $row['Metodo_pagamento']; ?></li>
                                <li><strong>Stato:</strong> 
                                    <span class="badge <?php echo $row['Stato'] == 'Pagato' ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $row['Stato'] == 'Pagato' ? 'Pagato' : 'In attesa'; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
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
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
        <a href="utenti.php" class="btn btn-secondary mt-3">Torna indietro</a>
    </div>
</body>
</html>
<?php
}catch (Exception $e) {
    echo "<div class='alert alert-danger'>Errore: " . $e->getMessage() . "</div>";
    echo "<a href='amministratore.php' class='btn btn-secondary mt-3'>Torna indietro</a>";
}
?>

