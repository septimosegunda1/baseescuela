<?php
// Configuración de la base de datos
$servername = "localhost"; // Tu servidor de base de datos, usualmente "localhost"
$username = "root"; // Tu nombre de usuario de la base de datos
$password = ""; // Tu contraseña de la base de datos (vacío si no tienes)
$dbname = "sistema_escuela"; // El nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$alumno_encontrado = false;
$alumno_data = [];
$materias_previas_data = [];
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST['dni'];

    // Consulta para obtener los datos del alumno
    $stmt_alumno = $conn->prepare("SELECT id_alumnos, nombre, apellido, DNI, fecha_nacimiento, telefono, curso FROM alumnos WHERE DNI = ?");
    if ($stmt_alumno) {
        $stmt_alumno->bind_param("i", $dni);
        $stmt_alumno->execute();
        $result_alumno = $stmt_alumno->get_result();

        if ($result_alumno->num_rows > 0) {
            $alumno_encontrado = true;
            $alumno_data = $result_alumno->fetch_assoc();

            // Consulta para obtener las materias previas del alumno
            $stmt_materias = $conn->prepare("SELECT m.nombre_materia, mp.fecha_examen FROM materias_previas mp JOIN materias m ON mp.id_materias = m.id_materias WHERE mp.id_alumnos = ?");
            if ($stmt_materias) {
                $stmt_materias->bind_param("i", $alumno_data['id_alumnos']);
                $stmt_materias->execute();
                $result_materias = $stmt_materias->get_result();
                while ($row = $result_materias->fetch_assoc()) {
                    $materias_previas_data[] = $row;
                }
                $stmt_materias->close();
            } else {
                $error_message = "Error en la preparación de la consulta de materias: " . $conn->error;
            }
        } else {
            $error_message = "No se encontró ningún alumno con el DNI: " . htmlspecialchars($dni);
        }
        $stmt_alumno->close();
    } else {
        $error_message = "Error en la preparación de la consulta de alumno: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - Escuela Secundaria Técnica N°5</title>
    <link rel="stylesheet" href="estilo-index.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos específicos para la página de resultados */
        .resultados-container {
            padding: 4rem 2rem;
            max-width: 900px;
            margin: 2rem auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .resultados-container h2 {
            font-size: 2.8rem;
            color: #1a5276;
            margin-bottom: 2.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        .resultados-container h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: #f39c12;
            border-radius: 2px;
        }

        .alumno-info p {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: #444;
        }

        .alumno-info p strong {
            color: #1a5276;
        }

        .materias-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .materias-table th, .materias-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }

        .materias-table th {
            background-color: #1a5276;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        .materias-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .materias-table tr:hover {
            background-color: #e9e9e9;
        }

        .no-results-message, .error-message {
            font-size: 1.3rem;
            color: #c0392b;
            text-align: center;
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: #ffebee;
            border: 1px solid #c0392b;
            border-radius: 8px;
        }

        .success-message {
            font-size: 1.3rem;
            color: #27ae60;
            text-align: center;
            margin-top: 2rem;
            padding: 1.5rem;
            background-color: #e8f5e9;
            border: 1px solid #27ae60;
            border-radius: 8px;
        }

        .back-button-container {
            text-align: center;
            margin-top: 3rem;
        }

        .back-button {
            display: inline-block;
            background-color: #f39c12;
            color: white;
            padding: 1rem 2rem;
            text-decoration: none;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
        }

        /* Ajustes responsive para buscar.php */
        @media (max-width: 768px) {
            .resultados-container {
                padding: 2rem 1rem;
                margin: 1rem auto;
            }
            .resultados-container h2 {
                font-size: 2.2rem;
            }
            .alumno-info p {
                font-size: 1rem;
            }
            .materias-table th, .materias-table td {
                padding: 8px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <div class="escudo">
                <img src="Escudo.jpg" alt="Escudo EEST N°5" height="70">
            </div>
            <div class="logo">
                <h1>E.E.S<span>.T N°5</span></h1>
            </div>
        </div>
        <nav>
            <ul class="menu">
                <li><a href="index.html#inicio">Inicio</a></li>
                <li><a href="index.html#nosotros">Nosotros</a></li>
                <li><a href="index.html#especialidades">Especialidades</a></li>
                <li><a href="index.html#contacto">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="resultados-container">
            <?php if ($alumno_encontrado): ?>
                <h2>Resultados para el DNI: <?php echo htmlspecialchars($alumno_data['DNI']); ?></h2>
                <div class="alumno-info">
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($alumno_data['nombre'] . ' ' . $alumno_data['apellido']); ?></p>
                    <p><strong>Curso:</strong> <?php echo htmlspecialchars($alumno_data['curso']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($alumno_data['telefono']); ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($alumno_data['fecha_nacimiento']))); ?></p>
                </div>

                <?php if (!empty($materias_previas_data)): ?>
                    <h3 style="margin-top: 2rem; font-size: 1.8rem; color: #1a5276; text-align: center;">Materias Previas</h3>
                    <table class="materias-table">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Fecha de Examen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materias_previas_data as $materia): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($materia['nombre_materia']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($materia['fecha_examen']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="success-message">¡Felicitaciones! Este alumno no tiene materias previas registradas.</p>
                <?php endif; ?>

            <?php elseif (!empty($error_message)): ?>
                <p class="no-results-message"><?php echo $error_message; ?></p>
            <?php else: ?>
                <p class="no-results-message">Por favor, ingresa un DNI para buscar.</p>
            <?php endif; ?>

            <div class="back-button-container">
                <a href="index.html" class="back-button">Volver a Inicio</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-contenido">
            <div class="footer-info">
                <div class="logo-container">
                    <div class="escudo">
                        <img src="Escudo.jpg" alt="Escudo EEST N°5" height="60">
                    </div>
                    <div class="logo">
                        <h1>E.E.S<span>.T N°5</span></h1>
                    </div>
                </div>
                <p>Educando para el futuro, formando profesionales.</p>
            </div>

            <div class="footer-sections">
                <div class="footer-enlaces">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="index.html#inicio">Inicio</a></li>
                        <li><a href="index.html#nosotros">Nosotros</a></li>
                        <li><a href="index.html#especialidades">Especialidades</a></li>
                        <li><a href="index.html#contacto">Contacto</a></li>
                    </ul>
                </div>

                <div class="footer-contacto">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Calle Falsa 123, Ranelagh</p>
                    <p><i class="fas fa-phone"></i> (011) 4200-1234</p>
                    <p><i class="fas fa-envelope"></i> info@eestn5.edu.ar</p>
                    <div class="footer-redes">
                        <a href="#" class="red-social" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="red-social" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="red-social" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="" class="red-social" title="Tiktok"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="footer-horarios">
                    <h3>Horarios de Atención</h3>
                    <ul>
                        <li><strong>Administración:</strong> Lunes a Viernes 8:00 - 16:00</li>
                        <li><strong>Turno Matutino:</strong> 7:30 - 13:30</li>
                        <li><strong>Turno Vespertino:</strong> 14:00 - 20:00</li>
                        <li><strong>Biblioteca:</strong> 8:00 - 19:00</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer-legal">
            <div class="legal-links">
                <a href="#">Política de Privacidad</a>
                <a href="#">Términos y Condiciones</a>
                <a href="#">Aviso Legal</a>
            </div>
            <div class="copyright">
                <p>&copy; 2025 Escuela Secundaria Técnica N°5. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>