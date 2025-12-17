<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once('db/db_Aux.php');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['prenominaData']) || empty($data['prenominaData'])) {
    echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos.']);
    exit;
}

$con = new LocalConector();
$conex = $con->conectar();

if ($conex->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión: ' . $conex->connect_error]);
    exit;
}

$conex->begin_transaction();

try {
    $prenominaData = $data['prenominaData'];
    $errores = [];
    $insertados = 0;

    // Obtener semana y año del primer registro
    $semana = $prenominaData[0]['semana'];
    $anio = $prenominaData[0]['anio'];

    // Eliminar registros previos de la misma semana y año
    $stmtDelete = $conex->prepare("DELETE FROM PrenominaEspecial WHERE semana = ? AND anio = ?");
    $stmtDelete->bind_param("ii", $semana, $anio);
    $stmtDelete->execute();
    $stmtDelete->close();

    // Preparar statements
    $stmtPrenomina = $conex->prepare("
        INSERT INTO PrenominaEspecial (nomina, nombre, semana, anio, turno) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmtDetalle = $conex->prepare("
        INSERT INTO DetallesPrenominaEspecial (id_prenomina_especial, dia, tipo, valor) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($prenominaData as $registro) {
        $nomina = ltrim($registro['nomina'], '0'); // Quitar ceros a la izquierda
        $nombre = $registro['nombre'];
        $turno = $registro['turno'];
        $semana = $registro['semana'];
        $anio = $registro['anio'];
        $detalles = $registro['detalles'];

        // Insertar registro principal
        $stmtPrenomina->bind_param("ssiss", $nomina, $nombre, $semana, $anio, $turno);

        if (!$stmtPrenomina->execute()) {
            $errores[] = "Error al insertar nómina $nomina: " . $stmtPrenomina->error;
            continue;
        }

        $idPrenomina = $conex->insert_id;

        // Insertar detalles diarios
        foreach ($detalles as $detalle) {
            $dia = $detalle['dia'];
            $tipo = $detalle['tipo'];
            $valor = $detalle['valor'];

            $stmtDetalle->bind_param("iisd", $idPrenomina, $dia, $tipo, $valor);

            if (!$stmtDetalle->execute()) {
                $errores[] = "Error al insertar detalle para nómina $nomina, día $dia: " . $stmtDetalle->error;
            }
        }

        $insertados++;
    }

    $stmtPrenomina->close();
    $stmtDetalle->close();

    if (empty($errores)) {
        $conex->commit();
        echo json_encode([
            'status' => 'success',
            'message' => "Se insertaron $insertados registros correctamente."
        ]);
    } else {
        $conex->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Ocurrieron errores durante la inserción.',
            'detalles' => $errores
        ]);
    }

} catch (Exception $e) {
    $conex->rollback();
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ]);
} finally {
    $conex->close();
}
?>