const datosPagina = JSON.parse(document.getElementById('datos-inicio').textContent);



/*
|--------------------------------------------------------------------------
| CACHÉ VISUAL DE LA SESIÓN CON LOCALSTORAGE
|--------------------------------------------------------------------------
|
| IMPORTANTE:
| localStorage NO reemplaza la sesión PHP.
|
| La sesión PHP sigue siendo la fuente real de autenticación, permisos
| y seguridad. En localStorage solo guardamos información visual no sensible
| para mostrar el usuario autenticado en la interfaz.
|
|--------------------------------------------------------------------------
*/

    const CLAVE_CACHE_SESION =
        'sigenmuni_sesion';


    const sesionServidor = {

        id_usuario:
            datosPagina.idUsuario,

        usuario:
            datosPagina.usuarioSesion,

        nombre_completo:
            datosPagina.nombreCompleto,

        rol:
            datosPagina.rol,

        actualizado_en:
            new Date().toISOString()
    };


    /*
    |--------------------------------------------------------------------------
    | GUARDAR / ACTUALIZAR CACHÉ
    |--------------------------------------------------------------------------
    */

    try {

        localStorage.setItem(
            CLAVE_CACHE_SESION,
            JSON.stringify(
                sesionServidor
            )
        );

    } catch (error) {

        console.warn(
            'No fue posible guardar la caché de sesión en localStorage.',
            error
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LEER CACHÉ Y MOSTRAR NOMBRE + ROL
    |--------------------------------------------------------------------------
    */

    try {

        const cacheGuardada =
            localStorage.getItem(
                CLAVE_CACHE_SESION
            );


        if (cacheGuardada) {

            const datosCache =
                JSON.parse(
                    cacheGuardada
                );


            const nombreUsuarioHeader =
                document.getElementById(
                    'nombreUsuarioHeader'
                );


            const rolUsuarioHeader =
                document.getElementById(
                    'rolUsuarioHeader'
                );


            if (
                nombreUsuarioHeader
                &&
                datosCache.nombre_completo
            ) {

                nombreUsuarioHeader.textContent =
                    datosCache.nombre_completo;
            }


            if (
                rolUsuarioHeader
                &&
                datosCache.rol
            ) {

                rolUsuarioHeader.textContent =
                    datosCache.rol;
            }
        }

    } catch (error) {

        /*
        | Si el contenido de la caché estuviera corrupto,
        | se elimina. La página sigue mostrando los datos
        | renderizados por PHP.
        */

        localStorage.removeItem(
            CLAVE_CACHE_SESION
        );

        console.warn(
            'La caché de sesión era inválida y fue eliminada.',
            error
        );
    }


/*
|--------------------------------------------------------------------------
| BUSCADOR DE MÓDULOS
|--------------------------------------------------------------------------
*/

    const buscador =
        document.getElementById(
            'buscadorModulos'
        );

    const tarjetas =
        document.querySelectorAll(
            '.card-modulo'
        );

    const sinResultados =
        document.getElementById(
            'sinResultados'
        );


    if (
        buscador
        &&
        sinResultados
    ) {

        buscador.addEventListener(
            'input',
            function ()
            {

                const texto =
                    this.value
                        .toLowerCase()
                        .trim();

                let visibles =
                    0;


                tarjetas.forEach(
                    function(tarjeta)
                    {

                        const nombre =
                            (
                                tarjeta.getAttribute(
                                    'data-nombre'
                                )
                                ??
                                ''
                            )
                            .toLowerCase();


                        if (
                            nombre.includes(
                                texto
                            )
                        ) {

                            tarjeta
                                .classList
                                .remove(
                                    'oculto'
                                );

                            visibles++;

                        } else {

                            tarjeta
                                .classList
                                .add(
                                    'oculto'
                                );
                        }
                    }
                );


                sinResultados.style.display =
                    visibles === 0
                        ?
                        'block'
                        :
                        'none';
            }
        );
    }
