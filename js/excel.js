document.getElementById('btnExcelInventario').addEventListener('click', () => {
    document.getElementById('fileInputInventario').click();
});

document.getElementById('fileInputInventario').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelInventario(file);
    }
});
async function insertarExcelInventario(file) {
    try {
        document.getElementById("btnModal").click();
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        console.log(jsonData[0].length);

        if (jsonData[0].length===5){

            const inventarioData = jsonData.slice(1).map((row) => {
                return {
                    Nomina: row[0],
                    Nombre: row[1],
                    AhorroTotal: row[2],
                    PendientePrestamo: row[3],
                    FondoAhorro: row[4]
                };
            });

            const response = await fetch('dao/daoInsertarCajaAhorro.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ inventarioDatos: inventarioData })
            });

            const result = await response.json();

            if (result.status === "success") {
                document.getElementById("btnCloseM").click();
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización exitosa',
                    text: result.message
                });
                setTimeout(function() {
                    window.location.pathname = "RH/CargasGrammovilApp/table_caja_ahorro.php";
                }, 1000);
            } else {
                document.getElementById("btnCloseM").click();
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrio un problema',
                    text: result.message
                });
                throw new Error(result.message );
            }
        }else {
            document.getElementById("btnCloseM").click();
            let timerInterval;
            Swal.fire({
                title: "El archivo no pertenece a la base.",
                html: "Te regresaremos a la pagina <b></b> milliseconds.",
                timer: 1500,
                timerProgressBar: true,
                icon: "error",
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    location.reload();
                }
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Recargue la página e intente nuevamente.'
        });
    }
}



document.getElementById('btnExcelVacaciones').addEventListener('click', () => {
    document.getElementById('fileInputVacaciones').click();
});

document.getElementById('fileInputVacaciones').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelVacaciones(file);
    }
});
async function insertarExcelVacaciones(file) {
    try {

        document.getElementById("btnModal").click();
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        console.log(jsonData[0].length);

        if (jsonData[0].length===7){
            const inventarioData = jsonData.slice(1).map((row) => {
                let dateParts = row[4].split("/");
                let formattedDate = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]);

                return {
                    Nomina: row[0],
                    PrimerApeido: row[1],
                    SegundoApeido: row[2],
                    Nombre: row[3],
                    Antiguedad: formattedDate.toISOString().split('T')[0],
                    Vacaciones: row[6]
                };
            });
            const response = await fetch('dao/daoInsertarVacaciones.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ inventarioDatos: inventarioData })
            });

            const result = await response.json();

            if (result.status === "success") {
                document.getElementById("btnCloseM").click();
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización exitosa',
                    text: result.message
                });
                setTimeout(function() {
                    window.location.pathname = "RH/CargasGrammovilApp/table_vacaciones.php";
                }, 1000);
            } else {
                document.getElementById("btnCloseM").click();
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrio un problema',
                    text: result.message
                });
                throw new Error(result.message );
            }
        }else{
            document.getElementById("btnCloseM").click();
            let timerInterval;
            Swal.fire({
                title: "El archivo no pertenece a la base.",
                html: "Te regresaremos a la pagina <b></b> milliseconds.",
                timer: 1500,
                timerProgressBar: true,
                icon: "error",
                didOpen: () => {
                    Swal.showLoading();
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {
                        timer.textContent = `${Swal.getTimerLeft()}`;
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    location.reload();
                }
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Recargue la página e intente nuevamente.'
        });
    }
}

// Lógica para el botón de Asistencias
document.getElementById('btnExcelAsistencias').addEventListener('click', () => {
    document.getElementById('fileInputAsistencias').click();
});

document.getElementById('fileInputAsistencias').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelAsistencias(file);
    }
});

async function insertarExcelAsistencias(file) {
    try {
        document.getElementById("btnModal").click();
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: null });

        const semana = jsonData[0][1]; // 'WK' en A1, semana en B1
        const anio = new Date().getFullYear();

        // CORRECCIÓN 1: Se limpian los encabezados para eliminar espacios en blanco al inicio o final
        const headers = jsonData[1].map(h => (h ? h.toString().trim() : h));

        const nominaIndex = headers.indexOf('NO.');
        const nombreIndex = headers.indexOf('NOMBRE');
        const turnoIndex = headers.indexOf('TURNO');
        const faltasIndex = headers.findIndex(h => h && h.toUpperCase().includes('TOTAL FALTAS'));
        const teIndex = headers.findIndex(h => h && h.toUpperCase().includes('TOTAL TE'));
        // Se usa el mismo método para encontrar OBSERVACIONES de forma segura
        const observacionesIndex = headers.findIndex(h => h && h.toUpperCase().includes('OBSERVACIONES'));

        // Se filtran solo los encabezados que son numéricos (los días del mes)
        const dias = headers.filter(h => h && !isNaN(parseInt(h, 10)) && parseInt(h, 10) >= 1 && parseInt(h, 10) <= 31);

        if (nominaIndex === -1 || nombreIndex === -1 || turnoIndex === -1) {
            throw new Error('El formato del Excel no es correcto. Faltan las columnas NO., NOMBRE o TURNO.');
        }

        const asistenciasData = jsonData.slice(2).map((row) => {
            if (!row[nominaIndex]) return null; // Si no hay nómina en la fila, se ignora

            const detallesDiarios = [];
            dias.forEach(dia => {
                const diaIndex = headers.indexOf(dia);
                if (diaIndex !== -1 && diaIndex < row.length) {

                    // CORRECCIÓN 2: Se ajusta el orden de lectura para que coincida con el Excel.
                    // El tipo (letra 'A', 'D', 'IEG') está en la columna del día.
                    const tipoRaw = row[diaIndex];
                    // El valor numérico ('3', '4', '0') está en la columna siguiente (la que tiene 'TE' en el header).
                    const valorRaw = row[diaIndex + 1];

                    detallesDiarios.push({
                        dia: parseInt(dia, 10),
                        // Se asegura que el valor sea un número, reemplazando comas por puntos.
                        valor: valorRaw !== null && valorRaw !== '' ? parseFloat(valorRaw.toString().replace(',', '.')) : null,
                        tipo: tipoRaw || null
                    });
                }
            });

            return {
                nomina: row[nominaIndex],
                nombre: row[nombreIndex] ? row[nombreIndex].trim() : '',
                turno: row[turnoIndex],
                semana: parseInt(semana, 10),
                anio: anio,
                total_faltas: faltasIndex !== -1 && row[faltasIndex] ? parseFloat(row[faltasIndex].toString().replace(',', '.')) : 0,
                total_te: teIndex !== -1 && row[teIndex] ? parseFloat(row[teIndex].toString().replace(',', '.')) : 0,
                observaciones: observacionesIndex !== -1 ? (row[observacionesIndex] || '') : '',
                detalles: detallesDiarios
            };
        }).filter(Boolean); // Se eliminan las filas que resultaron nulas

        const response = await fetch('https://grammermx.com/RH/CargasGrammovilApp/dao/daoInsertarAsistencias.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ asistenciasData: asistenciasData })
        });

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("btnCloseM").click();
            Swal.fire({
                icon: 'success',
                title: 'Carga exitosa',
                text: result.message
            });
        } else {
            document.getElementById("btnCloseM").click();
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un problema',
                text: result.message + (result.detalles ? `\nDetalles: ${result.detalles.join(', ')}` : '')
            });
        }

    } catch (error) {
        console.error("Error procesando el archivo de asistencias:", error);
        document.getElementById("btnCloseM").click();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Revisa la consola para más detalles.'
        });
    }
}

// Lógica para el botón de Prenómina Especial
document.getElementById('btnExcelPrenominaEspecial').addEventListener('click', () => {
    document.getElementById('fileInputPrenominaEspecial').click();
});

document.getElementById('fileInputPrenominaEspecial').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelPrenominaEspecial(file);
    }
});
// VERSIÓN CORREGIDA - Reemplaza la función insertarExcelPrenominaEspecial
async function insertarExcelPrenominaEspecial(file) {
    try {
        document.getElementById("btnModal").click();
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: null });

        // Verificar si es prenómina especial
        if (jsonData[0][0] !== 'WK' || jsonData[0][1] !== 'ESPECIAL') {
            throw new Error('El archivo no parece ser una prenómina especial válida.');
        }

        // Para prenómina especial, usaremos la semana actual o un valor predeterminado
        // Ya que el archivo dice "ESPECIAL" en lugar de un número de semana
        const semana = 99; // Código especial para prenómina especial
        const anio = new Date().getFullYear();

        // Limpiar encabezados de la fila 2 (índice 1)
        const headers = jsonData[1].map(h => (h ? h.toString().trim() : h));

        const nominaIndex = headers.indexOf('NO.');
        const nombreIndex = headers.indexOf('NOMBRE');
        const turnoIndex = headers.indexOf('TURNO');

        // Encontrar columnas de días (números del 1 al 31)
        const diasIndices = [];
        headers.forEach((h, index) => {
            if (h && !isNaN(parseInt(h, 10)) && parseInt(h, 10) >= 1 && parseInt(h, 10) <= 31) {
                diasIndices.push({
                    dia: parseInt(h, 10),
                    colIndex: index
                });
            }
        });

        console.log('Días encontrados:', diasIndices);

        if (nominaIndex === -1 || nombreIndex === -1 || turnoIndex === -1) {
            throw new Error('El formato del Excel no es correcto. Faltan las columnas NO., NOMBRE o TURNO.');
        }

        if (diasIndices.length === 0) {
            throw new Error('No se encontraron columnas de días en el archivo.');
        }

        const prenominaData = jsonData.slice(2).map((row) => {
            // Verificar que la fila tenga datos válidos
            if (!row[nominaIndex] || row[nominaIndex] === null) return null;

            const detallesDiarios = [];

            // Para cada día encontrado
            diasIndices.forEach(diaInfo => {
                const colIndex = diaInfo.colIndex;
                const dia = diaInfo.dia;

                // El tipo está en la columna del día
                const tipoRaw = row[colIndex];
                // El valor TE está en la columna siguiente
                const valorRaw = row[colIndex + 1];

                // Solo agregar si hay algún dato
                if (tipoRaw !== null || valorRaw !== null) {
                    detallesDiarios.push({
                        dia: dia,
                        tipo: tipoRaw ? tipoRaw.toString().trim() : null,
                        valor: valorRaw !== null && valorRaw !== '' && !isNaN(valorRaw)
                            ? parseFloat(valorRaw.toString().replace(',', '.'))
                            : null
                    });
                }
            });

            return {
                nomina: row[nominaIndex].toString().trim(),
                nombre: row[nombreIndex] ? row[nombreIndex].toString().trim() : '',
                turno: row[turnoIndex] ? row[turnoIndex].toString().trim() : '',
                semana: semana, // 99 para prenómina especial
                anio: anio,
                detalles: detallesDiarios
            };
        }).filter(Boolean); // Eliminar filas nulas

        console.log('Registros procesados:', prenominaData.length);
        console.log('Primer registro:', prenominaData[0]);

        if (prenominaData.length === 0) {
            throw new Error('No se encontraron registros válidos en el archivo.');
        }

        const response = await fetch('https://grammermx.com/RH/CargasGrammovilApp/dao/daoInsertarPrenominaEspecial.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ prenominaData: prenominaData })
        });

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("btnCloseM").click();
            Swal.fire({
                icon: 'success',
                title: 'Carga exitosa',
                text: result.message
            });
        } else {
            document.getElementById("btnCloseM").click();
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un problema',
                text: result.message + (result.detalles ? `\nDetalles: ${result.detalles.join(', ')}` : '')
            });
        }

    } catch (error) {
        console.error("Error procesando el archivo de prenómina especial:", error);
        document.getElementById("btnCloseM").click();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Revisa la consola para más detalles.'
        });
    }
}

