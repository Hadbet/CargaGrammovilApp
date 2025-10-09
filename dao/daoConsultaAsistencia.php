<?php
// Permitir solicitudes desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once('db/db_Aux.php'); // Asegúrate que la ruta a tu archivo de conexión sea correcta

// Obtenemos los parámetros de la URL
$nomina = isset($_GET['nomina']) ? $_GET['nomina'] : die(json_encode(array("message" => "No se proporcionó la nómina.")));
$semana = isset($_GET['semana']) ? $_GET['semana'] : die(json_encode(array("message" => "No se proporcionó la semana.")));
$anio = date("Y"); // Usamos el año actual

// Quitamos los ceros a la izquierda de la nómina para que coincida con la base de datos
$nomina_limpia = ltrim($nomina, '0');

$con = new LocalConector();
$conex = $con->conectar();

$response = array();

try {
    // 1. Preparamos la consulta para obtener los datos semanales
    $stmtSemanal = $conex->prepare("
        SELECT * FROM AsistenciasSemanales 
        WHERE nomina = ? AND semana = ? AND anio = ?
    ");
    $stmtSemanal->bind_param("sii", $nomina_limpia, $semana, $anio);
    $stmtSemanal->execute();
    $resultadoSemanal = $stmtSemanal->get_result();

    if ($resultadoSemanal->num_rows > 0) {
        $asistenciaData = $resultadoSemanal->fetch_assoc();
        $idAsistenciaSemanal = $asistenciaData['id'];

        // 2. Preparamos la consulta para obtener los detalles diarios
        $stmtDetalles = $conex->prepare("
            SELECT dia, valor, tipo FROM DetallesAsistenciaDiaria
            WHERE id_asistencia_semanal = ?
            ORDER BY dia ASC
        ");
        $stmtDetalles->bind_param("i", $idAsistenciaSemanal);
        $stmtDetalles->execute();
        $resultadoDetalles = $stmtDetalles->get_result();

        $detalles = array();
        while($row = $resultadoDetalles->fetch_assoc()){
            $detalles[] = $row;
        }

        $asistenciaData['detalles'] = $detalles;

        $response['status'] = 'success';
        $response['data'] = $asistenciaData;
        $stmtDetalles->close();

    } else {
        $response['status'] = 'not_found';
        $response['message'] = 'No se encontraron registros de asistencia para la semana ' . $semana . '.';
    }

    $stmtSemanal->close();

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
} finally {
    $conex->close();
}

echo json_encode($response);
?>
