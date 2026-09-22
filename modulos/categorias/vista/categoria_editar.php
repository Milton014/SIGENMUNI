<?php

/*
|--------------------------------------------------------------------------
| RUTAS Y PROTECCIÓN CSRF
|--------------------------------------------------------------------------
|
| Esta vista trabaja exclusivamente mediante el Router.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';

$categoriaEditarId =
    (int)(
        $categoria['id']
        ?? 0
    );

$categoriaEditarAccion =
    sigenmuniUrlRuta(
        'categorias/editar',
        [
            'id' => $categoriaEditarId
        ]
    );

$categoriaEditarCancelar =
    sigenmuniUrlRuta(
        'categorias'
    );

$categoriaEditarCsrf =
    sigenmuniCsrfToken();

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Editar Categoría - SIGENMUNI
</title>


<style>

/* =========================================
   GENERAL
========================================= */

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    margin:0;
    background:#f4f7fb;
    color:#1f2937;
}


/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #7c3aed,
        #a855f7
    );

    color:white;
    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:30px;
}

.header p{
    margin:6px 0 0 0;
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:94%;
    max-width:850px;
    margin:30px auto;
}

.panel{
    background:white;
    padding:28px;
    border-radius:18px;
    box-shadow:
        0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin:0 0 8px 0;
    color:#6d28d9;
    font-size:26px;
}

.descripcion{
    margin:0 0 24px 0;
    color:#64748b;
    font-size:14px;
    line-height:1.5;
}


/* =========================================
   MENSAJES
========================================= */

.mensaje{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:20px;
    font-size:14px;
    font-weight:bold;
}

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
}

.mensaje-ok{
    background:#dcfce7;
    color:#166534;
    border:1px solid #86efac;
}

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;
    box-shadow:
        0 0 0 3px rgba(220,38,38,.12) !important;
}


/* =========================================
   RESUMEN DE USO
========================================= */

.resumen{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:12px;
    margin-bottom:22px;
}

.resumen-card{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:15px;
}

.resumen-card .titulo{
    display:block;
    color:#64748b;
    font-size:12px;
    font-weight:bold;
    margin-bottom:6px;
}

.resumen-card .numero{
    color:#6d28d9;
    font-size:22px;
    font-weight:bold;
}


/* =========================================
   AVISO
========================================= */

.aviso{
    margin-bottom:22px;
    padding:13px 15px;
    border:1px solid #ddd6fe;
    border-radius:10px;
    background:#f5f3ff;
    color:#5b21b6;
    font-size:13px;
    line-height:1.5;
}


/* =========================================
   FORMULARIO
========================================= */

.grupo{
    margin-bottom:18px;
}

label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
    font-weight:bold;
}

input[type="text"],
input[type="number"]{
    width:100%;
    min-height:44px;
    padding:11px 12px;
    border:1px solid #cbd5e1;
    border-radius:10px;
    background:white;
    font-size:14px;
    outline:none;
    transition:.2s;
}

input[type="text"]:focus,
input[type="number"]:focus{
    border-color:#a855f7;
    box-shadow:
        0 0 0 3px rgba(168,85,247,.14);
}

.ayuda{
    margin-top:7px;
    color:#64748b;
    font-size:12px;
    line-height:1.5;
}


/* =========================================
   ESTADO
========================================= */

.estado-box{
    display:flex;
    align-items:center;
    gap:10px;
    padding:13px 14px;
    border:1px solid #e5e7eb;
    border-radius:10px;
    background:#f8fafc;
}

.estado-box input{
    width:17px;
    height:17px;
    accent-color:#7c3aed;
}

.estado-box label{
    margin:0;
    cursor:pointer;
}


/* =========================================
   BOTONES
========================================= */

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:24px;
}

.btn{
    display:inline-block;
    padding:11px 16px;
    border:none;
    border-radius:10px;
    color:white;
    text-decoration:none;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    text-align:center;
    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#7c3aed;
}

.btn-cancelar{
    background:#1f2937;
}


/* =========================================
   CELULAR
========================================= */

@media (max-width:768px){

    .header{
        padding:20px;
        text-align:center;
    }

    .header h1{
        font-size:24px;
    }

    .contenedor{
        width:94%;
        margin:20px auto;
    }

    .panel{
        padding:20px;
        border-radius:16px;
    }

    h2{
        font-size:24px;
        text-align:center;
    }

    .descripcion{
        text-align:center;
    }

    .resumen{
        grid-template-columns:1fr;
    }

    input[type="text"],
    input[type="number"]{
        font-size:16px;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
    }
}

</style>

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>
        SIGENMUNI
    </h1>

    <p>
        Edición de Categoría Municipal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Editar Categoría
        </h2>

        <p class="descripcion">
            Modifique los datos generales de la categoría.
        </p>


        <!-- =====================================
             MENSAJE JAVASCRIPT
        ====================================== -->

        <div
            id="alertaCategoria"
            class="mensaje mensaje-error"
            style="display:none;"
        >
        </div>


        <!-- =====================================
             MENSAJE DEL CONTROLADOR
        ====================================== -->

        <?php if (!empty($mensaje)): ?>

            <div
                class="mensaje <?php
                    echo $tipo_mensaje === 'ok'
                        ? 'mensaje-ok'
                        : 'mensaje-error';
                ?>"
            >

                <?php
                echo htmlspecialchars(
                    $mensaje,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <?php if (!empty($categoria)): ?>


            <!-- =====================================
                 RESUMEN DE ASOCIACIONES
            ====================================== -->

            <div class="resumen">

                <div class="resumen-card">

                    <span class="titulo">
                        Empleados asociados
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $empleadosAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-card">

                    <span class="titulo">
                        Empleados activos
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $empleadosActivosAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>


                <div class="resumen-card">

                    <span class="titulo">
                        Valores asociados
                    </span>

                    <span class="numero">
                        <?php
                        echo (int)(
                            $valoresAsociados
                            ?? 0
                        );
                        ?>
                    </span>

                </div>

            </div>


            <!-- =====================================
                 AVISO
            ====================================== -->

            <div class="aviso">

                Los importes de Sueldo Básico, Dedicación Funcional
                y Suplemento Especial se administran desde los valores
                de los conceptos 101, 102 y 104. Esta pantalla modifica
                solamente el código, nombre y estado de la categoría.

            </div>


            <!-- =====================================
                 FORMULARIO
            ====================================== -->

            <form
                action="<?php
                    echo htmlspecialchars(
                        $categoriaEditarAccion,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                method="POST"
                id="formCategoria"
                novalidate
            >

                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php
                        echo htmlspecialchars(
                            $categoriaEditarCsrf,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >


                <!-- =================================
                     CÓDIGO
                ================================== -->

                <div class="grupo">

                    <label for="codigo">
                        Código *
                    </label>

                    <input
                        type="number"
                        name="codigo"
                        id="codigo"
                        min="1"
                        step="1"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                (string)(
                                    $categoria['codigo']
                                    ?? ''
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        El código debe ser numérico y único.
                        Las categorías tradicionales pueden conservar
                        los códigos 1 a 24 y los cargos especiales
                        pueden utilizar 1001 en adelante.
                    </div>

                </div>


                <!-- =================================
                     NOMBRE
                ================================== -->

                <div class="grupo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="150"
                        autocomplete="off"
                        value="<?php
                            echo htmlspecialchars(
                                $categoria['nombre']
                                ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                    >

                    <div class="ayuda">
                        El nombre de la categoría tampoco puede repetirse.
                    </div>

                </div>


                <!-- =================================
                     ESTADO
                ================================== -->

                <div class="grupo">

                    <div class="estado-box">

                        <input
                            type="checkbox"
                            name="activo"
                            id="activo"
                            value="1"
                            <?php
                            echo (
                                (int)(
                                    $categoria['activo']
                                    ?? 0
                                )
                                ===
                                1
                            )
                                ?
                                'checked'
                                :
                                '';
                            ?>
                        >

                        <label for="activo">
                            Categoría activa
                        </label>

                    </div>

                    <div class="ayuda">

                        Si la categoría se inactiva, dejará de estar disponible
                        para nuevas selecciones. Los empleados y registros
                        históricos asociados no se eliminan.

                    </div>

                </div>


                <!-- =================================
                     BOTONES
                ================================== -->

                <div class="acciones">

                    <button
                        type="submit"
                        class="btn btn-guardar"
                    >
                        Actualizar Categoría
                    </button>


                    <a
                        href="<?php
                        echo htmlspecialchars(
                            $categoriaEditarCancelar,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                        class="btn btn-cancelar"
                    >
                        Cancelar
                    </a>

                </div>

            </form>


        <?php else: ?>

            <div class="mensaje mensaje-error">
                No se pudo cargar la categoría solicitada.
            </div>


            <div class="acciones">

                <a
                    href="<?php
                    echo htmlspecialchars(
                        $categoriaEditarCancelar,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-cancelar"
                >
                    Volver a Categorías
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>


<script>

<?php if (!empty($categoria)): ?>

/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        'formCategoria'
    )
    .addEventListener(
        'submit',
        function(e)
        {
            const alerta =
                document
                    .getElementById(
                        'alertaCategoria'
                    );


            const codigo =
                document
                    .getElementById(
                        'codigo'
                    );


            const nombre =
                document
                    .getElementById(
                        'nombre'
                    );


            alerta.style.display =
                'none';


            alerta.innerHTML =
                '';


            codigo.classList.remove(
                'input-error'
            );


            nombre.classList.remove(
                'input-error'
            );


            function mostrarError(
                mensaje,
                campo
            ) {
                e.preventDefault();


                alerta.innerHTML =
                    mensaje;


                alerta.style.display =
                    'block';


                campo.classList.add(
                    'input-error'
                );


                campo.focus();


                window.scrollTo({
                    top:0,
                    behavior:'smooth'
                });
            }


            /*
            |--------------------------------------------------------------------------
            | CÓDIGO
            |--------------------------------------------------------------------------
            */

            const codigoValor =
                codigo.value.trim();


            if (
                codigoValor
                ===
                ''
            ) {

                mostrarError(
                    'Debe ingresar el código de la categoría.',
                    codigo
                );

                return;
            }


            if (
                !/^[0-9]+$/.test(
                    codigoValor
                )
            ) {

                mostrarError(
                    'El código debe contener solamente números.',
                    codigo
                );

                return;
            }


            if (
                parseInt(
                    codigoValor,
                    10
                )
                <=
                0
            ) {

                mostrarError(
                    'El código debe ser mayor a cero.',
                    codigo
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | NOMBRE
            |--------------------------------------------------------------------------
            */

            const nombreValor =
                nombre.value.trim();


            if (
                nombreValor
                ===
                ''
            ) {

                mostrarError(
                    'Debe ingresar el nombre de la categoría.',
                    nombre
                );

                return;
            }


            if (
                nombreValor.length
                >
                150
            ) {

                mostrarError(
                    'El nombre no puede superar los 150 caracteres.',
                    nombre
                );

                return;
            }
        }
    );

<?php endif; ?>

</script>


</body>

</html>