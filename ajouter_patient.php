<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$password = "";
$dbname = "med";

$conn = new mysqli($host, $user, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Récupération sécurisée des données du formulaire
$first_name = htmlspecialchars($_POST['first_name'] ?? '');
$last_name = htmlspecialchars($_POST['last_name'] ?? '');
$date_of_birth = $_POST['date_of_birth'] ?? '';
$gender = $_POST['gender'] ?? ''; // Valeur 'M' ou 'F' depuis le formulaire
$address = htmlspecialchars($_POST['address'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

// Validation des données
$errors = [];
if (empty($first_name)) $errors[] = "Le prénom est obligatoire";
if (empty($last_name)) $errors[] = "Le nom est obligatoire";
if (!DateTime::createFromFormat('Y-m-d', $date_of_birth)) $errors[] = "Format de date invalide (YYYY-MM-DD)";
if (!in_array($gender, ['M', 'F'])) $errors[] = "Veuillez sélectionner un sexe valide";
if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) $errors[] = "Email invalide";

if (!empty($errors)) {
    // Affichage des erreurs
    echo "<h2>Erreurs :</h2>";
    foreach ($errors as $error) {
        echo "<p>• $error</p>";
    }
    exit;
}

// Conversion des valeurs M/F pour la base de données
$gender_full = ($gender == 'M') ? 'Male' : 'Female';

// Requête d'insertion préparée
$sql = "INSERT INTO Patient (first_name, last_name, date_of_birth, gender, address, email) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ssssss", $first_name, $last_name, $date_of_birth, $gender_full, $address, $email);
    
    if ($stmt->execute()) {
        // Récupération de l'ID et du code patient généré
        $patient_id = $stmt->insert_id;
        $result = $conn->query("SELECT id FROM Patient WHERE id = $patient_id");
        $patient_code = $result->fetch_assoc()['id'];
        
        echo "<h2>✅ Patient ajouté avec succès !</h2>";
        
    } else {
        echo "<h2>❌ Erreur lors de l'ajout du patient : " . $stmt->error . "</h2>";
    }
    
    $stmt->close();
} else {
    echo "<h2>❌ Erreur de préparation : " . $conn->error . "</h2>";
}

$conn->close();
?>