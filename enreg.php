<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données soumises par le formulaire
    $patient_id = $_POST['patient_id'];
    $study_date = $_POST['study_date'];
    $study_description = $_POST['study_description'];

    // Traitement du fichier DICOM
    $dicom_image = $_FILES['dicom_image']['tmp_name'];
    $dicom_data = file_get_contents($dicom_image);  // Lire l'image DICOM

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '', 'med'); // Remplacer par votre base de données

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Insertion de l'étude dans la table ImagingStudy
    $stmt = $conn->prepare("INSERT INTO ImagingStudy (patient_id, study_date, study_description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $patient_id, $study_date, $study_description);
    $stmt->execute();
    $study_id = $stmt->insert_id;  // Récupérer l'ID de l'étude nouvellement insérée

    // Insertion de l'image DICOM dans la table DicomImages
    $stmt = $conn->prepare("INSERT INTO DicomImages (study_id, dicom_data, image_format) VALUES (?, ?, ?)");
    $image_format = "DICOM";  // Format de l'image
    $stmt->bind_param("iss", $study_id, $dicom_data, $image_format);
    $stmt->execute();

    // Fermer la connexion
    $stmt->close();
    $conn->close();

    echo "Étude et image DICOM enregistrées avec succès!";
}
?>
