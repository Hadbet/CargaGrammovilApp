<?php
include_once('db/db_Aux.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['inventarioDatos']) && is_array($inputData['inventarioDatos'])) {
        $todosExitosos = true;
        $errores = [];

        foreach ($inputData['inventarioDatos'] as $registroInventario) {

            $Nomina = isset($registroInventario['Nomina']) ? trim($registroInventario['Nomina']) : null;
            $PrimerApeido = isset($registroInventario['PrimerApeido']) ? trim($registroInventario['PrimerApeido']) : null;
            $SegundoApeido = isset($registroInventario['SegundoApeido']) ? trim($registroInventario['SegundoApeido']) : null;
            $Nombre = isset($registroInventario['Nombre']) ? trim($registroInventario['Nombre']) : null;
            $Antiguedad = isset($registroInventario['Antiguedad']) ? trim($registroInventario['Antiguedad']) : null;
            $Vacaciones = isset($registroInventario['Vacaciones']) ? trim($registroInventario['Vacaciones']) : null;

            if ($Nomina === null || $Nombre === null || $Nombre === null || $Antiguedad === null || $Vacaciones === null) {
                $errores[] = "Faltan datos para el registro Nomina: $Nomina, Nombre: $Nombre, Vacaciones: $Vacaciones, Antiguedad: $Antiguedad";
                $todosExitosos = false;
            } else {
                $respuestaInsert = insertarRegistrosInventario($Nomina, $Nombre.' '.$PrimerApeido.' '.$SegundoApeido, $Antiguedad, $Vacaciones);
                if ($respuestaInsert['status'] !== 'success') {
                    $errores[] = "Error al insertar el registro Nomina: $Nomina. " . $respuestaInsert['message'];
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


function insertarRegistrosInventario($Nomina, $Nombre, $Antiguedad, $Vacaciones) {
    $con = new LocalConector();
    $conex = $con->conectar();

    $conex->begin_transaction();

    try {
        $consultaExistente = $conex->prepare("SELECT * FROM `Vacaciones` WHERE `IdUser` = ? ");
        $consultaExistente->bind_param("s", $Nomina);
        $consultaExistente->execute();
        $consultaExistente->store_result();

        if ($consultaExistente->num_rows > 0) {
            $updateInventario = $conex->prepare("UPDATE `Vacaciones` SET `NomUser` = ?, `FechaIngreso` = ?, `DiasVacaciones` = ? WHERE `IdUser` = ? ");
            $updateInventario->bind_param("ssss",  $Nombre, $Antiguedad, $Vacaciones, $Nomina);
            $resultado = $updateInventario->execute();

            if (!$resultado) {
                $conex->rollback();
                $respuesta = array('status' => 'error', 'message' => 'Error al actualizar el registro con Nomina: ' . $Nomina . ', Nombre: '. $Nombre . ', Antiguedad:'. $Antiguedad .', Vacaciones: '.$Vacaciones);
            } else {
                $conex->commit();
                $respuesta = array('status' => 'success', 'message' => 'Registro actualizado correctamente.');
            }

            $updateInventario->close();

        } else {

            $insertParte = $conex->prepare("INSERT INTO  `Vacaciones` (`IdUser`, `NomUser`, `FechaIngreso`, `DiasVacaciones`)
                                            VALUES (?, ?, ?, ?)");
            $insertParte->bind_param("ssss", $Nomina, $Nombre, $Antiguedad, $Vacaciones);

            $resultado = $insertParte->execute();

            if (!$resultado) {
                $conex->rollback();
                $respuesta = array('status' => 'error', 'message' => 'Error en la BD al insertar el registro con Nomina: ' . $Nomina. ', Nombre: '. $Nombre . ', Antiguedad:'. $Antiguedad .', Vacaciones: '.$Vacaciones);
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