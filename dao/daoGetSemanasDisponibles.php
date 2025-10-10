<?php
// Establece el tipo de contenido a JSON
header('Content-Type: application/json');
include_once('db/db_Aux.php'); // Asegúrate de que la ruta a tu conector sea correcta

// Obtiene la nómina del query string y le quita los ceros a la izquierda
$nomina = isset($_GET['nomina']) ? ltrim($_GET['nomina'], '0') : null;

if (!$nomina) {
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionó la nómina.']);
    exit;
}

$con = new LocalConector();
$conex = $con->conectar();
$respuesta = [];

// Prepara la consulta para obtener las semanas y años únicos para esa nómina
$stmt = $conex->prepare("
    SELECT DISTINCT semana, anio 
    FROM AsistenciasSemanales 
    WHERE nomina = ? 
    ORDER BY anio DESC, semana DESC
");
$stmt->bind_param("s", $nomina);
$stmt->execute();
$resultado = $stmt->get_result();

$semanas = [];
while ($fila = $resultado->fetch_assoc()) {
    $semanas[] = $fila;
}

// Devuelve los datos si se encontraron semanas
if (!empty($semanas)) {
    $respuesta = ['status' => 'success', 'data' => $semanas];
} else {
    $respuesta = ['status' => 'error', 'message' => 'No se encontraron semanas con registros para este usuario.'];
}

$stmt->close();
$conex->close();

echo json_encode($respuesta);
?>
