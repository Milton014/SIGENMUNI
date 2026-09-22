<?php

/*
|--------------------------------------------------------------------------
| URL Y CSRF - SOLO ROUTER
|--------------------------------------------------------------------------
|
| Esta vista ya no mantiene compatibilidad con las entradas antiguas.
| Todas las acciones del formulario utilizan el Front Controller.
|
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';

$categoriaNuevaAccion =
    sigenmuniUrlRuta(
        'categorias/nueva'
    );

$categoriaNuevaCancelar =
    sigenmuniUrlRuta(
        'categorias'
    );

$categoriaNuevaCsrf =
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
    Nueva Categoría - SIGENMUNI
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
    max-width:760px;
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


/* =========================================
   AYUDA
========================================= */

.ayuda{
    margin-top:7px;
    color:#64748b;
    font-size:12px;
    line-height:1.5;
}

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
        Alta de Categoría Municipal
    </p>

</div>


<!-- =========================================
     CONTENIDO
========================================= -->

<div class="contenedor">

    <div class="panel">

        <h2>
            Nueva Categoría
        </h2>

        <p class="descripcion">
            Registre una nueva categoría o cargo municipal.
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


        <!-- =====================================
             AVISO
        ====================================== -->

        <div class="aviso">

            Los importes de Sueldo Básico, Dedicación Funcional
            y Suplemento Especial no se cargan en esta pantalla.
            Luego podrá asignarlos desde los valores de los conceptos
            101, 102 y 104.

        </div>


        <!-- =====================================
             FORMULARIO
        ====================================== -->

        <form
            action="<?php
                echo htmlspecialchars(
                    $categoriaNuevaAccion,
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
                        $categoriaNuevaCsrf,
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
                    placeholder="Ejemplo: 1001"
                    value="<?php
                        echo htmlspecialchars(
                            (string)(
                                $datos['codigo']
                                ?? ''
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    El código debe ser numérico y no puede repetirse.
                    Puede utilizar 1 a 24 para categorías tradicionales
                    y 1001 en adelante para cargos especiales.
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
                    placeholder="Ejemplo: Intendente"
                    value="<?php
                        echo htmlspecialchars(
                            $datos['nombre']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                >

                <div class="ayuda">
                    El nombre también debe ser único.
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
                                $datos['activo']
                                ?? 1
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

            </div>


            <!-- =================================
                 BOTONES
            ================================== -->

            <div class="acciones">

                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Categoría
                </button>


                <a
                    href="<?php
                        echo htmlspecialchars(
                            $categoriaNuevaCancelar,
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

    </div>

</div>


<script>

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


            /*
            |--------------------------------------------------------------------------
            | LIMPIAR ESTADO
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | FUNCIÓN DE ERROR
            |--------------------------------------------------------------------------
            */

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

</script>


</body>

</html>