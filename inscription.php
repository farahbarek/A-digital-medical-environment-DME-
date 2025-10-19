<?php
// Fichier : secu.php    juste pour l'inscri d'un nouveau utulisateur

// Connexion à la base de données
$host = 'localhost'; // ou l'adresse de votre serveur MySQL
$dbname = 'med';
$username = 'root'; // Remplacez par votre nom d'utilisateur MySQL
$password = ''; // Remplacez par votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et sécurisation des données
    $id_code = htmlspecialchars(trim($_POST['id_code'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validation des données
    if (empty($id_code)) {  // Fixed: Added parentheses
        die("Le champ Code ID est obligatoire.");
    }
    if (empty($password)) {
        die("Le champ Mot de passe est obligatoire.");
    }
    if (strlen($password) < 8) {
        die("Le mot de passe doit contenir au moins 8 caractères.");
    }

    // Hashage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Préparation de la requête d'insertion
    try {
        $stmt = $pdo->prepare("INSERT INTO users (id_code, password) VALUES (:id_code, :password)");
        
        // Exécution avec les paramètres
        $success = $stmt->execute([
            ':id_code' => $id_code,
            ':password' => $hashed_password
        ]);

        if ($success) {
            // Message de succès avec redirection
            header('Location: inscription.php');
            exit();
        } else {
            die("Une erreur est survenue lors de l'inscription.");
        }
        
    } catch (PDOException $e) {
        // Gestion des erreurs
        if ($e->getCode() == '23000') {
            die("Ce code ID est déjà utilisé. Veuillez en choisir un autre.");
        } else {
            die("Erreur lors de l'inscription : " . $e->getMessage());
        }
    }
} else {
    // Accès direct au fichier sans soumission de formulaire
    header('Location: index.html');
    exit();
}
?>