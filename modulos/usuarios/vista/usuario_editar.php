<?php

/*
|--------------------------------------------------------------------------
| EDITAR USUARIO - NAVEGACIÓN ROUTER
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../core/Url.php';


$usuarioEditarId =
    (int)(
        $usuario['id']
        ??
        $_GET['id']
        ??
        0
    );


$usuarioEditarAccion =
    sigenmuniUrlRuta(
        'usuarios/editar',
        [
            'id' =>
                $usuarioEditarId
        ]
    );


$usuarioEditarUrlVolver =
    sigenmuniUrlRuta(
        'usuarios'
    );

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Usuario - SIGENMUNI</title>

<style>

/* =========================================
   GENERAL
========================================= */

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
    font-size:14px;
}


/* =========================================
   CONTENEDOR
========================================= */

.contenedor{
    width:92%;
    max-width:900px;
    margin:30px auto;
}


/* =========================================
   PANEL
========================================= */

.panel{
    background:white;
    padding:28px;
    border-radius:18px;

    box-shadow:
        0 8px 20px rgba(0,0,0,.10);
}

h2{
    margin-top:0;
    margin-bottom:15px;
    color:#111827;
}


/* =========================================
   USUARIO ACTUAL
========================================= */

.info-usuario{
    background:#ecfdf5;
    color:#166534;

    border:
        1px solid #86efac;

    padding:12px 14px;

    border-radius:10px;

    margin-bottom:20px;

    font-weight:bold;
}


/* =========================================
   MENSAJE ERROR
========================================= */

.mensaje-error{
    background:#fee2e2;
    color:#991b1b;

    border:
        1px solid #fecaca;

    padding:12px 14px;

    border-radius:10px;

    margin-bottom:16px;

    font-size:14px;
    font-weight:bold;
}


/* =========================================
   FORMULARIO
========================================= */

.form-grid{
    display:grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(260px,1fr)
        );

    gap:16px;
}

.campo{
    display:flex;
    flex-direction:column;
}

label{
    font-weight:bold;
    margin-bottom:6px;
    color:#374151;
    font-size:14px;
}

input,
select{
    width:100%;

    padding:12px;

    border:
        1px solid #cbd5e1;

    border-radius:10px;

    font-size:14px;

    transition:.2s;

    background:white;
}

input:focus,
select:focus{
    outline:none;

    border-color:#14b8a6;

    box-shadow:
        0 0 0 3px
        rgba(20,184,166,.15);
}


/* =========================================
   INPUT ERROR
========================================= */

.input-error{
    border-color:#dc2626 !important;
    background:#fff1f2 !important;

    box-shadow:
        0 0 0 3px
        rgba(220,38,38,.12)
        !important;
}


/* =========================================
   AYUDA
========================================= */

.ayuda{
    font-size:13px;
    color:#6b7280;
    margin-top:6px;
    line-height:1.4;
}


/* =========================================
   CONTRASEÑAS
========================================= */

.password-wrapper{
    position:relative;
}

.password-wrapper input{
    padding-right:48px;
}

.toggle-pass{
    position:absolute;
    right:14px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    user-select:none;
    font-size:17px;
    line-height:1;
}

.requisitos-clave{
    margin-top:9px;
    padding:11px 12px;
    background:#f0fdfa;
    border:1px solid #99f6e4;
    border-radius:10px;
}

.requisitos-titulo{
    display:block;
    color:#0f766e;
    font-size:12px;
    font-weight:bold;
    margin-bottom:7px;
}

.requisito{
    display:flex;
    align-items:center;
    gap:7px;
    margin:5px 0;
    color:#64748b;
    font-size:12px;
    line-height:1.35;
}

.requisito .icono{
    width:18px;
    height:18px;
    flex:0 0 18px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:50%;
    background:#e5e7eb;
    color:#64748b;
    font-size:11px;
    font-weight:bold;
}

.requisito.valido{
    color:#166534;
}

.requisito.valido .icono{
    background:#dcfce7;
    color:#166534;
}


/* =========================================
   ACCIONES
========================================= */

.acciones{
    margin-top:22px;

    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

button,
.btn{
    padding:12px 18px;

    border-radius:10px;

    border:none;

    text-decoration:none;

    font-weight:bold;

    cursor:pointer;

    display:inline-block;

    font-size:14px;

    transition:.2s;
}

.btn-actualizar{
    background:#0f766e;
    color:white;
}

.btn-actualizar:hover{
    background:#115e59;
}

.btn-volver{
    background:#1f2937;
    color:white;
}

.btn-volver:hover{
    background:#111827;
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
        font-size:22px;
    }

    .form-grid{
        grid-template-columns:1fr;
    }

    .acciones{
        flex-direction:column;
    }

    button,
    .btn{
        width:100%;
        text-align:center;
    }

    input,
    select{
        min-height:44px;
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
        Editar usuario del sistema
    </p>

</div>


<div class="contenedor">


    <div class="panel">


        <h2>
            Editar Usuario
        </h2>


        <!-- =================================
             USUARIO ACTUAL
        ================================== -->

        <div class="info-usuario">

            Usuario actual:

            <?php
            echo htmlspecialchars(
                $usuario['nombre_usuario']
                ?? '',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>

        </div>


        <!-- =================================
             ERROR DEL CONTROLADOR
        ================================== -->

        <?php if (!empty($error)): ?>

            <div class="mensaje-error">

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
            id="alertaUsuario"
            class="mensaje-error"
            style="display:none;"
        ></div>


        <!-- =================================
             FORMULARIO
        ================================== -->

        <form
            method="POST"
            action="<?php
                echo htmlspecialchars(
                    $usuarioEditarAccion,
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>"
            id="formUsuario"
            novalidate
        >


            <div class="form-grid">


                <!-- NOMBRE -->

                <div class="campo">

                    <label for="nombre">
                        Nombre *
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['nombre']
                                ??
                                $usuario['nombre']
                                ??
                                '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="given-name"
                    >

                </div>


                <!-- APELLIDO -->

                <div class="campo">

                    <label for="apellido">
                        Apellido *
                    </label>

                    <input
                        type="text"
                        name="apellido"
                        id="apellido"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['apellido']
                                ??
                                $usuario['apellido']
                                ??
                                '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="family-name"
                    >

                </div>


                <!-- DNI -->

                <div class="campo">

                    <label for="dni">
                        DNI *
                    </label>

                    <input
                        type="text"
                        name="dni"
                        id="dni"
                        minlength="7"
                        maxlength="8"
                        inputmode="numeric"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['dni']
                                ??
                                $usuario['dni']
                                ??
                                '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        oninput="
                            this.value =
                            this.value.replace(
                                /[^0-9]/g,
                                ''
                            );
                        "
                    >

                    <div class="ayuda">
                        Ingrese solo números, sin puntos ni espacios.
                    </div>

                </div>


                <!-- EMAIL -->

                <div class="campo">

                    <label for="email">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        maxlength="150"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['email']
                                ??
                                $usuario['email']
                                ??
                                '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="email"
                    >

                </div>


                <!-- NOMBRE DE USUARIO -->

                <div class="campo">

                    <label for="nombre_usuario">
                        Nombre de usuario *
                    </label>

                    <input
                        type="text"
                        name="nombre_usuario"
                        id="nombre_usuario"
                        maxlength="100"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST['nombre_usuario']
                                ??
                                $usuario['nombre_usuario']
                                ??
                                '',
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        ?>"
                        autocomplete="username"
                    >

                </div>


                <!-- ROL -->

                <div class="campo">

                    <label for="rol_id">
                        Rol *
                    </label>


                    <select
                        name="rol_id"
                        id="rol_id"
                    >

                        <option value="">
                            Seleccione un rol
                        </option>


                        <?php

                        $rolSeleccionado =
                            isset($_POST['rol_id'])
                                ?
                                (int)$_POST['rol_id']
                                :
                                (int)(
                                    $usuario['rol_id']
                                    ?? 0
                                );

                        ?>


                        <?php foreach ($roles as $r): ?>


                            <option
                                value="<?php
                                    echo (int)$r['id'];
                                ?>"
                                <?php
                                echo (
                                    $rolSeleccionado
                                    ===
                                    (int)$r['id']
                                )
                                    ?
                                    'selected'
                                    :
                                    '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $r['nombre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>

                            </option>


                        <?php endforeach; ?>


                    </select>


                    <div class="ayuda">
                        Los roles y permisos se administran desde Gestión de Roles.
                    </div>

                </div>


                <!-- NUEVA CONTRASEÑA -->

                <div class="campo">

                    <label for="clave">
                        Nueva contraseña
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="clave"
                            id="clave"
                            minlength="8"
                            autocomplete="new-password"
                        >

                        <span
                            class="toggle-pass"
                            onclick="togglePassword('clave')"
                            title="Mostrar u ocultar contraseña"
                        >
                            👁
                        </span>

                    </div>

                    <div class="ayuda">
                        Deje este campo vacío si no desea cambiar la contraseña.
                    </div>

                    <div class="requisitos-clave">

                        <span class="requisitos-titulo">
                            🔒 Si asigna una nueva contraseña debe contener:
                        </span>

                        <div class="requisito" id="reqLongitud">
                            <span class="icono">•</span>
                            Mínimo 8 caracteres
                        </div>

                        <div class="requisito" id="reqMayuscula">
                            <span class="icono">•</span>
                            Al menos una letra mayúscula
                        </div>

                        <div class="requisito" id="reqEspecial">
                            <span class="icono">•</span>
                            Al menos un carácter especial (@, #, $, %, !, etc.)
                        </div>

                    </div>

                </div>


                <!-- CONFIRMAR NUEVA CONTRASEÑA -->

                <div class="campo">

                    <label for="confirmar_clave">
                        Confirmar nueva contraseña
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="confirmar_clave"
                            id="confirmar_clave"
                            minlength="8"
                            autocomplete="new-password"
                        >

                        <span
                            class="toggle-pass"
                            onclick="togglePassword('confirmar_clave')"
                            title="Mostrar u ocultar contraseña"
                        >
                            👁
                        </span>

                    </div>

                    <div class="ayuda">
                        Solo es obligatorio si el administrador asigna una nueva contraseña.
                    </div>

                </div>


            </div>


            <!-- =================================
                 ACCIONES
            ================================== -->

            <div class="acciones">


                <button
                    type="submit"
                    class="btn-actualizar"
                >
                    Actualizar usuario
                </button>


                <a
                    href="<?php
                    echo htmlspecialchars(
                        $usuarioEditarUrlVolver,
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

/*
|--------------------------------------------------------------------------
| MOSTRAR / OCULTAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

function togglePassword(id)
{
    const input = document.getElementById(id);

    input.type =
        input.type === "password"
            ? "text"
            : "password";
}


/*
|--------------------------------------------------------------------------
| INDICADORES DE LA POLÍTICA DE CONTRASEÑA
|--------------------------------------------------------------------------
*/

const campoClave = document.getElementById("clave");
const reqLongitud = document.getElementById("reqLongitud");
const reqMayuscula = document.getElementById("reqMayuscula");
const reqEspecial = document.getElementById("reqEspecial");


function actualizarRequisito(elemento, valido)
{
    const icono = elemento.querySelector(".icono");

    if (valido) {
        elemento.classList.add("valido");
        icono.textContent = "✓";
    } else {
        elemento.classList.remove("valido");
        icono.textContent = "•";
    }
}


function actualizarRequisitosClave()
{
    const clave = campoClave.value;

    actualizarRequisito(
        reqLongitud,
        [...clave].length >= 8
    );

    actualizarRequisito(
        reqMayuscula,
        /\p{Lu}/u.test(clave)
    );

    actualizarRequisito(
        reqEspecial,
        /[^\p{L}\p{N}\s]/u.test(clave)
    );
}


campoClave.addEventListener(
    "input",
    actualizarRequisitosClave
);


/*
|--------------------------------------------------------------------------
| VALIDACIÓN DEL FORMULARIO
|--------------------------------------------------------------------------
*/

document
    .getElementById(
        "formUsuario"
    )
    .addEventListener(
        "submit",
        function(e)
        {

            const alerta =
                document.getElementById(
                    "alertaUsuario"
                );


            const campos = [

                "nombre",
                "apellido",
                "dni",
                "email",
                "nombre_usuario",
                "rol_id",
                "clave",
                "confirmar_clave"

            ];


            campos.forEach(
                function(id)
                {

                    document
                        .getElementById(id)
                        .classList
                        .remove(
                            "input-error"
                        );

                }
            );


            alerta.style.display =
                "none";

            alerta.textContent =
                "";


            /*
            |--------------------------------------------------------------------------
            | MOSTRAR ERROR
            |--------------------------------------------------------------------------
            */

            function mostrarError(
                mensaje,
                id
            ) {

                e.preventDefault();


                const campo =
                    document.getElementById(
                        id
                    );


                alerta.textContent =
                    mensaje;


                alerta.style.display =
                    "block";


                campo
                    .classList
                    .add(
                        "input-error"
                    );


                campo.focus();


                window.scrollTo({

                    top:0,

                    behavior:"smooth"

                });

            }


            /*
            |--------------------------------------------------------------------------
            | VALORES
            |--------------------------------------------------------------------------
            */

            const nombre =
                document
                    .getElementById(
                        "nombre"
                    )
                    .value
                    .trim();


            const apellido =
                document
                    .getElementById(
                        "apellido"
                    )
                    .value
                    .trim();


            const dni =
                document
                    .getElementById(
                        "dni"
                    )
                    .value
                    .trim();


            const email =
                document
                    .getElementById(
                        "email"
                    )
                    .value
                    .trim();


            const usuario =
                document
                    .getElementById(
                        "nombre_usuario"
                    )
                    .value
                    .trim();


            const rolId =
                document
                    .getElementById(
                        "rol_id"
                    )
                    .value;


            const clave =
                document
                    .getElementById(
                        "clave"
                    )
                    .value;


            const confirmarClave =
                document
                    .getElementById(
                        "confirmar_clave"
                    )
                    .value;


            /*
            |--------------------------------------------------------------------------
            | NOMBRE
            |--------------------------------------------------------------------------
            */

            if (nombre === "") {

                mostrarError(
                    "Debe ingresar el nombre.",
                    "nombre"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | APELLIDO
            |--------------------------------------------------------------------------
            */

            if (apellido === "") {

                mostrarError(
                    "Debe ingresar el apellido.",
                    "apellido"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | DNI
            |--------------------------------------------------------------------------
            */

            if (dni === "") {

                mostrarError(
                    "Debe ingresar el DNI.",
                    "dni"
                );

                return;
            }


            if (
                !/^[0-9]{7,8}$/
                    .test(dni)
            ) {

                mostrarError(
                    "El DNI debe tener entre 7 y 8 números.",
                    "dni"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */

            if (email === "") {

                mostrarError(
                    "Debe ingresar el email.",
                    "email"
                );

                return;
            }


            const formatoEmail =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


            if (
                !formatoEmail.test(
                    email
                )
            ) {

                mostrarError(
                    "Debe ingresar un email válido.",
                    "email"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | USUARIO
            |--------------------------------------------------------------------------
            */

            if (usuario === "") {

                mostrarError(
                    "Debe ingresar el nombre de usuario.",
                    "nombre_usuario"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ROL
            |--------------------------------------------------------------------------
            */

            if (rolId === "") {

                mostrarError(
                    "Debe seleccionar un rol.",
                    "rol_id"
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | NUEVA CONTRASEÑA
            |--------------------------------------------------------------------------
            |
            | La contraseña es opcional al editar. Si el administrador escribe una
            | nueva, debe cumplir toda la política de seguridad.
            |
            |--------------------------------------------------------------------------
            */

            if (clave !== "") {

                if ([...clave].length < 8) {

                    mostrarError(
                        "La nueva contraseña debe tener al menos 8 caracteres.",
                        "clave"
                    );

                    return;
                }

                if (!/\p{Lu}/u.test(clave)) {

                    mostrarError(
                        "La nueva contraseña debe contener al menos una letra mayúscula.",
                        "clave"
                    );

                    return;
                }

                if (!/[^\p{L}\p{N}\s]/u.test(clave)) {

                    mostrarError(
                        "La nueva contraseña debe contener al menos un carácter especial.",
                        "clave"
                    );

                    return;
                }

                if (confirmarClave === "") {

                    mostrarError(
                        "Debe confirmar la nueva contraseña.",
                        "confirmar_clave"
                    );

                    return;
                }

                if (clave !== confirmarClave) {

                    mostrarError(
                        "Las contraseñas ingresadas no coinciden.",
                        "confirmar_clave"
                    );

                    return;
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | SI NO CAMBIA CONTRASEÑA TAMPOCO DEBE ESCRIBIR CONFIRMACIÓN
                |--------------------------------------------------------------------------
                */

                if (confirmarClave !== "") {

                    mostrarError(
                        "Ingrese primero la nueva contraseña.",
                        "clave"
                    );

                    return;
                }

            }

        }
    );


/*
|--------------------------------------------------------------------------
| INICIALIZAR INDICADORES
|--------------------------------------------------------------------------
*/

actualizarRequisitosClave();

</script>


</body>

</html>