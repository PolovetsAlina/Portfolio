<?php
require_once 'header.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $messaggio = isset($_POST['messaggio']) ? trim($_POST['messaggio']) : '';
    
    if (empty($nome) || empty($email) || empty($messaggio)) {
        $error_message = 'Tutti i campi sono obbligatori.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Formato email non valido.';
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "viaggi";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            $error_message = "Errore di connessione al database: " . $conn->connect_error;
        } else {
            $sql = "INSERT INTO contatti (Nome, Email, Messaggio, Data) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $nome, $email, $messaggio);
                if ($stmt->execute()) {
                    $success_message = 'Messaggio inviato con successo!';
                } else {
                    $error_message = 'Errore durante l\'invio del messaggio.';
                }
                $stmt->close();
            } else {
                $error_message = 'Errore nella preparazione della query.';
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contattaci</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
</head>
<body>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="section-title">Informazioni di Contatto</h5>
                            <div class="mb-4">
                                <h6>Indirizzo</h6>
                                <p>Via Roma 123<br>00100 Roma (RM)<br>Italia</p>
                            </div>
                            <div class="mb-4">
                                <h6>Telefono</h6>
                                <p>+39 06 1234567</p>
                            </div>
                            <div class="mb-4">
                                <h6>Email</h6>
                                <p>info@viaggi.example.com</p>
                            </div>
                            <div>
                                <h6>Orari di Apertura</h6>
                                <p>Lun - Ven: 9:00 - 18:00<br>
                                   Sab: 9:00 - 13:00<br>
                                   Dom: Chiuso</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($success_message): ?>
                            <div class="alert alert-success">
                                <?php echo ($success_message); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($error_message): ?>
                            <div class="alert alert-danger">
                                <?php echo ($error_message); ?>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="contatti.php">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required
                                           value="<?php echo (isset($_POST['nome']) ? $_POST['nome'] : ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?php echo (isset($_POST['email']) ? $_POST['email'] : ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="messaggio" class="form-label">Messaggio</label>
                                    <textarea class="form-control" id="messaggio" name="messaggio" rows="5" required><?php echo(isset($_POST['messaggio']) ? $_POST['messaggio'] : ''); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Invia Messaggio</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
