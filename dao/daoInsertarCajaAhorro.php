<?php
include_once('db/db_Aux.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['inventarioDatos']) && is_array($inputData['inventarioDatos'])) {
        $todosExitosos = true;
        $errores = [];

        foreach ($inputData['inventarioDatos'] as $registroInventario) {

            $Nomina = isset($registroInventario['Nomina']) ? trim($registroInventario['Nomina']) : null;
            $Nombre = isset($registroInventario['Nombre']) ? trim($registroInventario['Nombre']) : null;
            $AhorroTotal = isset($registroInventario['AhorroTotal']) ? trim($registroInventario['AhorroTotal']) : null;
            $PendientePrestamo = isset($registroInventario['PendientePrestamo']) ? trim($registroInventario['PendientePrestamo']) : null;
            $FondoAhorro = isset($registroInventario['FondoAhorro']) ? trim($registroInventario['FondoAhorro']) : null;

            if ($Nomina === null || $Nombre === null || $AhorroTotal === null || $PendientePrestamo === null || $FondoAhorro === null) {
                $errores[] = "Faltan datos para el registro Nomina: $Nomina, Nombre: $Nombre, Ahorro Total: $AhorroTotal, Pendiente Prestamo: $PendientePrestamo, FondoAhorro: $FondoAhorro";
                $todosExitosos = false;
            } else {
                $respuestaInsert = insertarRegistrosInventario($Nomina, $Nombre, $AhorroTotal, $PendientePrestamo, $FondoAhorro);
                if ($respuestaInsert['status'] !== 'success') {
                    $errores[] = "Error al insertar el registro ID: $Nomina. " . $respuestaInsert['message'];
                    $todosExitosos = false;
                    break;
                }
            }
        }

        if ($todosExitosos) {
            $respuesta = array("status" => 'success', "message" => "Todos los registros en la Tabla fueron actualizados correctamente.");
        } else {
            $respuesta = array("status" => 'error', "message" => "Se encontraron errores al insertar los registros.", "detalles" => $errores);
        }
    } else {
        $respuesta = array("status" => 'error', "message" => "Datos no válidos.");
    }
} else {
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD POST");
}

echo json_encode($respuesta);


function insertarRegistrosInventario($Nomina, $Nombre, $AhorroTotal, $PendientePrestamo, $FondoAhorro) {
    $con = new LocalConector();
    $conex = $con->conectar();

    $conex->begin_transaction();

    try {
        $consultaExistente = $conex->prepare("SELECT * FROM `CajaAhorro` WHERE `Nomina` = ? ");
        $consultaExistente->bind_param("s", $Nomina);
        $consultaExistente->execute();
        $consultaExistente->store_result();

        if ($consultaExistente->num_rows > 0) {
            $updateInventario = $conex->prepare("UPDATE `CajaAhorro` SET `Nombre` = ?, `AhorroTotal` = ?, `PendientePrestamo` = ?, `FondoAhorro` = ? WHERE `Nomina` = ? ");
            $updateInventario->bind_param("sssss",  $Nombre, $AhorroTotal, $PendientePrestamo, $FondoAhorro, $Nomina);
            $resultado = $updateInventario->execute();

            if (!$resultado) {
                $conex->rollback();
                $respuesta = array('status' => 'error', 'message' => 'Error al actualizar el registro con Nomina: ' . $Nomina . ', Nombre: '. $Nombre . ', Ahorro Total:'. $AhorroTotal .', Prestamo: '.$PendientePrestamo);
            } else {
                $conex->commit();
                $respuesta = array('status' => 'success', 'message' => 'Registro actualizado correctamente.');
            }

            $updateInventario->close();

        } else {

            $insertParte = $conex->prepare("INSERT INTO  `CajaAhorro` (`Nomina`, `Nombre`, `AhorroTotal`, `PendientePrestamo`, `FondoAhorro`)
                                            VALUES (?, ?, ?, ?, ?)");
            $insertParte->bind_param("sssss", $Nomina, $Nombre, $AhorroTotal, $PendientePrestamo, $FondoAhorro);

            $resultado = $insertParte->execute();

            if (!$resultado) {
                $conex->rollback();
                $respuesta = array('status' => 'error', 'message' => 'Error en la BD al insertar el registro con Nomina: ' . $Nomina. ', Nombre: '. $Nombre . ', AhorroTotal:'. $AhorroTotal .', Prestamo: '.$PendientePrestamo);
            } else {
                $conex->commit();
                $respuesta = array('status' => 'success', 'message' => 'Registro insertado correctamente.');
            }

            $insertParte->close();
        }

        $consultaExistente->close();

    } catch (Exception $e) {
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    } finally {
        $conex->close();
    }

    return $respuesta;
}
?>