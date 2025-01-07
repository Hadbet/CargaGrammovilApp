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
  <body class="vertical  light  ">
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
                      <p class="text-white mb-4">Recuarda cargar el excel con los datos especificos.</p>
                      <a href="form_idea.html" class="btn btn-lg bg-secondary-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
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
                      <a href="form_kaizen.html" class="btn btn-lg bg-primary-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
                    </div> <!-- .card-body -->
                  </div> <!-- .card -->
                </div> <!-- .col-md-->
                <div class="col-md-4">
                  <div class="card shadow bg-success text-center mb-4">
                    <div class="card-body p-5">
                      <span class="circle circle-md bg-success-light">
                        <i class="fe fe-smile fe-24 text-white"></i>
                      </span>
                      <h3 class="h4 mt-4 mb-1 text-white">Usuarios</h3>
                      <p class="text-white mb-4">Recuerda cargar el excel con los datos especificos.</p>
                      <a href="form_best_practice.html" class="btn btn-lg bg-success-light text-white">Subir<i class="fe fe-arrow-right fe-16 ml-2"></i></a>
                    </div> <!-- .card-body -->
                  </div> <!-- .card -->
                </div> <!-- .col-md-->
              </div> <!-- .row -->
            </div> <!-- .col-12 -->
          </div> <!-- .row -->
        </div> <!-- .container-fluid -->
      </main> <!-- main -->
    </div> <!-- .wrapper -->

    <?php include 'estaticos/scriptEstandar.php'; ?>

  </body>
</html>