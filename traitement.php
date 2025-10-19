<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données soumises par le formulaire
    $patient_id = $_POST['patient_id'];
    $exam_type = $_POST['exam_type'];
    $exam_date = $_POST['exam_date'];
    $reason = $_POST['reason'];
    $prescribing_physician = $_POST['prescribing_physician'];

    // Connexion à la base de données HL7
    $conn = new mysqli('localhost', 'root', '', 'med');  

    // Vérifier la connexion
    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Insertion de la prescription dans la base de données
    $sql = "INSERT INTO prescription (patient_id, exam_type, exam_date, reason, prescribing_physician)
            VALUES ('$patient_id', '$exam_type', '$exam_date', '$reason', '$prescribing_physician')";

    if ($conn->query($sql) === TRUE) {
        echo "Prescription ajoutée avec succès!";
    } else {
        echo "Erreur : " . $conn->error;
    }

    // Fermer la connexion
    $conn->close();
}
?>
