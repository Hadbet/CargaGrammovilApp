<?php
include_once('db/db_Aux.php'); // Ajusta la ruta si es necesario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['asistenciasData']) && is_array($inputData['asistenciasData'])) {
        $todosExitosos = true;
        $errores = [];

        foreach ($inputData['asistenciasData'] as $registroAsistencia) {
            $respuestaInsert = insertarRegistroAsistencia($registroAsistencia);
            if ($respuestaInsert['status'] !== 'success') {
                $errores[] = "Error en nómina " . ($registroAsistencia['nomina'] ?? 'N/A') . ": " . $respuestaInsert['message'];
                $todosExitosos = false;
                // Si un registro falla, detenemos el proceso para no continuar con errores.
                break;
            }
        }

        if ($todosExitosos) {
            $respuesta = array("status" => 'success', "message" => "Todos los registros de asistencia fueron procesados correctamente.");
        } else {
            $respuesta = array("status" => 'error', "message" => "Se encontraron errores al procesar los registros.", "detalles" => $errores);
        }
    } else {
        $respuesta = array("status" => 'error', "message" => "Datos de asistencia no válidos o ausentes.");
    }
} else {
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD POST");
}

echo json_encode($respuesta);

function insertarRegistroAsistencia($data) {
    $con = new LocalConector();
    $conex = $con->conectar();
    $conex->begin_transaction();

    try {
        $nomina = $data['nomina'];
        $semana = $data['semana'];
        $anio = $data['anio'];

        // 1. Antes de insertar, borramos cualquier registro existente para esa nómina, semana y año.
        // Esto asegura que siempre se cargue la información más reciente del Excel.
        $stmtDeleteDetalles = $conex->prepare("
            DELETE dd FROM DetallesAsistenciaDiaria dd
            JOIN AsistenciasSemanales as ON dd.id_asistencia_semanal = as.id
            WHERE as.nomina = ? AND as.semana = ? AND as.anio = ?
        ");
        $stmtDeleteDetalles->bind_param("sii", $nomina, $semana, $anio);
        $stmtDeleteDetalles->execute();
        $stmtDeleteDetalles->close();

        $stmtDeleteSemanal = $conex->prepare("DELETE FROM AsistenciasSemanales WHERE nomina = ? AND semana = ? AND anio = ?");
        $stmtDeleteSemanal->bind_param("sii", $nomina, $semana, $anio);
        $stmtDeleteSemanal->execute();
        $stmtDeleteSemanal->close();

        // 2. Insertamos el nuevo registro en la tabla principal `AsistenciasSemanales`
        $stmtInsertSemanal = $conex->prepare("
            INSERT INTO AsistenciasSemanales (nomina, nombre, turno, semana, anio, total_faltas, total_te, observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsertSemanal->bind_param(
            "sssiidds",
            $data['nomina'],
            $data['nombre'],
            $data['turno'],
            $data['semana'],
            $data['anio'],
            $data['total_faltas'],
            $data['total_te'],
            $data['observaciones']
        );

        if (!$stmtInsertSemanal->execute()) {
            throw new Exception("Error al insertar el registro semanal: " . $stmtInsertSemanal->error);
        }

        $idAsistenciaSemanal = $conex->insert_id; // Obtenemos el ID del registro que acabamos de crear
        $stmtInsertSemanal->close();

        // 3. Insertamos cada uno de los detalles diarios en la tabla `DetallesAsistenciaDiaria`
        $stmtInsertDetalle = $conex->prepare("
            INSERT INTO DetallesAsistenciaDiaria (id_asistencia_semanal, dia, valor, tipo)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($data['detalles'] as $detalle) {
            $stmtInsertDetalle->bind_param(
                "iids",
                $idAsistenciaSemanal,
                $detalle['dia'],
                $detalle['valor'],
                $detalle['tipo']
            );
            if (!$stmtInsertDetalle->execute()) {
                throw new Exception("Error al insertar detalle del día " . $detalle['dia'] . ": " . $stmtInsertDetalle->error);
            }
        }
        $stmtInsertDetalle->close();

        // Si todo salió bien, confirmamos la transacción
        $conex->commit();
        $respuesta = array('status' => 'success', 'message' => 'Registro procesado.');

    } catch (Exception $e) {
        // Si algo falló, revertimos todos los cambios de esta transacción
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    } finally {
        $conex->close();
    }

    return $respuesta;
}
?>
