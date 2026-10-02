

document.addEventListener(
    "DOMContentLoaded",
    function()
    {

        const principales =
            document.querySelectorAll(
                ".check-principal"
            );


        const secundarios =
            document.querySelectorAll(
                ".check-secundario"
            );


        /*
        |--------------------------------------------------------------------------
        | ESCRITORIO → CELULAR
        |--------------------------------------------------------------------------
        */

        principales.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        secundarios.forEach(
                            function(secundario)
                            {

                                if (
                                    secundario.dataset.archivo
                                    ===
                                    archivo
                                ) {

                                    secundario.checked =
                                        check.checked;
                                }

                            }
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CELULAR → ESCRITORIO
        |--------------------------------------------------------------------------
        |
        | Solo los checks principales tienen name="permisos[]".
        | Por eso sincronizamos el check móvil con el principal.
        |
        |--------------------------------------------------------------------------
        */

        secundarios.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        principales.forEach(
                            function(principal)
                            {

                                if (
                                    principal.dataset.archivo
                                    ===
                                    archivo
                                ) {

                                    principal.checked =
                                        check.checked;
                                }

                            }
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DEPENDENCIA CONSULTAS Y REPORTES
        |--------------------------------------------------------------------------
        |
        | Reglas visuales:
        |
        | - Si se marca cualquier reporte hijo, se marca reportes.php.
        | - Si se desmarca reportes.php, se desmarcan todos sus hijos.
        | - Si se marca reportes.php, no se marcan automáticamente los hijos:
        |   el administrador decide cuáles habilitar.
        |
        |--------------------------------------------------------------------------
        */

        const archivoPadreReportes =
            "reportes.php";


        const archivosReportesHijos = [

            "reporte_empleados.php",

            "reporte_historial_empleado.php",

            "reporte_conceptos.php",

            "reporte_categorias.php",

            "reporte_liquidaciones.php",

            "estadisticas.php",

            "reporte_auditoria.php"
        ];


        function obtenerPrincipalPorArchivo(
            archivo
        ) {

            return document.querySelector(
                '.check-principal[data-archivo="'
                +
                archivo
                +
                '"]'
            );
        }


        function obtenerSecundarioPorArchivo(
            archivo
        ) {

            return document.querySelector(
                '.check-secundario[data-archivo="'
                +
                archivo
                +
                '"]'
            );
        }


        function sincronizarCheckArchivo(
            archivo,
            checked
        ) {

            const principal =
                obtenerPrincipalPorArchivo(
                    archivo
                );


            const secundario =
                obtenerSecundarioPorArchivo(
                    archivo
                );


            if (
                principal
                &&
                !principal.disabled
            ) {

                principal.checked =
                    checked;
            }


            if (
                secundario
                &&
                !secundario.disabled
            ) {

                secundario.checked =
                    checked;
            }
        }


        function activarPadreReportes()
        {

            sincronizarCheckArchivo(
                archivoPadreReportes,
                true
            );
        }


        function desactivarHijosReportes()
        {

            archivosReportesHijos.forEach(
                function(archivo)
                {

                    sincronizarCheckArchivo(
                        archivo,
                        false
                    );
                }
            );
        }


        principales.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        if (
                            archivo
                            ===
                            archivoPadreReportes
                            &&
                            !this.checked
                        ) {

                            desactivarHijosReportes();
                        }


                        if (
                            archivosReportesHijos.includes(
                                archivo
                            )
                            &&
                            this.checked
                        ) {

                            activarPadreReportes();
                        }
                    }
                );
            }
        );


        secundarios.forEach(
            function(check)
            {

                check.addEventListener(
                    "change",
                    function()
                    {

                        const archivo =
                            this.dataset.archivo;


                        if (
                            archivo
                            ===
                            archivoPadreReportes
                            &&
                            !this.checked
                        ) {

                            desactivarHijosReportes();
                        }


                        if (
                            archivosReportesHijos.includes(
                                archivo
                            )
                            &&
                            this.checked
                        ) {

                            activarPadreReportes();
                        }
                    }
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | SELECCIONAR TODOS
        |--------------------------------------------------------------------------
        */

        const seleccionarTodos =
            document.getElementById(
                "seleccionarTodos"
            );


        if (seleccionarTodos) {

            seleccionarTodos.addEventListener(
                "click",
                function()
                {

                    principales.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = true;
                            }

                        }
                    );


                    secundarios.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = true;
                            }

                        }
                    );

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | QUITAR TODOS
        |--------------------------------------------------------------------------
        */

        const quitarTodos =
            document.getElementById(
                "quitarTodos"
            );


        if (quitarTodos) {

            quitarTodos.addEventListener(
                "click",
                function()
                {

                    principales.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = false;
                            }

                        }
                    );


                    secundarios.forEach(
                        function(check)
                        {

                            if (!check.disabled) {
                                check.checked = false;
                            }

                        }
                    );

                }
            );
        }

    }
);

