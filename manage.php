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

// Manejo de la eliminación de archivos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $fileId = $_POST['file_id'];
    $sql = "SELECT file_name FROM files WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $file = $result->fetch_assoc();

    if ($file) {
        $filePath = 'uploads/' . $file['file_name'];
        if (unlink($filePath)) {
            $sql = "DELETE FROM files WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $fileId);
            $stmt->execute();
            echo "<tr><td colspan='3' class='message'>Archivo eliminado exitosamente.</td></tr>";
        } else {
            echo "<tr><td colspan='3' class='message'>No se pudo eliminar el archivo, intentelo mas tarde.</td></tr>";
        }
    }
}

// Consulta para obtener archivos
$sql = "SELECT * FROM files";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['file_name']; ?></td>
        <td>
            <form action="" method="POST">
                <input type="hidden" name="file_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="delete">Eliminar</button>
            </form>
        </td>
    </tr>
<?php endwhile; ?>

<?php
$conn->close();
?>
