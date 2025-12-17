<?php
header('Content-Type: application/json');
include_once('db/db_Aux.php');

$nomina = isset($_GET['nomina']) ? ltrim($_GET['nomina'], '0') : null;

if (!$nomina) {
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionó la nómina.']);
    exit;
}

$con = new LocalConector();
$conex = $con->conectar();
$respuesta = [];

$stmt = $conex->prepare("
    SELECT DISTINCT semana, anio 
    FROM PrenominaEspecial 
    WHERE nomina = ? 
    ORDER BY anio DESC, semana DESC
");
$stmt->bind_param("s", $nomina);
$stmt->execute();
$resultado = $stmt->get_result();

$semanas = [];
while ($fila = $resultado->fetch_assoc()) {
    // Cambiar el texto de "99" a "ESPECIAL"
    $fila['semana_texto'] = $fila['semana'] == 99 ? 'ESPECIAL' : $fila['semana'];
    $semanas[] = $fila;
}

if (!empty($semanas)) {
    $respuesta = ['status' => 'success', 'data' => $semanas];
} else {
    $respuesta = ['status' => 'error', 'message' => 'No se encontraron semanas con registros para este usuario.'];
}

$stmt->close();
$conex->close();

echo json_encode($respuesta);
?>