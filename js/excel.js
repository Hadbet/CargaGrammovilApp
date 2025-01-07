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
        // Leer el archivo Excel
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

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
            Swal.fire({
                icon: 'success',
                title: 'Actualización exitosa',
                text: result.message
            });
            setTimeout(function() {
                window.location.pathname = "RH/CargasGrammovilApp/dao/table_caja_ahorro.php";
            }, 1000);
        } else {
            throw new Error(result.message );
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Recargue la página e intente nuevamente.'
        });
    }
}
