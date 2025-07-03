<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "escuela";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo de la subida de archivos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileDestination = 'uploads/' . $fileName;

    if (move_uploaded_file($fileTmpName, $fileDestination)) {
        $sql = "INSERT INTO files (file_name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $fileName);
        $stmt->execute();
        $message = "Archivo subido exitosamente.";
    } else {
        $message = "Error al subir el archivo.";
    }

    // Redirigir a index.html con mensaje
    header("Location: index.html?message=" . urlencode($message));
    exit();
}

$conn->close();
?>
