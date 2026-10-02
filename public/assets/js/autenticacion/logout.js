const datosPagina = JSON.parse(document.getElementById('datos-logout').textContent);



/*
|--------------------------------------------------------------------------
| ELIMINAR CACHÉ LOCAL DE LA SESIÓN
|--------------------------------------------------------------------------
*/

try {

    localStorage.removeItem(
        "sigenmuni_sesion"
    );

} catch (error) {

    console.warn(
        "No fue posible limpiar localStorage.",
        error
    );
}


/*
|--------------------------------------------------------------------------
| REDIRIGIR AL LOGIN DEL ROUTER
|--------------------------------------------------------------------------
*/

window.location.replace(
    datosPagina.autenticacionUrlLogin
);

