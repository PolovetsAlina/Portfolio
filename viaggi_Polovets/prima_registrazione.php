<?php
session_start();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viaggi - Esplora il Mondo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style/registrazione.css">
</head>
<body class="bg-light">
    <header class="bg-dark py-5" style="background-image: url('https://images.unsplash.com/photo-1506748686211-8a1494010bfc'); background-size: cover; background-position: center;">
        <div class="container text-center text-white">
            <h1 class="display-4">Registrazione</h1>
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
                            <li class="nav-item"><a class="nav-link" href="registrazione.php">Registrati</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4 fw-bold" style="color: #2c3e50;">Crea il tuo account</h2>
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Il tuo nome" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cognome" class="form-label">Cognome</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="bi bi-person-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="cognome" name="cognome" placeholder="Il tuo cognome" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="email" name="mail" placeholder="La tua email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="pass" placeholder="Crea una password" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Telefono</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="tel" class="form-control" id="telefono" name="tel" placeholder="Il tuo telefono" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="codice_fiscale" class="form-label">Codice Fiscale</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="bi bi-person-badge"></i>
                                        </span>
                                        <input type="text" class="form-control text-uppercase" id="codice_fiscale" name="c_f" placeholder="Codice Fiscale" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3 fw-semibold">Registrati</button>
                            <p class="text-center mb-0 mt-3">
                                Hai già un account? 
                                <a href="registrazione.php" class="text-primary fw-semibold text-decoration-none">Accedi</a>
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
            min-width: 50px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 38px;
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
            height: 38px;
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
   
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST["mail"])){$mail=$_POST["mail"];} else{ $mail=null;};
    if(!empty($_POST["pass"])){$pw=md5($_POST["pass"]);} else{ $pw=null;};
    if(!empty($_POST["cognome"])){$cognome=$_POST["cognome"];} else{ $cognome=null;};
    if(!empty($_POST["nome"])){$nome=$_POST["nome"];} else{ $nome=null;};
    if(!empty($_POST["tel"])){$tel=$_POST["tel"];} else{ $tel=null;};
    if(!empty($_POST["c_f"])){$c_f=$_POST["c_f"];} else{ $c_f=null;};

    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "viaggi";

    $connessione = new mysqli($host, $user, $password, $db);

    if ($connessione->connect_errno) {
        echo "Connessione fallita: ". $connessione->connect_error . ".";
        exit();
    }

    $stmt = $connessione->prepare("INSERT INTO utenti (Mail, Password, Cognome, Nome, Telefono, Codice_Fiscale, Ruolo) VALUES (?, ?, ?, ?, ?, ?, 'utente')");
    $stmt->bind_param("ssssss", $mail, $pw, $cognome, $nome, $tel, $c_f);

    if ($stmt->execute()) {
        header('Location: registrazione.php');
        exit();
    } else {
        echo "Errore della query: " . $stmt->error . ".";
    }

    $stmt->close();
    $connessione->close();
}
?> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
