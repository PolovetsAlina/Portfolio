<?php
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST["mail"])){$mail=$_POST["mail"];} else{ $mail=null;};
    if(!empty($_POST["pass"])){$pw=md5($_POST["pass"]);} else{ $pw=null;};

    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "viaggi";

    $connessione = new mysqli($host, $user, $password, $db);

    if ($connessione->connect_errno) {
        die("Connessione fallita: ". $connessione->connect_error);
    }

    if (!$tabella_risultato = $connessione->query("SELECT Mail, Password, Cognome, Nome, Telefono, Codice_Fiscale, Ruolo FROM utenti WHERE Mail='$mail'")) {
        die("Errore della query: " . $connessione->error);
    } else {
        if($tabella_risultato->num_rows > 0) {
            $row = $tabella_risultato->fetch_array(MYSQLI_ASSOC);

            if ($pw === $row['Password']) {
                session_start();
                $_SESSION['mail'] = $mail;
                $_SESSION['ruolo'] = $row['Ruolo'];
                $_SESSION['nome'] = $row['Nome'];
                
                $tabella_risultato->close();
                $connessione->close();
                
                if($row['Ruolo'] === 'admin') {
                    header("Location: amministratore.php");
                } else {
                    header("Location: utenti.php");
                }
                exit();
            } else {
                $error_message = "La password inserita non è corretta";
            }
        } else {
            $error_message = "CREDENZIALI NON VALIDE";
        }
        $tabella_risultato->close();
        $connessione->close();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaggi - Esplora il Mondo</title>
    <link rel="stylesheet" href="./style/registrazione.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <header class="bg-dark py-5" style="background-image: url('https://images.unsplash.com/photo-1506748686211-8a1494010bfc'); background-size: cover; background-position: center;">
        <div class="container text-center text-white">
            <h1 class="display-4">Regestrazione</h1>
            <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="pacchetti.php">Pacchetti</a></li>
                            <li class="nav-item"><a class="nav-link" href="proposte.php">Proposte</a></li>
                            <li class="nav-item"><a class="nav-link" href="contatti.php">Contatti</a></li>
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
        </div>
    </header>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4 fw-bold" style="color: #2c3e50;">Accedi</h2>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="<?php echo ($_SERVER['PHP_SELF']); ?>">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="text" class="form-control" id="email" name="mail" placeholder="La tua email" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="pass" placeholder="La tua password" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Ricordami</label>
                                </div>
                                <a href="#" class="text-decoration-none text-primary">Password dimenticata?</a>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-semibold">Accedi</button>
                            <p class="text-center mb-0 mt-3">
                                Non hai un account? 
                                <a href="prima_registrazione.php" class="text-primary fw-semibold text-decoration-none">Registrati</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            overflow: hidden;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            min-width: 55px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 38px; /* Altezza standard di Bootstrap per gli input */
        }
        .input-group-text i {
            font-size: 1rem;
            width: 1rem;
            height: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .form-control {
            border-left: none;
            padding: 0.5rem 0.75rem;
            height: 38px; /* Stessa altezza dell'icona */
            line-height: 1.5;
            border-radius: 0 0.375rem 0.375rem 0 !important;
        }
        .form-control:focus {
            border-left: none;
            box-shadow: none;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px 0;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
    <footer class="bg-dark text-center py-3">
        <p class="mb-0 text-white">&copy; 2025 Viaggi - Tutti i diritti riservati</p>
    </footer>
    
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>