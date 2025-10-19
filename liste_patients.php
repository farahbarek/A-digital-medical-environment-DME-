<?php
// Afficher les erreurs pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'med');
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des patients 👥</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 10px; }
        a { text-decoration: none; color: #007BFF; }
        a:hover { text-decoration: underline; }
        .patient-info {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 400px;
        }
    </style>
</head>
<body>

<h1>Liste des patients</h1>
<ul>
<?php
// Requête pour afficher tous les patients avec leur ID et nom complet
$sql = "SELECT id, first_name, last_name FROM Patient";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<li><a href='?id=" . $row['id'] . "'>Patient : " . $row['first_name'] . " " . $row['last_name'] . " (ID: " . $row['id'] . ")</a></li>";
    }
} else {
    echo "<li>Aucun patient trouvé.</li>";
}
?>
</ul>

<?php
// Si un ID de patient est cliqué
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM Patient WHERE id = $id";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $patient = $res->fetch_assoc();
        echo "<div class='patient-info'>";
        echo "<h2>Informations personnelles</h2>";
        echo "<p><strong>ID :</strong> " . $patient['id'] . "</p>";
        echo "<p><strong>Prénom :</strong> " . $patient['first_name'] . "</p>";
        echo "<p><strong>Nom :</strong> " . $patient['last_name'] . "</p>";
        echo "<p><strong>Date de naissance :</strong> " . $patient['date_of_birth'] . "</p>";
        echo "<p><strong>sexe :</strong> " . $patient['gender'] . "</p>";
        echo "<p><strong>Adresse :</strong> " . $patient['address'] . "</p>";
        echo "<p><strong>Email :</strong> " . $patient['email'] . "</p>";
        echo "</div>";
    } else {
        echo "<p>Patient introuvable.</p>";
    }
}

// Fermer la connexion
$conn->close();
?>

</body>
</html>
