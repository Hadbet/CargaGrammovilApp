<?php
include_once('db/db_Aux.php'); // Ajusta la ruta a tu conector de BD si es necesario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['asistenciasData']) && is_array($inputData['asistenciasData'])) {
        $todosExitosos = true;
        $errores = [];

        foreach ($inputData['asistenciasData'] as $registroAsistencia) {
            // Se valida que el registro tenga al menos una nómina para ser procesado
            if (empty($registroAsistencia['nomina'])) {
                continue;
            }

            $respuestaInsert = insertarRegistroAsistencia($registroAsistencia);
            if ($respuestaInsert['status'] !== 'success') {
                $errores[] = "Error en nómina " . ($registroAsistencia['nomina'] ?? 'N/A') . ": " . $respuestaInsert['message'];
                $todosExitosos = false;
                // Si se desea detener el proceso al encontrar el primer error, descomentar la siguiente línea
                // break;
            }
        }

        if ($todosExitosos) {
            $respuesta = array("status" => 'success', "message" => "Todos los registros de asistencia fueron procesados correctamente.");
        } else {
            $respuesta = array("status" => 'error', "message" => "Se encontraron errores al procesar algunos registros.", "detalles" => $errores);
        }
    } else {
        $respuesta = array("status" => 'error', "message" => "Los datos enviados no tienen el formato correcto.");
    }
} else {
    $respuesta = array("status" => 'error', "message" => "Método no permitido. Se esperaba POST.");
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

        // 1. Borrar registros previos para la misma nómina, semana y año para evitar duplicados.
        $stmtDeleteDetalles = $conex->prepare("
            DELETE dd FROM DetallesAsistenciaDiaria dd
            JOIN AsistenciasSemanales asw ON dd.id_asistencia_semanal = asw.id
            WHERE asw.nomina = ? AND asw.semana = ? AND asw.anio = ?
        ");
        $stmtDeleteDetalles->bind_param("sii", $nomina, $semana, $anio);
        if (!$stmtDeleteDetalles->execute()) { throw new Exception("Error al limpiar detalles previos: " . $stmtDeleteDetalles->error); }
        $stmtDeleteDetalles->close();

        $stmtDeleteSemanal = $conex->prepare("DELETE FROM AsistenciasSemanales WHERE nomina = ? AND semana = ? AND anio = ?");
        $stmtDeleteSemanal->bind_param("sii", $nomina, $semana, $anio);
        if (!$stmtDeleteSemanal->execute()) { throw new Exception("Error al limpiar registro semanal previo: " . $stmtDeleteSemanal->error); }
        $stmtDeleteSemanal->close();

        // 2. Insertar el registro principal en la tabla `AsistenciasSemanales`
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
            $data['observaciones'] // Este campo ahora se leerá correctamente
        );

        if (!$stmtInsertSemanal->execute()) {
            throw new Exception("Error al insertar el registro semanal: " . $stmtInsertSemanal->error);
        }

        $idAsistenciaSemanal = $conex->insert_id;
        $stmtInsertSemanal->close();

        // 3. Insertar cada uno de los detalles diarios
        $stmtInsertDetalle = $conex->prepare("
            INSERT INTO DetallesAsistenciaDiaria (id_asistencia_semanal, dia, valor, tipo)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($data['detalles'] as $detalle) {
            // Se usa una variable para el valor para poder pasarla por referencia a bind_param
            // y asegurar que si el valor es null, se inserte NULL en la base de datos.
            $valorAInsertar = $detalle['valor'];

            $stmtInsertDetalle->bind_param(
                "iids",
                $idAsistenciaSemanal,
                $detalle['dia'],
                $valorAInsertar, // Se pasa la variable con el valor numérico
                $detalle['tipo']      // Se pasa la variable con el tipo (letra)
            );
            if (!$stmtInsertDetalle->execute()) {
                throw new Exception("Error al insertar detalle del día " . $detalle['dia'] . ": " . $stmtInsertDetalle->error);
            }
        }
        $stmtInsertDetalle->close();

        // Si todo fue exitoso, se confirman los cambios
        $conex->commit();
        $respuesta = array('status' => 'success', 'message' => 'Registro procesado exitosamente.');

    } catch (Exception $e) {
        // Si ocurre cualquier error, se revierten todos los cambios
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    } finally {
        $conex->close();
    }

    return $respuesta;
}
?>

