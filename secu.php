<?php
session_start();

// Configuration BDD
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'med');

try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données
$id_code = $_POST['id_code'] ?? '';
$password = $_POST['password'] ?? '';

// Validation
if (empty($id_code) || empty($password)) {
    die("Tous les champs sont requis");
}

// Vérification de l'utilisateur
$stmt = $conn->prepare("SELECT id, id_code, password FROM users WHERE id_code = :id_code LIMIT 1");
$stmt->bindParam(':id_code', $id_code);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérification du mot de passe haché
    if (password_verify($password, $user['password'])) {
        // Authentification réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['id_code'] = $user['id_code'];
        
        header("Location: http://localhost/work/DME.html"); // Redirection vers DME.html
        exit();
    } else {
        header("Location: login.html?error=invalid_password"); // Mot de passe incorrect
        exit();
    }
} else {
    header("Location: login.html?error=user_not_found"); // Utilisateur inexistant
    exit();
}
?>