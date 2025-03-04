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
