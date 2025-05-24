<?php
session_start();

define('RUOLO_USER', 'user');
define('RUOLO_ADMIN', 'admin');

function isLoggedIn() {
    return isset($_SESSION['mail']);
}

function getUserName() {
    if (isLoggedIn()) {
        return isset($_SESSION['nome']) ? ($_SESSION['nome']) : 'Utente';
    }
    return '';
}

function getUserRole() {
    if (!isLoggedIn()) {
        return RUOLO_GUEST;
    }
    
    if (isset($_SESSION['ruolo'])) {
        return $_SESSION['ruolo'];
    }
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "viaggi";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        return RUOLO_USER;
    }
    
    $mail = $_SESSION['mail'];
    
    $sql = "SELECT Ruolo, Nome FROM utenti WHERE Mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['ruolo'] = $row['Ruolo'];
        $_SESSION['nome'] = $row['Nome'];
        return $row['Ruolo'];
    }
    
    $_SESSION['ruolo'] = RUOLO_USER;
    return RUOLO_USER;
}

function isAdmin() {
    return getUserRole() === RUOLO_ADMIN;
}

function isUser() {
    return getUserRole() === RUOLO_USER;
}

function checkPageAccess($requiredRole = null) {
    if ($requiredRole === RUOLO_ADMIN && !isAdmin()) {
        header('Location: index.php');
        exit();
    }
    if ($requiredRole === RUOLO_USER && !isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function getWelcomeMessage() {
    if (isLoggedIn()) {
        $role = getUserRole();
        $name = getUserName();
        if ($role === RUOLO_ADMIN) {
            return "Benvenuto Amministratore, $name";
        }
        return "Benvenuto, $name";
    }
    return "Benvenuti su Viaggi";
}

function handleLogout() {
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit();
    }
}

function getNavbarLinks() {
    $links = [
        ['href' => 'index.php', 'text' => 'Home'],
        ['href' => 'proposte.php', 'text' => 'Proposte'],
        ['href' => 'contatti.php', 'text' => 'Contatti']
    ];

    if (isLoggedIn()) {
        $role = getUserRole();
        
        if ($role === RUOLO_USER) {
            $links[] = ['href' => 'utenti.php', 'text' => 'Area Personale'];
            $links[] = ['href' => 'visualizza_prenotazioni_ut.php', 'text' => 'Prenotazioni'];
            $links[] = ['href' => 'visualizza_pagamenti_ut.php', 'text' => 'Pagamenti'];
        } elseif ($role === RUOLO_ADMIN) {
            $links[] = ['href' => 'gestisci_viaggi.php', 'text' => 'Gestione Viaggi'];
            $links[] = ['href' => 'amministratore.php', 'text' => 'Amministrazione'];
        }
        
        $links[] = ['href' => '?logout=1', 'text' => 'Logout'];
    } else {
        $links[] = ['href' => 'login.php', 'text' => 'Login'];
        $links[] = ['href' => 'registrazione.php', 'text' => 'Accedi o Registrati'];
    }
    
    return $links;
}

handleLogout();
?>

<header class="bg-dark py-5" style="background-image: url(''); background-size: cover; background-position: center;">
    <div class="container text-center text-white">
        <h1 class="display-4"><?php echo getWelcomeMessage(); ?></h1>
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="proposte.php">Proposte</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contatti.php">Contatti</a>
                        </li>
                        <?php if (isLoggedIn()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="utenti.php">Area Personale</a>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="amministratore.php">Amministrazione</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="?logout=1">Logout</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="registrazione.php">Accedi o Registrati</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
