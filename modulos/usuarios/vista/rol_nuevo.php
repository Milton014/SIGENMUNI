<?php

/*
|--------------------------------------------------------------------------
| NUEVO ROL - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$rolNuevoAccion =
    sigenmuniUrlRuta(
        'usuarios/roles/nuevo'
    );


$rolNuevoUrlVolver =
    sigenmuniUrlRuta(
        'usuarios/roles'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Rol - SIGENMUNI</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:Arial,sans-serif;
    background:#f4f7fb;
    margin:0;
    color:#1f2937;
}


/* =========================================
   HEADER
========================================= */

.header{
    background:linear-gradient(
        135deg,
        #0f766e,
        #14b8a6
    );

    color:white;
    padding:22px 30px;

    box-shadow:
        0 4px 14px rgba(0,0,0,.10);
}

.header h1{
    margin:0;
    font-size:28px;
}

.header p{
    margin:6px 0 0;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:95%;
    max-width:900px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    border-radius:18px;
    padding:25px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);
}

h2{
    margin-top:0;
    color:#0f172a;
}


/* =========================================
   INFORMACIÓN
========================================= */

.info{
    background:#eff6ff;
    color:#1e40af;

    border:
        1px solid #bfdbfe;

    padding:12px;

    border-radius:10px;

    margin-bottom:20px;

    line-height:1.5;
}


/* =========================================
   ERROR
========================================= */

.error{
    background:#fee2e2;
    color:#991b1b;

    border:
        1px solid #fecaca;

    padding:12px 15px;

    border-radius:10px;

    margin-bottom:15px;

    font-weight:bold;
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
    font-weight:bold;
}

input[type=text],
textarea{
    width:100%;

    padding:12px;

    border:
        1px solid #d1d5db;

    border-radius:10px;

    font-size:14px;

    outline:none;

    transition:.2s;
}

input[type=text]:focus,
textarea:focus{
    border-color:#0f766e;

    box-shadow:
        0 0 0 3px
        rgba(15,118,110,.15);
}

.campo-error{
    border-color:#dc2626 !important;

    box-shadow:
        0 0 0 3px
        rgba(220,38,38,.15)
        !important;

    background:#fff1f2;
}

textarea{
    resize:vertical;
    min-height:100px;
}


/* =========================================
   CHECKBOX
========================================= */

.check{
    margin-top:12px;
}

.check label{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:normal;
    cursor:pointer;
}

.check input{
    width:18px;
    height:18px;
}


/* =========================================
   AVISO ADMIN
========================================= */

.aviso-admin{
    margin-top:12px;

    padding:12px 14px;

    background:#fef3c7;
    color:#92400e;

    border:
        1px solid #fde68a;

    border-radius:10px;

    font-size:13px;
    line-height:1.5;
}


/* =========================================
   BOTONES
========================================= */

.btn{
    border:none;

    padding:12px 16px;

    border-radius:10px;

    font-weight:bold;

    cursor:pointer;

    text-decoration:none;

    display:inline-block;

    font-size:14px;

    transition:.2s;
}

.btn:hover{
    opacity:.92;
    transform:translateY(-1px);
}

.btn-guardar{
    background:#0f766e;
    color:white;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.acciones{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}


/* =========================================
   CELULAR
========================================= */

@media(max-width:768px){

    .header{
        padding:18px;
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
        padding:18px;
        border-radius:14px;
    }

    h2{
        text-align:center;
    }

    .acciones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
    }

    input[type=text],
    textarea{
        font-size:16px;
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
        Alta de Roles
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <h2>
            Nuevo Rol
        </h2>


        <!-- =================================
             INFORMACIÓN
        ================================== -->

        <div class="info">

            Desde aquí puede crear nuevos perfiles como:

            <strong>
                RRHH, TESORERÍA, CONTADURÍA, INVITADO,
                AUDITORÍA, SECRETARÍA
            </strong>

            u otros roles que necesite el sistema.

        </div>


        <!-- =================================
             ERROR DEL CONTROLADOR
        ================================== -->

        <?php if (!empty($error)): ?>

            <div class="error">

                <?php
                echo htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                );
                ?>

            </div>

        <?php endif; ?>


        <!-- =================================
             ERROR JAVASCRIPT
        ================================== -->

        <div
            class="error"
            id="alertaFormulario"
            style="display:none;"
        ></div>


        <!-- =================================
             FORMULARIO
        ================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $rolNuevoAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formRol"
            novalidate
        >



            <!-- NOMBRE -->

            <div class="grupo">

                <label for="nombre">
                    Nombre del Rol *
                </label>


                <input
                    type="text"
                    name="nombre"
                    id="nombre"
                    maxlength="100"
                    value="<?php
                        echo htmlspecialchars(
                            $datosRol['nombre']
                            ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>"
                    autocomplete="off"
                >

            </div>


            <!-- DESCRIPCIÓN -->

            <div class="grupo">

                <label for="descripcion">
                    Descripción
                </label>


                <textarea
                    name="descripcion"
                    id="descripcion"
                    maxlength="500"
                    placeholder="Descripción del rol..."
                ><?php
                    echo htmlspecialchars(
                        $datosRol['descripcion']
                        ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?></textarea>

            </div>


            <!-- =================================
                 ADMINISTRADOR
            ================================== -->

            <div class="check">

                <label>

                    <input
                        type="checkbox"
                        name="es_admin"
                        id="es_admin"
                        value="1"
                        <?php
                        echo (
                            (int)(
                                $datosRol['es_admin']
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

                    Este rol tendrá acceso total al sistema

                </label>

            </div>


            <div
                class="aviso-admin"
                id="avisoAdmin"
                style="<?php
                    echo (
                        (int)(
                            $datosRol['es_admin']
                            ?? 0
                        )
                        ===
                        1
                    )
                        ?
                        ''
                        :
                        'display:none;';
                ?>"
            >

                Un rol administrador tendrá acceso total a los módulos
                del sistema. Sus permisos se habilitarán automáticamente.

            </div>


            <!-- =================================
                 ESTADO
            ================================== -->

            <div class="check">

                <label>

                    <input
                        type="checkbox"
                        checked
                        disabled
                    >

                    Rol activo

                </label>

            </div>


            <div class="info" style="margin-top:12px; margin-bottom:0;">

                Los roles nuevos se crean inicialmente en estado
                <strong>Activo</strong>.

                Luego podrá activarlos o inactivarlos desde
                Gestión de Roles.

            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <button
                    type="submit"
                    class="btn btn-guardar"
                >
                    Guardar Rol
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $rolNuevoUrlVolver,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>"
                    class="btn btn-volver"
                >
                    Volver
                </a>


            </div>


        </form>


    </div>


</div>


<script>

const formRol =
    document.getElementById(
        "formRol"
    );

const nombre =
    document.getElementById(
        "nombre"
    );

const alertaFormulario =
    document.getElementById(
        "alertaFormulario"
    );

const esAdmin =
    document.getElementById(
        "es_admin"
    );

const avisoAdmin =
    document.getElementById(
        "avisoAdmin"
    );


/*
|--------------------------------------------------------------------------
| MOSTRAR AVISO DE ADMINISTRADOR
|--------------------------------------------------------------------------
*/

esAdmin.addEventListener(
    "change",
    function()
    {

        avisoAdmin.style.display =
            this.checked
                ?
                "block"
                :
                "none";

    }
);


/*
|--------------------------------------------------------------------------
| VALIDACIÓN
|--------------------------------------------------------------------------
*/

formRol.addEventListener(
    "submit",
    function(e)
    {

        nombre
            .classList
            .remove(
                "campo-error"
            );


        alertaFormulario.style.display =
            "none";


        alertaFormulario.textContent =
            "";


        const nombreValor =
            nombre.value.trim();


        if (
            nombreValor
            ===
            ""
        ) {

            e.preventDefault();


            nombre
                .classList
                .add(
                    "campo-error"
                );


            nombre.focus();


            alertaFormulario.textContent =
                "Debe ingresar el nombre del rol.";


            alertaFormulario.style.display =
                "block";


            window.scrollTo({

                top:0,

                behavior:"smooth"

            });


            return false;
        }


        if (
            nombreValor.length
            >
            100
        ) {

            e.preventDefault();


            nombre
                .classList
                .add(
                    "campo-error"
                );


            nombre.focus();


            alertaFormulario.textContent =
                "El nombre del rol no puede superar los 100 caracteres.";


            alertaFormulario.style.display =
                "block";


            return false;
        }

    }
);

</script>


</body>

</html>