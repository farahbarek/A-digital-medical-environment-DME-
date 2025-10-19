<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $study_id = $_POST['study_id'];
    $action = $_POST['action'] ?? 'both'; // Par défaut, tout afficher

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'med');

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Récupérer les données selon l'action demandée
    if ($action === 'image' || $action === 'both') {
        // Requête pour l'image DICOM
        $stmt = $conn->prepare("SELECT dicom_data FROM DicomImages WHERE study_id = ?");
        $stmt->bind_param("i", $study_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($dicom_data);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            if ($action === 'image') {
                header("Content-Type: application/dicom");
                echo $dicom_data;
                exit;
            }
        } else {
            echo "Aucune image trouvée pour ce Study ID.<br>";
        }
        $stmt->close();
    }

    if ($action === 'description' || $action === 'both') {
        // Requête pour la description
        $stmt = $conn->prepare("SELECT study_description FROM ImagingStudy WHERE id = ?");
        $stmt->bind_param("i", $study_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($study_description);

        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            echo "<h2>Description de l'étude :</h2>";
            echo "<p>" . htmlspecialchars($study_description) . "</p>";
        } else {
            echo "Aucune description trouvée pour ce Study ID.<br>";
        }
        $stmt->close();
    }

    // Afficher l'image si "both" est sélectionné
    if ($action === 'both' && isset($dicom_data)) {
        echo "<h2>Image DICOM :</h2>";
        echo '<img src="data:application/dicom;base64,' . base64_encode($dicom_data) . '" alt="Image DICOM">';
    }

    $conn->close();
}
?>