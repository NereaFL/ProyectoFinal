<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Teatro Dorado Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css" />
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css" />
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css" />
    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="vendors/bootstrap-datepicker/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="shortcut icon" href="images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500&display=swap" rel="stylesheet">


  </head>

  <body>
    <div class="container-scroller">
        <?php include("templates/sidebar.php"); ?>

        <div class="container-fluid page-body-wrapper">
            <?php include("templates/navbar.php"); ?>
            

            <div class="main-panel">
            <div class="content-wrapper pb-0">

                <?php

                if(file_exists($path_modulo)) {
                    include($path_modulo);
                } else {
                     echo 'ERROR AL CARGAR EL MODULO <b>' . $modulo . '</b>. No existe el archivo <b>' . $archivo;
                }

                ?>

                
            </div>
            <?php include("templates/footer.php"); ?>
            </div>
        </div>
    </div>

    <?php include("templates/scripts.php"); ?>
  </body>
</html>

        
