<?php

require_once __DIR__ . '/../modelo/UsuarioModelo.php';
require_once __DIR__ . '/../../../core/Url.php';
require_once __DIR__ . '/../../../core/Csrf.php';


class UsuarioControlador
{
    private $modelo;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct($conexion)
    {
        $this->modelo =
            new UsuarioModelo(
                $conexion
            );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO PRINCIPAL DE USUARIOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $mensaje = "";
        $error = "";


        if (isset($_GET['ok'])) {

            switch ($_GET['ok']) {

                case 'nuevo':
                    $mensaje =
                        "Usuario creado correctamente.";
                    break;

                case 'editar':
                    $mensaje =
                        "Usuario actualizado correctamente.";
                    break;

                case 'estado':
                    $mensaje =
                        "Estado del usuario actualizado correctamente.";
                    break;

            }
        }


        if (isset($_GET['error'])) {

            switch ($_GET['error']) {

                case 'usuario_propio':
                    $error =
                        "No puede inactivar su propio usuario mientras está logueado.";
                    break;

                case 'ultimo_admin':
                    $error =
                        "No puede inactivar el único usuario administrador activo.";
                    break;

                case 'admin_protegido':
                    $error =
                        "El usuario ADMIN principal está protegido y no puede ser inactivado.";
                    break;

                case 'usuario_no_existe':
                    $error =
                        "El usuario seleccionado no existe.";
                    break;

                case 'parametros_invalidos':
                    $error =
                        "Los parámetros recibidos no son válidos.";
                    break;

                case 'rol_inactivo':
                    $error =
                        "El rol asignado al usuario está inactivo.";
                    break;

                case 'error_estado':
                    $error =
                        "No se pudo actualizar el estado del usuario.";
                    break;

                case 'csrf':
                    $error =
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.";
                    break;
            }
        }


        try {

            $usuarios =
                $this->modelo
                    ->listar();


            require __DIR__
                . '/../vista/usuarios.php';

        } catch (Exception $e) {

            die(
                "Error al cargar los usuarios: "
                . htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVO USUARIO
    |--------------------------------------------------------------------------
    */

    public function nuevo()
    {
        $error = "";


        $datosUsuario = [

            'nombre' =>
                '',

            'apellido' =>
                '',

            'dni' =>
                '',

            'nombre_usuario' =>
                '',

            'email' =>
                '',

            'rol_id' =>
                '',

            'activo' =>
                1
        ];


        try {

            $roles =
                $this->modelo
                    ->obtenerRolesActivos();


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

                $datosUsuario =
                    $this->recibirDatosUsuario();


                /*
                |--------------------------------------------------------------------------
                | CONTRASEÑA
                |--------------------------------------------------------------------------
                |
                | No se utiliza trim() porque una contraseña no debe modificarse
                | silenciosamente antes de validarla o almacenarla.
                |
                |--------------------------------------------------------------------------
                */

                $clave =
                    $_POST['clave']
                    ?? '';


                $confirmarClave =
                    $_POST['confirmar_clave']
                    ?? '';


                $datosGuardar =
                    $this->validarUsuario(
                        $datosUsuario,
                        null
                    );


                if ($clave === '') {

                    throw new Exception(
                        "Debe ingresar una contraseña."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VALIDAR POLÍTICA DE CONTRASEÑA
                |--------------------------------------------------------------------------
                */

                $this->validarPoliticaClave(
                    $clave
                );


                if (
                    $clave
                    !==
                    $confirmarClave
                ) {

                    throw new Exception(
                        "Las contraseñas ingresadas no coinciden."
                    );
                }


                $claveHash =
                    password_hash(
                        $clave,
                        PASSWORD_DEFAULT
                    );


                if ($claveHash === false) {

                    throw new Exception(
                        "No se pudo generar la contraseña del usuario."
                    );
                }


                $datosGuardar['clave'] =
                    $claveHash;


                $datosGuardar['activo'] =
                    1;




                $idUsuario =
                    $this->modelo
                        ->guardar(
                            $datosGuardar
                        );


                if ($idUsuario <= 0) {

                    throw new Exception(
                        "No se pudo crear el usuario."
                    );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'usuarios',
                        [
                            'ok' =>
                                'nuevo'
                        ]
                    );


                header(
                    'Location: '
                    .
                    $urlDestino
                );

                exit();
            }

        } catch (Exception $e) {

            $error =
                $e->getMessage();


            /*
            |--------------------------------------------------------------------------
            | RECARGAR ROLES SI EL ERROR OCURRIÓ ANTES DE SU ASIGNACIÓN
            |--------------------------------------------------------------------------
            */

            if (!isset($roles)) {

                try {

                    $roles =
                        $this->modelo
                            ->obtenerRolesActivos();

                } catch (Exception $rolesException) {

                    $roles = [];
                }
            }
        }


        require __DIR__
            . '/../vista/usuario_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR USUARIO
    |--------------------------------------------------------------------------
    */

    public function editar()
    {
        $id =
            isset($_GET['id'])
                ?
                (int)$_GET['id']
                :
                0;


        if ($id <= 0) {

            $urlDestino =
                sigenmuniUrlRuta(
                    'usuarios',
                    [
                        'error' =>
                            'parametros_invalidos'
                    ]
                );


            header(
                'Location: '
                .
                $urlDestino
            );

            exit();
        }


        $error = "";


        try {

            $usuario =
                $this->modelo
                    ->obtenerPorId(
                        $id
                    );


            if (!$usuario) {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'usuarios',
                        [
                            'error' =>
                                'usuario_no_existe'
                        ]
                    );


                header(
                    'Location: '
                    .
                    $urlDestino
                );

                exit();
            }


            $roles =
                $this->modelo
                    ->obtenerRolesActivos();


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

                $datosUsuario =
                    $this->recibirDatosUsuario();


                /*
                |--------------------------------------------------------------------------
                | CONTRASEÑA
                |--------------------------------------------------------------------------
                |
                | No se utiliza trim() porque una contraseña no debe modificarse
                | silenciosamente antes de validarla o almacenarla.
                |
                |--------------------------------------------------------------------------
                */

                $clave =
                    $_POST['clave']
                    ?? '';


                $confirmarClave =
                    $_POST['confirmar_clave']
                    ?? '';


                $datosGuardar =
                    $this->validarUsuario(
                        $datosUsuario,
                        $id
                    );


                if ($clave !== '') {

                    /*
                    |--------------------------------------------------------------------------
                    | VALIDAR POLÍTICA DE LA NUEVA CONTRASEÑA
                    |--------------------------------------------------------------------------
                    */

                    $this->validarPoliticaClave(
                        $clave
                    );


                    if (
                        $clave
                        !==
                        $confirmarClave
                    ) {

                        throw new Exception(
                            "Las contraseñas ingresadas no coinciden."
                        );
                    }


                    $claveHash =
                        password_hash(
                            $clave,
                            PASSWORD_DEFAULT
                        );


                    if ($claveHash === false) {

                        throw new Exception(
                            "No se pudo generar la nueva contraseña del usuario."
                        );
                    }


                    $datosGuardar['clave'] =
                        $claveHash;


                    $this->modelo
                        ->actualizarConClave(
                            $id,
                            $datosGuardar
                        );

                } else {

                    if ($confirmarClave !== '') {

                        throw new Exception(
                            "Ingrese primero la nueva contraseña."
                        );
                    }


                    $this->modelo
                        ->actualizarSinClave(
                            $id,
                            $datosGuardar
                        );
                }


                $urlDestino =
                    sigenmuniUrlRuta(
                        'usuarios',
                        [
                            'ok' =>
                                'editar'
                        ]
                    );


                header(
                    'Location: '
                    .
                    $urlDestino
                );

                exit();
            }


        } catch (Exception $e) {

            $error =
                $e->getMessage();


            if (!isset($roles)) {

                try {

                    $roles =
                        $this->modelo
                            ->obtenerRolesActivos();

                } catch (Exception $rolesException) {

                    $roles = [];
                }
            }


            if (!isset($usuario)) {

                $usuario = [];
            }
        }


        require __DIR__
            . '/../vista/usuario_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE USUARIO
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado()
    {
        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN AL LISTADO
        |--------------------------------------------------------------------------
        */

        $redirigir =
            function (array $parametros = []) {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'usuarios',
                        $parametros
                    );


                header(
                    'Location: '
                    .
                    $urlDestino
                );

                exit();
            };


        /*
        |--------------------------------------------------------------------------
        | SOLO POST
        |--------------------------------------------------------------------------
        */

        $metodo =
            strtoupper(
                $_SERVER['REQUEST_METHOD']
                ??
                'GET'
            );


        if ($metodo !== 'POST') {

            http_response_code(405);

            header(
                'Allow: POST'
            );

            exit();
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS RECIBIDOS
        |--------------------------------------------------------------------------
        */

        $id =
            isset($_POST['id'])
                ?
                (int)$_POST['id']
                :
                0;


        $estadoSolicitado =
            isset($_POST['estado'])
                ?
                (int)$_POST['estado']
                :
                -1;


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ??
                ''
            )
        ) {

            $redirigir(
                [
                    'error' =>
                        'csrf'
                ]
            );
        }


        if (
            $estadoSolicitado !== 0
            &&
            $estadoSolicitado !== 1
        ) {

            $redirigir(
                [
                    'error' =>
                        'parametros_invalidos'
                ]
            );
        }


        if ($id <= 0) {

            $redirigir(
                [
                    'error' =>
                        'parametros_invalidos'
                ]
            );
        }


        try {

            $usuario =
                $this->modelo
                    ->obtenerPorId(
                        $id
                    );


            if (!$usuario) {

                $redirigir(
                    [
                        'error' =>
                            'usuario_no_existe'
                    ]
                );
            }


            $estadoActual =
                (int)(
                    $usuario['activo']
                    ??
                    0
                );


            $nuevoEstado =
                $estadoSolicitado;


            /*
            |--------------------------------------------------------------------------
            | SI YA ESTÁ EN EL ESTADO SOLICITADO
            |--------------------------------------------------------------------------
            */

            if ($estadoActual === $nuevoEstado) {

                $redirigir(
                    [
                        'ok' =>
                            'estado'
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EVITAR QUE EL USUARIO LOGUEADO SE INACTIVE
            |--------------------------------------------------------------------------
            */

            $idUsuarioSesion =
                (int)(
                    $_SESSION['id_usuario']
                    ??
                    0
                );


            if (
                $nuevoEstado === 0
                &&
                $idUsuarioSesion > 0
                &&
                $idUsuarioSesion === $id
            ) {

                $redirigir(
                    [
                        'error' =>
                            'usuario_propio'
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | COMPATIBILIDAD DE SESIONES ANTIGUAS SIN id_usuario
            |--------------------------------------------------------------------------
            */

            if (
                $nuevoEstado === 0
                &&
                $idUsuarioSesion <= 0
            ) {

                $usuarioSesion =
                    $_SESSION['usuario']
                    ??
                    null;


                if (
                    $usuarioSesion !== null
                    &&
                    (string)$usuarioSesion
                    ===
                    (string)(
                        $usuario['nombre_usuario']
                        ??
                        ''
                    )
                ) {

                    $redirigir(
                        [
                            'error' =>
                                'usuario_propio'
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | PROTEGER ADMIN PRINCIPAL
            |--------------------------------------------------------------------------
            */

            if (
                $nuevoEstado === 0
                &&
                $this->modelo
                    ->esAdminPrincipal(
                        $id
                    )
            ) {

                $redirigir(
                    [
                        'error' =>
                            'admin_protegido'
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EVITAR DEJAR EL SISTEMA SIN ADMINISTRADOR
            |--------------------------------------------------------------------------
            */

            if (
                $nuevoEstado === 0
                &&
                (int)(
                    $usuario['es_admin']
                    ??
                    0
                )
                ===
                1
            ) {

                $cantidadAdmins =
                    $this->modelo
                        ->contarAdministradoresActivos();


                if ($cantidadAdmins <= 1) {

                    $redirigir(
                        [
                            'error' =>
                                'ultimo_admin'
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | AL ACTIVAR VALIDAR ROL
            |--------------------------------------------------------------------------
            */

            if ($nuevoEstado === 1) {

                $rolId =
                    (int)(
                        $usuario['rol_id']
                        ??
                        0
                    );


                if ($rolId <= 0) {

                    $redirigir(
                        [
                            'error' =>
                                'rol_inactivo'
                        ]
                    );
                }


                $rol =
                    $this->modelo
                        ->obtenerRolPorId(
                            $rolId
                        );


                if (
                    !$rol
                    ||
                    (int)$rol['activo']
                    !==
                    1
                ) {

                    $redirigir(
                        [
                            'error' =>
                                'rol_inactivo'
                        ]
                    );
                }
            }


            $this->modelo
                ->cambiarEstado(
                    $id,
                    $nuevoEstado
                );


            $redirigir(
                [
                    'ok' =>
                        'estado'
                ]
            );


        } catch (Exception $e) {

            $redirigir(
                [
                    'error' =>
                        'error_estado'
                ]
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE ROLES
    |--------------------------------------------------------------------------
    */

    public function roles()
    {
        $mensaje = "";
        $error = "";


        if (isset($_GET['ok'])) {

            switch ($_GET['ok']) {

                case 'nuevo':
                    $mensaje =
                        "Rol creado correctamente.";
                    break;

                case 'editar':
                    $mensaje =
                        "Rol actualizado correctamente.";
                    break;

                case 'estado':
                    $mensaje =
                        "Estado del rol actualizado correctamente.";
                    break;

                case 'permisos':
                    $mensaje =
                        "Permisos del rol actualizados correctamente.";
                    break;
            }
        }


        if (isset($_GET['error'])) {

            switch ($_GET['error']) {

                case 'admin_protegido':
                    $error =
                        "El rol ADMIN principal está protegido y no puede desactivarse.";
                    break;

                case 'ultimo_admin':
                    $error =
                        "No se puede dejar el sistema sin un rol administrador activo.";
                    break;

                case 'rol_no_existe':
                    $error =
                        "El rol seleccionado no existe.";
                    break;

                case 'parametros_invalidos':
                    $error =
                        "Los parámetros recibidos no son válidos.";
                    break;

                case 'usuarios_activos':
                    $error =
                        "No se puede desactivar el rol porque tiene usuarios activos asignados.";
                    break;

                case 'error_estado':
                    $error =
                        "No se pudo actualizar el estado del rol.";
                    break;

                case 'csrf':
                    $error =
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.";
                    break;
            }
        }


        try {

            $roles =
                $this->modelo
                    ->listarRoles();


            /*
            |--------------------------------------------------------------------------
            | USUARIOS ACTIVOS POR ROL
            |--------------------------------------------------------------------------
            */

            foreach (
                $roles
                as
                &$rolFila
            ) {

                $rolFila['usuarios_activos'] =
                    $this->modelo
                        ->contarUsuariosActivosPorRol(
                            (int)$rolFila['id']
                        );
            }

            unset($rolFila);


            require __DIR__
                . '/../vista/roles.php';

        } catch (Exception $e) {

            die(
                "Error al cargar los roles: "
                . htmlspecialchars(
                    $e->getMessage(),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NUEVO ROL
    |--------------------------------------------------------------------------
    */

    public function nuevoRol()
    {
        $error = "";


        $datosRol = [

            'nombre' =>
                '',

            'descripcion' =>
                '',

            'es_admin' =>
                0
        ];


        if (
            $_SERVER['REQUEST_METHOD']
            ===
            'POST'
        ) {

            $datosRol = [

                'nombre' =>
                    strtoupper(
                        trim(
                            $_POST['nombre']
                            ?? ''
                        )
                    ),

                'descripcion' =>
                    trim(
                        $_POST['descripcion']
                        ?? ''
                    ),

                'es_admin' =>
                    isset($_POST['es_admin'])
                        ?
                        1
                        :
                        0
            ];


            try {

                $datosGuardar =
                    $this->validarRol(
                        $datosRol,
                        null
                    );


                $datosGuardar['activo'] =
                    1;


                $idRol =
                    $this->modelo
                        ->guardarRol(
                            $datosGuardar
                        );


                if ($idRol <= 0) {

                    throw new Exception(
                        "No se pudo crear el rol."
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | CREAR PERMISOS BASE
                |--------------------------------------------------------------------------
                */

                $this->modelo
                    ->asegurarPermisosBaseRol(
                        $idRol
                    );


                /*
                |--------------------------------------------------------------------------
                | SI ES ADMIN, TODOS LOS PERMISOS ACTIVOS
                |--------------------------------------------------------------------------
                */

                if (
                    (int)$datosGuardar[
                        'es_admin'
                    ]
                    ===
                    1
                ) {

                    $this->modelo
                        ->activarTodosPermisosRol(
                            $idRol
                        );
                }


                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'usuarios/roles',
                        ['ok' => 'nuevo']
                    )
                );

                exit();

            } catch (Exception $e) {

                $error =
                    $e->getMessage();
            }
        }


        require __DIR__
            . '/../vista/rol_nuevo.php';
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR ROL
    |--------------------------------------------------------------------------
    */

    public function editarRol()
    {
        $id =
            isset($_GET['id'])
                ?
                (int)$_GET['id']
                :
                0;


        if ($id <= 0) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'usuarios/roles',
                    ['error' => 'parametros_invalidos']
                )
            );

            exit();
        }


        $error = "";


        try {

            $rol =
                $this->modelo
                    ->obtenerRolPorId(
                        $id
                    );


            if (!$rol) {

                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'usuarios/roles',
                        ['error' => 'rol_no_existe']
                    )
                );

                exit();
            }


            $esAdminPrincipal =
                $this->modelo
                    ->esRolAdminPrincipal(
                        $id
                    );


            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

                $datosRol = [

                    'nombre' =>
                        strtoupper(
                            trim(
                                $_POST['nombre']
                                ?? ''
                            )
                        ),

                    'descripcion' =>
                        trim(
                            $_POST['descripcion']
                            ?? ''
                        ),

                    'es_admin' =>
                        isset($_POST['es_admin'])
                            ?
                            1
                            :
                            0
                ];


                /*
                |--------------------------------------------------------------------------
                | ADMIN PRINCIPAL NO CAMBIA IDENTIDAD NI PRIVILEGIO
                |--------------------------------------------------------------------------
                */

                if ($esAdminPrincipal) {

                    $datosRol['nombre'] =
                        'ADMIN';

                    $datosRol['es_admin'] =
                        1;
                }


                $datosGuardar =
                    $this->validarRol(
                        $datosRol,
                        $id
                    );


                $this->modelo
                    ->iniciarTransaccion();


                try {

                    $this->modelo
                        ->actualizarRol(
                            $id,
                            $datosGuardar
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SINCRONIZAR usuario.rol LEGACY
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->sincronizarNombreRolUsuarios(
                            $id,
                            $datosGuardar['nombre']
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | ASEGURAR CATÁLOGO DE PERMISOS
                    |--------------------------------------------------------------------------
                    */

                    $this->modelo
                        ->asegurarPermisosBaseRol(
                            $id
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | ROL ADMIN = TODOS LOS PERMISOS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int)$datosGuardar[
                            'es_admin'
                        ]
                        ===
                        1
                    ) {

                        $this->modelo
                            ->activarTodosPermisosRol(
                                $id
                            );
                    }


                    $this->modelo
                        ->confirmarTransaccion();

                } catch (Exception $e) {

                    $this->modelo
                        ->revertirTransaccion();

                    throw $e;
                }


                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'usuarios/roles',
                        ['ok' => 'editar']
                    )
                );

                exit();
            }


        } catch (Exception $e) {

            $error =
                $e->getMessage();


            if (!isset($rol)) {

                $rol = [];
            }


            if (!isset($esAdminPrincipal)) {

                $esAdminPrincipal =
                    false;
            }
        }


        require __DIR__
            . '/../vista/rol_editar.php';
    }


    /*
    |--------------------------------------------------------------------------
    | CAMBIAR ESTADO DE ROL
    |--------------------------------------------------------------------------
    */

    public function cambiarEstadoRol()
    {
        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN AL LISTADO
        |--------------------------------------------------------------------------
        */

        $redirigir =
            function (array $parametros = []) {

                $urlDestino =
                    sigenmuniUrlRuta(
                        'usuarios/roles',
                        $parametros
                    );


                header(
                    'Location: '
                    .
                    $urlDestino
                );

                exit();
            };


        /*
        |--------------------------------------------------------------------------
        | SOLO POST
        |--------------------------------------------------------------------------
        */

        $metodo =
            strtoupper(
                $_SERVER['REQUEST_METHOD']
                ??
                'GET'
            );


        if ($metodo !== 'POST') {

            http_response_code(405);

            header(
                'Allow: POST'
            );

            exit();
        }


        $id =
            isset($_POST['id'])
                ?
                (int)$_POST['id']
                :
                0;


        $estadoSolicitado =
            isset($_POST['estado'])
                ?
                (int)$_POST['estado']
                :
                -1;


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        if (
            !sigenmuniCsrfValido(
                $_POST['_csrf']
                ??
                ''
            )
        ) {

            $redirigir(
                [
                    'error' =>
                        'csrf'
                ]
            );
        }


        if (
            $estadoSolicitado !== 0
            &&
            $estadoSolicitado !== 1
        ) {

            $redirigir(
                [
                    'error' =>
                        'parametros_invalidos'
                ]
            );
        }


        if ($id <= 0) {

            $redirigir(
                [
                    'error' =>
                        'parametros_invalidos'
                ]
            );
        }


        try {

            $rol =
                $this->modelo
                    ->obtenerRolPorId(
                        $id
                    );


            if (!$rol) {

                $redirigir(
                    [
                        'error' =>
                            'rol_no_existe'
                    ]
                );
            }


            $estadoActual =
                (int)(
                    $rol['activo']
                    ??
                    0
                );


            $nuevoEstado =
                $estadoSolicitado;


            if ($estadoActual === $nuevoEstado) {

                $redirigir(
                    [
                        'ok' =>
                            'estado'
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ADMIN PRINCIPAL PROTEGIDO
            |--------------------------------------------------------------------------
            */

            if (
                $nuevoEstado === 0
                &&
                $this->modelo
                    ->esRolAdminPrincipal(
                        $id
                    )
            ) {

                $redirigir(
                    [
                        'error' =>
                            'admin_protegido'
                    ]
                );
            }


            /*
            |--------------------------------------------------------------------------
            | NO DESACTIVAR EL ÚLTIMO ROL ADMIN ACTIVO
            |--------------------------------------------------------------------------
            */

            if (
                $nuevoEstado === 0
                &&
                (int)(
                    $rol['es_admin']
                    ??
                    0
                )
                ===
                1
            ) {

                $cantidad =
                    $this->modelo
                        ->contarRolesAdministradoresActivos();


                if ($cantidad <= 1) {

                    $redirigir(
                        [
                            'error' =>
                                'ultimo_admin'
                        ]
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | NO DESACTIVAR ROL CON USUARIOS ACTIVOS
            |--------------------------------------------------------------------------
            */

            if ($nuevoEstado === 0) {

                $usuariosActivos =
                    $this->modelo
                        ->contarUsuariosActivosPorRol(
                            $id
                        );


                if ($usuariosActivos > 0) {

                    $redirigir(
                        [
                            'error' =>
                                'usuarios_activos'
                        ]
                    );
                }
            }


            $this->modelo
                ->cambiarEstadoRol(
                    $id,
                    $nuevoEstado
                );


            $redirigir(
                [
                    'ok' =>
                        'estado'
                ]
            );


        } catch (Exception $e) {

            $redirigir(
                [
                    'error' =>
                        'error_estado'
                ]
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISOS DEL ROL
    |--------------------------------------------------------------------------
    */

    public function permisosRol()
    {
        $id =
            isset($_GET['id'])
                ?
                (int)$_GET['id']
                :
                0;


        if ($id <= 0) {

            header(
                'Location: '
                .
                sigenmuniUrlRuta(
                    'usuarios/roles',
                    ['error' => 'parametros_invalidos']
                )
            );

            exit();
        }


        $error = "";


        try {

            /*
            |--------------------------------------------------------------------------
            | OBTENER ROL
            |--------------------------------------------------------------------------
            */

            $rol =
                $this->modelo
                    ->obtenerRolPorId(
                        $id
                    );


            if (!$rol) {

                header(
                    'Location: '
                    .
                    sigenmuniUrlRuta(
                        'usuarios/roles',
                        ['error' => 'rol_no_existe']
                    )
                );

                exit();
            }


            /*
            |--------------------------------------------------------------------------
            | IDENTIFICAR ADMIN PRINCIPAL Y ROL ADMINISTRADOR
            |--------------------------------------------------------------------------
            |
            | - ADMIN principal: nombre ADMIN + es_admin = 1
            | - Cualquier rol con es_admin = 1 tiene acceso total.
            |
            |--------------------------------------------------------------------------
            */

            $esAdminPrincipal =
                $this->modelo
                    ->esRolAdminPrincipal(
                        $id
                    );


            $esAdministrador =
                (
                    (int)(
                        $rol['es_admin']
                        ?? 0
                    )
                    ===
                    1
                );


            /*
            |--------------------------------------------------------------------------
            | PERMISOS PROTEGIDOS
            |--------------------------------------------------------------------------
            |
            | Todo rol administrador tiene acceso total y sus permisos no deben
            | quedar configurables individualmente, porque seguridad.php ya lo
            | considera administrador con acceso global.
            |
            |--------------------------------------------------------------------------
            */

            $permisosProtegidos =
                (
                    $esAdminPrincipal
                    ||
                    $esAdministrador
                );


            /*
            |--------------------------------------------------------------------------
            | ASEGURAR QUE EL ROL TENGA EL CATÁLOGO ACTUAL
            |--------------------------------------------------------------------------
            */

            $this->modelo
                ->asegurarPermisosBaseRol(
                    $id
                );


            /*
            |--------------------------------------------------------------------------
            | TODO ROL ADMINISTRADOR TIENE TODOS LOS PERMISOS ACTIVOS
            |--------------------------------------------------------------------------
            */

            if ($permisosProtegidos) {

                $this->modelo
                    ->activarTodosPermisosRol(
                        $id
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR
            |--------------------------------------------------------------------------
            */

            if (
                $_SERVER['REQUEST_METHOD']
                ===
                'POST'
            ) {

                /*
                |--------------------------------------------------------------------------
                | PROTECCIÓN CSRF
                |--------------------------------------------------------------------------
                */

                if (
                    !sigenmuniCsrfValido(
                        $_POST['_csrf']
                        ?? ''
                    )
                ) {

                    $error =
                        "La sesión del formulario no es válida o expiró. Recargue la página e inténtelo nuevamente.";


                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | ADMINISTRADORES: FORZAR ACCESO TOTAL
                    |--------------------------------------------------------------------------
                    */

                    if ($permisosProtegidos) {

                        $this->modelo
                            ->activarTodosPermisosRol(
                                $id
                            );


                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | ROL COMÚN: RECIBIR CHECKBOX
                        |--------------------------------------------------------------------------
                        */

                        $archivosPermitidos =
                            $_POST['permisos']
                            ?? [];


                        if (
                            !is_array(
                                $archivosPermitidos
                            )
                        ) {

                            $archivosPermitidos =
                                [];
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | EL MODELO VALIDA LOS ARCHIVOS CONTRA EL CATÁLOGO FUNCIONAL
                        |--------------------------------------------------------------------------
                        */

                        $this->modelo
                            ->guardarPermisosRol(
                                $id,
                                $archivosPermitidos
                            );
                    }


                    header(
                        'Location: '
                        .
                        sigenmuniUrlRuta(
                            'usuarios/roles',
                            ['ok' => 'permisos']
                        )
                    );

                    exit();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | DATOS PARA LA VISTA
            |--------------------------------------------------------------------------
            */

            $permisos =
                $this->modelo
                    ->obtenerPermisosRol(
                        $id
                    );


            $permisosPorArchivo =
                $this->modelo
                    ->obtenerPermisosRolPorArchivo(
                        $id
                    );


            $catalogoModulos =
                $this->modelo
                    ->obtenerCatalogoModulos();


            require __DIR__
                . '/../vista/rol_permisos.php';


        } catch (Exception $e) {

            $error =
                $e->getMessage();


            if (!isset($rol)) {

                $rol = [];
            }


            if (!isset($permisos)) {

                $permisos = [];
            }


            if (!isset($permisosPorArchivo)) {

                $permisosPorArchivo = [];
            }


            if (!isset($catalogoModulos)) {

                $catalogoModulos = [];
            }


            if (!isset($esAdminPrincipal)) {

                $esAdminPrincipal =
                    false;
            }


            if (!isset($esAdministrador)) {

                $esAdministrador =
                    false;
            }


            if (!isset($permisosProtegidos)) {

                $permisosProtegidos =
                    false;
            }


            require __DIR__
                . '/../vista/rol_permisos.php';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR POLÍTICA DE CONTRASEÑA
    |--------------------------------------------------------------------------
    |
    | Toda contraseña nueva asignada desde Gestión de Usuarios debe tener:
    |
    | - mínimo 8 caracteres;
    | - al menos una letra mayúscula;
    | - al menos un carácter especial.
    |
    | Esta validación se ejecuta en el servidor y no depende del JavaScript
    | de las vistas.
    |
    |--------------------------------------------------------------------------
    */

    private function validarPoliticaClave($clave)
    {
        $longitud =
            function_exists('mb_strlen')
                ?
                mb_strlen(
                    $clave,
                    'UTF-8'
                )
                :
                strlen($clave);


        if ($longitud < 8) {

            throw new Exception(
                "La contraseña debe tener al menos 8 caracteres."
            );
        }


        if (
            !preg_match(
                '/\p{Lu}/u',
                $clave
            )
        ) {

            throw new Exception(
                "La contraseña debe contener al menos una letra mayúscula."
            );
        }


        if (
            !preg_match(
                '/[^\p{L}\p{N}\s]/u',
                $clave
            )
        ) {

            throw new Exception(
                "La contraseña debe contener al menos un carácter especial, por ejemplo: @, #, $, %, !."
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RECIBIR DATOS DE USUARIO
    |--------------------------------------------------------------------------
    */

    private function recibirDatosUsuario()
    {
        return [

            'nombre' =>
                trim(
                    $_POST['nombre']
                    ?? ''
                ),

            'apellido' =>
                trim(
                    $_POST['apellido']
                    ?? ''
                ),

            'dni' =>
                trim(
                    $_POST['dni']
                    ?? ''
                ),

            'nombre_usuario' =>
                trim(
                    $_POST['nombre_usuario']
                    ?? ''
                ),

            'email' =>
                trim(
                    $_POST['email']
                    ?? ''
                ),

            'rol_id' =>
                isset($_POST['rol_id'])
                    ?
                    (int)$_POST['rol_id']
                    :
                    0
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR USUARIO
    |--------------------------------------------------------------------------
    */

    private function validarUsuario(
        $datos,
        $idExcluir = null
    ) {
        if (
            $datos['nombre']
            ===
            ''
        ) {

            throw new Exception(
                "Debe ingresar el nombre."
            );
        }


        if (
            $datos['apellido']
            ===
            ''
        ) {

            throw new Exception(
                "Debe ingresar el apellido."
            );
        }


        if (
            $datos['dni']
            ===
            ''
        ) {

            throw new Exception(
                "Debe ingresar el DNI."
            );
        }


        if (
            !ctype_digit(
                $datos['dni']
            )
        ) {

            throw new Exception(
                "El DNI debe contener solamente números."
            );
        }


        if (
            strlen(
                $datos['dni']
            )
            <
            7
            ||
            strlen(
                $datos['dni']
            )
            >
            8
        ) {

            throw new Exception(
                "El DNI debe tener entre 7 y 8 números."
            );
        }


        if (
            $this->modelo
                ->existeDni(
                    $datos['dni'],
                    $idExcluir
                )
        ) {

            throw new Exception(
                "Ya existe un usuario con ese DNI."
            );
        }


        if (
            $datos['nombre_usuario']
            ===
            ''
        ) {

            throw new Exception(
                "Debe ingresar el nombre de usuario."
            );
        }


        if (
            $this->modelo
                ->existeNombreUsuario(
                    $datos['nombre_usuario'],
                    $idExcluir
                )
        ) {

            throw new Exception(
                "Ya existe un usuario con ese nombre de usuario."
            );
        }


        if (
            $datos['email']
            ===
            ''
        ) {

            throw new Exception(
                "Debe ingresar el correo electrónico."
            );
        }


        if (
            !filter_var(
                $datos['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {

            throw new Exception(
                "El correo electrónico ingresado no es válido."
            );
        }


        $emailNormalizado =
            strtolower(
                $datos['email']
            );


        if (
            $this->modelo
                ->existeEmail(
                    $emailNormalizado,
                    $idExcluir
                )
        ) {

            throw new Exception(
                "Ya existe un usuario con ese correo electrónico."
            );
        }


        $rolId =
            (int)$datos['rol_id'];


        if ($rolId <= 0) {

            throw new Exception(
                "Debe seleccionar un rol."
            );
        }


        $rol =
            $this->modelo
                ->obtenerRolPorId(
                    $rolId
                );


        if (!$rol) {

            throw new Exception(
                "El rol seleccionado no existe."
            );
        }


        if (
            (int)$rol['activo']
            !==
            1
        ) {

            throw new Exception(
                "El rol seleccionado se encuentra inactivo."
            );
        }


        return [

            'nombre' =>
                $datos['nombre'],

            'apellido' =>
                $datos['apellido'],

            'dni' =>
                $datos['dni'],

            'nombre_usuario' =>
                $datos['nombre_usuario'],

            'email' =>
                $emailNormalizado,

            'rol' =>
                $rol['nombre'],

            'rol_id' =>
                $rolId
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR ROL
    |--------------------------------------------------------------------------
    */

    private function validarRol(
        $datos,
        $idExcluir = null
    ) {
        $nombre =
            strtoupper(
                trim(
                    (string)(
                        $datos['nombre']
                        ?? ''
                    )
                )
            );


        if ($nombre === '') {

            throw new Exception(
                "Debe ingresar el nombre del rol."
            );
        }


        if (
            strlen($nombre)
            >
            100
        ) {

            throw new Exception(
                "El nombre del rol no puede superar los 100 caracteres."
            );
        }


        if (
            $this->modelo
                ->existeNombreRol(
                    $nombre,
                    $idExcluir
                )
        ) {

            throw new Exception(
                "Ya existe un rol con ese nombre."
            );
        }


        $descripcion =
            trim(
                (string)(
                    $datos['descripcion']
                    ?? ''
                )
            );


        if (
            strlen($descripcion)
            >
            500
        ) {

            throw new Exception(
                "La descripción no puede superar los 500 caracteres."
            );
        }


        $esAdmin =
            (int)(
                $datos['es_admin']
                ?? 0
            );


        $esAdmin =
            $esAdmin === 1
                ?
                1
                :
                0;


        return [

            'nombre' =>
                $nombre,

            'descripcion' =>
                $descripcion === ''
                    ?
                    null
                    :
                    $descripcion,

            'es_admin' =>
                $esAdmin
        ];
    }
}
