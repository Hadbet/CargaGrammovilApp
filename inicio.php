<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/images/Grammer_Logo.ico">
    <title>Carga de bases de datos</title>
    <!-- Simple bar CSS -->
    <link rel="stylesheet" href="css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="css/feather.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="css/daterangepicker.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="css/app-light.css" id="lightTheme">
</head>
<body class="vertical light">
<div class="wrapper">

    <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
            <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
    </nav>

    <?php require_once('estaticos/navegador.php'); ?>

    <main role="main" class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="w-50 mx-auto text-center justify-content-center py-5 my-5">
                        <h1 class="page-title mb-0">¿Qué base de datos actualizaras?</h1>
                    </div>
                    <div class="row my-4">
                        <div class="col-md-4">
                            <div class="card shadow bg-secondary text-center mb-4">
                                <div class="card-body p-5">
                      <span class="circle circle-md bg-secondary-light">
                        <i class="fe fe-message-circle fe-24 text-white"></i>
                      </span>
                                    <h3 class="h4 mt-4 mb-1 text-white">Vacaciones</h3>
                                    <p class="text-white mb-4">Recuerda cargar el excel con los datos especificos.</p>
                                    <a href="#" id="btnExcelVacaciones" class="btn btn-lg bg-secondary-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
                                    <input type="file" id="fileInputVacaciones" accept=".xlsx, .xls"
                                           style="display: none;"/>
                                </div> <!-- .card-body -->
                            </div> <!-- .card -->
                        </div> <!-- .col-md-->
                        <div class="col-md-4">
                            <div class="card shadow bg-primary text-center mb-4">
                                <div class="card-body p-5">
                      <span class="circle circle-md bg-primary-light">
                        <i class="fe fe-navigation fe-24 text-white"></i>
                      </span>
                                    <h3 class="h4 mt-4 mb-1 text-white">Caja Ahorro</h3>
                                    <p class="text-white mb-4">Recuerda cargar el excel con los datos especificos.</p>
                                    <a href="#" id="btnExcelInventario" class="btn btn-lg bg-primary-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
                                    <input type="file" id="fileInputInventario" accept=".xlsx, .xls"
                                           style="display: none;"/>
                                </div> <!-- .card-body -->
                            </div> <!-- .card -->
                        </div> <!-- .col-md-->
                        <!-- INICIO: Nuevo Card para Asistencias -->
                        <div class="col-md-4">
                            <div class="card shadow bg-info text-center mb-4">
                                <div class="card-body p-5">
                      <span class="circle circle-md bg-info-light">
                        <i class="fe fe-calendar fe-24 text-white"></i>
                      </span>
                                    <h3 class="h4 mt-4 mb-1 text-white">Asistencias</h3>
                                    <p class="text-white mb-4">Carga el reporte de asistencias semanales.</p>
                                    <a href="#" id="btnExcelAsistencias" class="btn btn-lg bg-info-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
                                    <input type="file" id="fileInputAsistencias" accept=".xlsx, .xls" style="display: none;"/>
                                </div> <!-- .card-body -->
                            </div> <!-- .card -->
                        </div> <!-- .col-md-->
                        <!-- FIN: Nuevo Card para Asistencias -->
                    </div> <!-- .row -->

                    <!-- Button trigger modal -->
                    <button style="display: none" type="button" class="btn mb-2 btn-outline-success" data-toggle="modal" data-target="#verticalModal" id="btnModal"> Launch demo modal </button>

                </div> <!-- .col-12 -->
            </div> <!-- .row -->
        </div> <!-- .container-fluid -->
    </main> <!-- main -->

    <!-- Modal -->
    <div class="modal fade" id="verticalModal" tabindex="-1" role="dialog" aria-labelledby="verticalModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group" style="text-align: center;">
                        <img src="assets/images/carga.gif" style="width: 60%">
                    </div>
                </div>
                <div class="modal-footer" style="display: none">
                    <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal" id="btnCloseM">Close</button>
                </div>
            </div>
        </div>
    </div>

</div> <!-- .wrapper -->

<?php include 'estaticos/scriptEstandar.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="js/apps.js"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>
<script src="js/excel.js"></script>
</body>
</html>
