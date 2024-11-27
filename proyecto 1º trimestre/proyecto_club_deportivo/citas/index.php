<?php
    require_once "../config/config.php";
    require_once "../config/funciones.php";

    $conexion = conectar($nombre_host, $nombre_usuario, $password_db, $nombre_db);
    if (!$conexion) { 
        echo "Error en la conexión"; 
        die();
    } 
 
    // $consulta->close();
    // $conexion->close();

    //aca llamo a los archivos para las funciones necesarias
    require_once "../funciones/funcionesCitas.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/otros.css">
    <title>Citas</title>
    <style>
    /* Celdas con citas destacadas */
    .has-citas {
        background-color: #6c757d; /* Color secundario de Bootstrap */
        color: #fff;
        cursor: pointer;
    }

    .has-citas:hover {
        background-color: #5a6268;
    }

    /* Títulos del calendario */
    #monthYear {
        font-weight: bold;
    }

    /* Tarjetas de citas */
    .card {
        border: 1px solid #ddd;
    }

    .card .card-body {
        line-height: 1.5;
    }





/* Mantén la celda como posición relativa para limitar el alcance */
td.has-citas {
    position: relative;
}

/* El enlace ahora solo afecta a su contenedor inmediato */
td a.stretched-link {
    display: block;
    height: 100%;
    width: 100%;
    text-align: center;
}

/* Soluciona cualquier interferencia adicional */
td a.stretched-link:hover {
    text-decoration: none;
}


</style>

</head>
<body>
    <?php
        $nivel = 1;
        $titulo = "Citas";
        require_once "../requires/cabecera.php"
    ?>

    <main>
    <section class="container py-4 seccion_servicios">
        <a href="agregar.php" class="btn btn-primary mb-4">Crear una nueva Cita</a>

        <!-- Este miniformulario sera usado para la busqueda de citas por nombre del socio, por la fecha o por el servicio contratado. -->
        <form method="GET" action="" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-8">
                    <label for="buscar" class="form-label">Buscar el nombre del socio, por la fecha o por el servicio contratado</label>
                    <input 
                        type="text" 
                        name="buscar" 
                        id="buscar" 
                        class="form-control" 
                        placeholder="Buscar..." 
                        value="<?php  isset($_GET['buscar']) ? $_GET['buscar'] : '' ?>"
                    />
                </div>
                <div class="col-12 col-md-4 mt-auto">
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </div>
            </div>
        </form>

        <?php
            $dia = isset($_GET['dia']) ? (int)$_GET['dia'] : null;
            $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');
            $anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
            $filtro = isset($_GET['buscar']) ? $_GET['buscar'] : null;


            //obtengo citas del mes actual(digo mes actual al mes que se ve por pantalla o al mes que se seleccione)
            $citasPorMes = getCitasPorMes($conexion, $mes, $anio);

            //calcula mes anterior y siguiente
            $navegacion = calcularMesAnteriorSiguiente($mes, $anio);

            //obtengo días del mes
            $totalDias = obtenerDiasDelMes($mes, $anio);

            // dia de la semana en el que comienza el mes
            //esto es para saber en el calendario por que dia de la semana empieza el primero de ese mes
            // por ejemplo el 1 de nov de 2024 empieza el dia viernes
            $primerDiaSemana = date('N', strtotime("$anio-$mes-01"));


            if ($filtro){
                $citas = buscarCitasConDetalles($conexion, $filtro);
                $titulo = "<p class='text-muted'>Resultados para: <strong>" . $filtro ."</strong></p>";
            }
            elseif ($dia) {
                // Este if es para el contenedor_lista_citas, compruebo si se mandó un dia por GET para saber si listo las citas por dia, sino los listo por ese mes
                // Si hay un dia, construir la fecha completa y obtener las citas de ese día
                $fechaSeleccionada = "$anio-$mes-" . ($dia < 10 ? "0$dia" : $dia);
                $citas = getCitasPorDiaConDetalles($conexion, $fechaSeleccionada);
                $titulo = "Citas del día: " . date('j \d\e F \d\e Y', strtotime($fechaSeleccionada));
            } else {
                // Si no hay día, obtener citas del mes actual
                $fechaInicio = "$anio-$mes-01";
                $fechaFin = date("Y-m-t", strtotime($fechaInicio)); // Último día del mes
                $citas = getCitasPorMesConDetalles($conexion, $fechaInicio, $fechaFin); // Nueva función
                $titulo = "Citas del mes: " . date('F \d\e Y', strtotime($fechaInicio));
            }

            //Ajustar navegación
            $mesAnteriorUrl = "index.php?mes={$navegacion['mesAnterior']}&anio={$navegacion['anioAnterior']}";
            $mesSiguienteUrl = "index.php?mes={$navegacion['mesSiguiente']}&anio={$navegacion['anioSiguiente']}";
            
            //Obtener nombre del mes y año
            $nombreMes = date('F', strtotime("$anio-$mes-01"));
        ?>


        <!-- <div class="container my-5">
            <h1 class="text-center">Calendario de Citas</h1>
            <div class="d-flex justify-content-between my-4">
                <a href="?mes=<?= $navegacion['mesAnterior'] ?>&anio=<?= $navegacion['anioAnterior'] ?>" class="btn btn-primary">← Mes Anterior</a>
                <h2><?= date('F Y', strtotime("$anio-$mes-01")) ?></h2>
                <a href="?mes=<?= $navegacion['mesSiguiente'] ?>&anio=<?= $navegacion['anioSiguiente'] ?>" class="btn btn-primary">Mes Siguiente →</a>
            </div>
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th>Lunesss</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                    <th>Sábado</th>
                    <th>Domingo</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // 7. Imprimir filas del calendario
                $dia = 1 - $primerDiaSemana; // Comienza con los días vacíos previos al 1
                while ($dia <= $totalDias) {
                    echo "<tr>";
                    for ($columna = 0; $columna < 7; $columna++) {
                        if ($dia > 0 && $dia <= $totalDias) {
                            // Verificar si el día tiene citas
                            $fechaActual = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
                            $tieneCitas = isset($citasPorMes[$fechaActual]);
                            $clase = $tieneCitas ? 'bg-info text-white' : '';
                            $contenido = $tieneCitas ? "($citasPorMes[$fechaActual])" : '';
                            
                            echo "<td class='$clase'>";
                            echo "<a href='?mes=$mes&anio=$anio&dia=$dia'>$dia $contenido</a>";
                            echo "</td>";
                        } else {
                            echo "<td></td>"; // Celdas vacías
                        }
                        $dia++;
                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div> -->

        <!-- <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Socio</th>
                    <th>Teléfono</th>
                    <th>Servicio</th>
                </tr>
            </thead>
            <tbody>
                <?php// foreach ($citas as $cita): ?>
                    <tr>
                        <td><?php //echo htmlspecialchars($cita['fecha']); ?></td>
                        <td><?php //echo htmlspecialchars($cita['hora']); ?></td>
                        <td><?php //echo htmlspecialchars($cita['socio_nombre']); ?></td>
                        <td><?php //echo htmlspecialchars($cita['socio_telefono']); ?></td>
                        <td><?php //echo htmlspecialchars($cita['servicio_descripcion']); ?></td>
                    </tr>
                <?php// endforeach; ?>
            </tbody>
        </table> -->
   

        <?php

            if(count($citas) > 0){
                //echo "·sss";
                
            }else {
                //echo "<p class='text-danger'>No se encontraron resultados.</p>";
            }

        ?>


        <!-- //ESTE ES EL CALENDARIO DINAMICO (se muestra cuando no se este buscando nada por el filtro)-->
        <?php if ($filtro == null):  ?>  

            <div class="contenedor_calendario py-4">
                <!-- Navegación de meses -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="<?= $mesAnteriorUrl ?>" class="btn btn-primary">← Mes Anterior</a>
                    <h2 class="text-center mb-0" id="monthYear"><?= $nombreMes . " $anio" ?></h2>
                    <a href="<?= $mesSiguienteUrl ?>" class="btn btn-primary">Mes Siguiente →</a>
                </div>

                <!-- Calendario -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center shadow-sm calendar-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Lunes</th>
                                <th>Martes</th>
                                <th>Miércoles</th>
                                <th>Jueves</th>
                                <th>Viernes</th>
                                <th>Sábado</th>
                                <th>Domingo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ajustar día inicial para que el Lunes sea el primer día
                            $primerDiaSemana = ($primerDiaSemana === 0) ? 6 : $primerDiaSemana - 1; // Ajustar Domingo a 6, resto de días Lunes = 0

                            //esta resta es para obtener el dia por el cual iniciara el mes, esta echo asi por que permite obtener dias negativos
                            //esto messirve por que por ejemplo si el mes por el viernes $primerDiaSemana=5-1, el $dia sera un negativo = 1 - 4
                            // osea $dia seria -3, porlotanto del -3 al 0 son 4 dias los cuales no se pintarian en el calendario
                            // entonces del lunes al jueves son dias en blanco, y el mes empezaria a partir del viernes con $dia = 1
                            $dia = 1 - $primerDiaSemana;

                            // Crear un array asociativo de citas por fecha para facilitar acceso
                            //de este modo podre acceder a la candidad de citas dependiendo del dia mas facilmente
                            $citasPorFecha = [];
                            foreach ($citasPorMes as $cita) {
                                $citasPorFecha[$cita['dia']] = $cita['total'];
                            }

                            //este while se ejecuta siempre que el dia que se analice sea menor al total de dias de ese mes
                            while ($dia <= $totalDias) {
                                echo '<tr>';
                                for ($columna = 0; $columna < 7; $columna++) {
                                    if ($dia > 0 && $dia <= $totalDias) {
                                        // Construir la fecha en formato YYYY-MM-DD
                                        //este campo $diaFormateado simplemente es para tener los dias como: "01" en lugar de "1"
                                        $diaFormateado = $dia < 10 ? "0$dia" : $dia;
                                        $fechaActual = "$anio-$mes-$diaFormateado";

                                        //verificar si el dia tiene citas
                                        $cantidadCitas = isset($citasPorFecha[$fechaActual]) ? $citasPorFecha[$fechaActual] : 0;

                                        // Clases y contenido dinámico
                                        $claseDia = $cantidadCitas > 0 ? 'bg-secondary text-white has-citas position-relative' : 'position-relative';
                                        $colorTexto = $cantidadCitas > 0 ? 'text-white' : 'text-dark';
                                        $urlDia = "index.php?mes=$mes&anio=$anio&dia=$dia";
                                        $citasTexto = $cantidadCitas > 1 ? "$cantidadCitas citas" : ($cantidadCitas === 1 ? "1 cita" : "");

                                        echo "<td class='$claseDia'>";
                                        echo "<a href='$urlDia' class='stretched-link $colorTexto text-decoration-none'>";
                                            echo $dia;
                                            if ($cantidadCitas > 0) {
                                                echo "<div class='small $colorTexto'>$citasTexto</div>";
                                            }
                                        echo "</a>";
                                        echo '</td>';
                                    } else {
                                        echo '<td></td>';
                                    }
                                    $dia++;
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>   

        <?php endif; ?>



        
        <!-- Contenedor del Calendario -->
        <!-- <div class="contenedor_calendario py-4 ">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="index.php?mes=10&anio=2024" class="btn btn-primary">← Mes Anterior</a>
                <h2 class="text-center mb-0" id="monthYear">Noviembre 2024</h2>
                <a href="index.php?mes=12&anio=2024" class="btn btn-primary">Mes Siguiente →</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center shadow-sm calendar-table">
                    <thead class="bg-light">
                        <tr>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bg-secondary text-white has-citas position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=1" class="stretched-link text-white text-decoration-none">
                                    1
                                    <div class="small text-white">2 citas</div>
                                </a>
                            </td>
                            <td class="position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=2" class="stretched-link text-dark text-decoration-none">
                                    2
                                </a>
                            </td>
                            <td class="position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=3" class="stretched-link text-dark text-decoration-none">
                                    3
                                </a>
                            </td>
                            <td class="bg-secondary text-white has-citas position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=4" class="stretched-link text-white text-decoration-none">
                                    4
                                    <div class="small text-white">1 cita</div>
                                </a>
                            </td>
                            <td class="position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=5" class="stretched-link text-dark text-decoration-none">
                                    5
                                </a>
                            </td>
                            <td class="position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=6" class="stretched-link text-dark text-decoration-none">
                                    6
                                </a>
                            </td>
                            <td class="position-relative">
                                <a href="index.php?mes=11&anio=2024&dia=7" class="stretched-link text-dark text-decoration-none">
                                    7
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>  -->

        <!-- este es el dinamico -->
        <div class="contenedor_lista_citas py-4">
            <h3 class="text-center mb-4"><?= $titulo ?></h3>
            <div class="row row-cols-1 g-3">
                <?php 
                if (count($citas) > 0): //verifico si hay citas
                    foreach ($citas as $cita): ?>
                        <div class="col">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Servicio: <?= $cita['descripcion'] ?></h5>
                                    <p class="mb-2"><strong>Fecha:</strong> <?php echo date('j \d\e F \d\e Y', strtotime($cita['fecha'])); ?></p>
                                    <p class="mb-2"><strong>Hora:</strong> <?php echo date('g:i A', strtotime($cita['hora'])) ?></p>
                                    <p class="mb-2"><strong>Socio:</strong> <?php echo $cita['nombre'] ?></p>
                                    <p class="mb-2"><strong>Teléfono:</strong> <?php echo $cita['telefono'] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php //verifico si se ha pasado un filtro, si se paso un filtro y no encontro nada manda el siguiente mensaje
                    elseif($filtro): ?>
                    <div class="col">
                        <p class='text-danger'>No se encontraron resultados.</p>
                    </div>
                <?php else: ?>
                    <div class="col">
                        <p class="text-center">No hay citas disponibles para el período seleccionado.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>



        <!-- Contenedor de la Lista de Citas -->
        <!-- <div class="contenedor_lista_citas py-4">
            <h3 class="text-center mb-4">Todas las citas</h3>
            <div class="row row-cols-1 g-3">
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Servicio: Entrenamiento Personal</h5>
                            <p class="mb-2"><strong>Fecha:</strong> 1 de Noviembre de 2024</p>
                            <p class="mb-2"><strong>Hora:</strong> 10:00 AM</p>
                            <p class="mb-2"><strong>Socio:</strong> Juan Pérez</p>
                            <p class="mb-2"><strong>Teléfono:</strong> 555-123-4567</p>
                        </div>
                    </div>
                </div>
           
            </div>
        </div> --> 


    </section>


    </main>

    <?php
        include('../requires/footer.php');
    ?>
</body>
</html>