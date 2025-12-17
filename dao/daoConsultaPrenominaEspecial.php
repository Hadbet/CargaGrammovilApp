<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once('db/db_Aux.php');

$nomina = isset($_GET['nomina']) ? $_GET['nomina'] : die(json_encode(array("message" => "No se proporcionó la nómina.")));
$semana = isset($_GET['semana']) ? $_GET['semana'] : die(json_encode(array("message" => "No se proporcionó la semana.")));
$anio = date("Y");

$nomina_limpia = ltrim($nomina, '0');

$con = new LocalConector();
$conex = $con->conectar();

$response = array();

try {
    $stmtPrenomina = $conex->prepare("
        SELECT * FROM PrenominaEspecial 
        WHERE nomina = ? AND semana = ? AND anio = ?
    ");
    $stmtPrenomina->bind_param("sii", $nomina_limpia, $semana, $anio);
    $stmtPrenomina->execute();
    $resultadoPrenomina = $stmtPrenomina->get_result();

    if ($resultadoPrenomina->num_rows > 0) {
        $prenominaData = $resultadoPrenomina->fetch_assoc();
        $idPrenomina = $prenominaData['id'];

        $stmtDetalles = $conex->prepare("
            SELECT dia, tipo, valor FROM DetallesPrenominaEspecial
            WHERE id_prenomina_especial = ?
            ORDER BY dia ASC
        ");
        $stmtDetalles->bind_param("i", $idPrenomina);
        $stmtDetalles->execute();
        $resultadoDetalles = $stmtDetalles->get_result();

        $detalles = array();
        while($row = $resultadoDetalles->fetch_assoc()){
            $detalles[] = $row;
        }

        // Obtener el rango de días basado en todos los registros de esta semana especial
        $stmtRango = $conex->prepare("
            SELECT MIN(d.dia) as dia_min, MAX(d.dia) as dia_max 
            FROM DetallesPrenominaEspecial d
            INNER JOIN PrenominaEspecial p ON d.id_prenomina_especial = p.id
            WHERE p.semana = ? AND p.anio = ?
        ");
        $stmtRango->bind_param("ii", $semana, $anio);
        $stmtRango->execute();
        $resultadoRango = $stmtRango->get_result();
        $rango = $resultadoRango->fetch_assoc();

        $prenominaData['detalles'] = $detalles;
        $prenominaData['dia_min'] = $rango['dia_min'];
        $prenominaData['dia_max'] = $rango['dia_max'];

        $response['status'] = 'success';
        $response['data'] = $prenominaData;

        $stmtDetalles->close();
        $stmtRango->close();

    } else {
        $response['status'] = 'not_found';
        $response['message'] = 'No se encontraron registros de prenómina especial para la semana ' . $semana . '.';
    }

    $stmtPrenomina->close();

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
} finally {
    $conex->close();
}

echo json_encode($response);
?>