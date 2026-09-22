<?php

/*
|--------------------------------------------------------------------------
| UTILIDADES DE URL
|--------------------------------------------------------------------------
|
| Una URL no es una ruta de disco. Estas funciones producen rutas desde la
| raíz del sitio, sin construir dominios con HTTP_HOST ni leer destinos
| desde parámetros del usuario.
|
| sigenmuniUrlArchivo(): archivos PHP generales ubicados en la raíz.
| sigenmuniUrlEntrada(): Front Controller public/index.php, sin parámetros.
| sigenmuniUrlRuta():    ruta registrada en el Router, con parámetros.
|--------------------------------------------------------------------------
*/

function sigenmuniNormalizarBaseUrl($valor)
{
    if (!is_string($valor)) {
        throw new RuntimeException(
            'config/app.php: url_base debe ser texto o null.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR LA RUTA BASE
    |--------------------------------------------------------------------------
    |
    | Decodificar y volver a codificar cada segmento permite carpetas con
    | espacios y evita caracteres de control o destinos externos.
    |
    */

    $ruta = rawurldecode($valor);

    if (
        preg_match(
            '/[\x00-\x1F\x7F?#\\\\]/',
            $ruta
        )
    ) {
        throw new RuntimeException(
            'config/app.php: url_base contiene caracteres no permitidos.'
        );
    }

    if (
        $ruta === ''
        ||
        $ruta === '/'
    ) {
        return '';
    }

    if (
        $ruta[0] !== '/'
        ||
        strpos(
            $ruta,
            '//'
        ) !== false
    ) {
        throw new RuntimeException(
            'config/app.php: url_base debe comenzar con una sola barra y no incluir dominio.'
        );
    }

    $segmentos =
        explode(
            '/',
            trim(
                $ruta,
                '/'
            )
        );

    foreach (
        $segmentos
        as
        $segmento
    ) {
        if (
            $segmento === '.'
            ||
            $segmento === '..'
            ||
            $segmento === ''
        ) {
            throw new RuntimeException(
                'config/app.php: url_base contiene un segmento no válido.'
            );
        }
    }

    return
        '/'
        .
        implode(
            '/',
            array_map(
                'rawurlencode',
                $segmentos
            )
        );
}


function sigenmuniBaseUrl()
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    $config =
        require __DIR__
        . '/../config/app.php';

    if (
        !is_array($config)
        ||
        !array_key_exists(
            'url_base',
            $config
        )
    ) {
        throw new RuntimeException(
            'Falta url_base en config/app.php.'
        );
    }

    if ($config['url_base'] !== null) {

        $base =
            sigenmuniNormalizarBaseUrl(
                $config['url_base']
            );

        return $base;
    }

    /*
    |--------------------------------------------------------------------------
    | DETECCIÓN AUTOMÁTICA DE LA URL BASE
    |--------------------------------------------------------------------------
    |
    | Se compara la ubicación física del script con su ubicación pública.
    | No se usa REQUEST_URI porque puede contener consultas o PATH_INFO.
    |
    */

    $raizFisica =
        realpath(
            dirname(
                __DIR__
            )
        );

    $archivoServidor =
        $_SERVER['SCRIPT_FILENAME']
        ??
        null;

    $nombreServidor =
        $_SERVER['SCRIPT_NAME']
        ??
        null;

    if (
        !is_string(
            $archivoServidor
        )
        ||
        !is_string(
            $nombreServidor
        )
    ) {
        throw new RuntimeException(
            'No se pudo detectar la URL base. Configurá url_base en config/app.php.'
        );
    }

    $scriptFisico =
        realpath(
            $archivoServidor
        );

    if (
        $raizFisica === false
        ||
        $scriptFisico === false
    ) {
        throw new RuntimeException(
            'No se pudo resolver la ubicación del script. Configurá url_base en config/app.php.'
        );
    }

    $raizFisica =
        str_replace(
            '\\',
            '/',
            $raizFisica
        );

    $scriptFisico =
        str_replace(
            '\\',
            '/',
            $scriptFisico
        );

    $prefijo =
        rtrim(
            $raizFisica,
            '/'
        )
        . '/';

    if (
        strpos(
            $scriptFisico,
            $prefijo
        )
        !==
        0
    ) {
        throw new RuntimeException(
            'El script no está dentro del proyecto. Configurá url_base en config/app.php.'
        );
    }

    $relativa =
        '/'
        .
        substr(
            $scriptFisico,
            strlen(
                $prefijo
            )
        );

    $nombreScript =
        rawurldecode(
            $nombreServidor
        );

    if (
        strlen(
            $nombreScript
        )
        <
        strlen(
            $relativa
        )
        ||
        substr(
            $nombreScript,
            -strlen(
                $relativa
            )
        )
        !==
        $relativa
    ) {
        throw new RuntimeException(
            'La URL usa un alias o una raíz pública diferente. Revisá url_base en config/app.php y la configuración del servidor.'
        );
    }

    $base =
        sigenmuniNormalizarBaseUrl(
            substr(
                $nombreScript,
                0,
                strlen(
                    $nombreScript
                )
                -
                strlen(
                    $relativa
                )
            )
        );

    return $base;
}


function sigenmuniAgregarConsultaUrl(
    $url,
    array $parametros = []
) {
    $consulta =
        http_build_query(
            $parametros,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

    return
        $consulta === ''
            ?
            $url
            :
            $url
            . '?'
            . $consulta;
}


function sigenmuniUrlArchivo(
    $archivo,
    array $parametros = []
) {
    /*
    |--------------------------------------------------------------------------
    | ARCHIVOS PHP GENERALES DE LA RAÍZ
    |--------------------------------------------------------------------------
    |
    | Solo admite nombres simples de archivos PHP ubicados en la raíz del
    | proyecto. No admite rutas arbitrarias.
    |
    */

    if (
        !is_string(
            $archivo
        )
        ||
        !preg_match(
            '/\A[a-zA-Z0-9_-]+\.php\z/',
            $archivo
        )
    ) {
        throw new InvalidArgumentException(
            'Nombre de archivo de entrada no válido.'
        );
    }

    return
        sigenmuniAgregarConsultaUrl(
            sigenmuniBaseUrl()
            . '/'
            . $archivo,
            $parametros
        );
}


function sigenmuniUrlEntrada()
{
    return
        sigenmuniBaseUrl()
        . '/public/index.php';
}


function sigenmuniUrlRuta(
    $ruta,
    array $parametros = []
) {
    /*
    |--------------------------------------------------------------------------
    | RUTA DEL ROUTER
    |--------------------------------------------------------------------------
    */

    if (
        !is_string(
            $ruta
        )
        ||
        !preg_match(
            '/\A[a-z0-9]+(?:[\/_-][a-z0-9]+)*\z/',
            $ruta
        )
        ||
        strlen(
            $ruta
        )
        >
        120
    ) {
        throw new InvalidArgumentException(
            'Nombre de ruta no válido.'
        );
    }

    if (
        array_key_exists(
            'r',
            $parametros
        )
    ) {
        throw new InvalidArgumentException(
            'El parámetro r se define mediante el nombre de la ruta.'
        );
    }

    return
        sigenmuniAgregarConsultaUrl(
            sigenmuniUrlEntrada(),
            [
                'r' =>
                    $ruta
            ]
            +
            $parametros
        );
}
