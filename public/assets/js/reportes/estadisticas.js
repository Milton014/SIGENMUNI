const datosPagina = JSON.parse(document.getElementById('datos-estadisticas').textContent);

document.querySelectorAll('[data-event-click="estadisticas-1"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
exportarPDF()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});

document.querySelectorAll('[data-event-click="estadisticas-2"]').forEach(function (elemento) {
    elemento.addEventListener('click', function (event) {
        const resultado = (function (event) {
exportarExcel()
        }).call(this, event);
        if (resultado === false) event.preventDefault();
    });
});



const charts = {};


/*
|--------------------------------------------------------------------------
| FORMATO MONEDA
|--------------------------------------------------------------------------
*/

function formatoPesos(valor){

    return new Intl.NumberFormat(
        'es-AR',
        {
            style:'currency',
            currency:'ARS',
            minimumFractionDigits:2
        }
    ).format(valor);
}


/*
|--------------------------------------------------------------------------
| CONFIGURACIÓN COMÚN
|--------------------------------------------------------------------------
*/

Chart.defaults.font.family =
    'Arial, Helvetica, sans-serif';

Chart.defaults.color =
    '#374151';


/*
|--------------------------------------------------------------------------
| EMPLEADOS ACTIVOS ACTUALES POR CATEGORÍA
|--------------------------------------------------------------------------
*/

if (datosPagina.graficoEmpleadosCategoria.labels && datosPagina.graficoEmpleadosCategoria.labels.length) {

charts.categoria =
    new Chart(
        document.getElementById(
            'graficoCategoria'
        ),
        {
            type:'bar',

            data:{
                labels:
                    datosPagina.graficoEmpleadosCategoria.labels,

                datasets:[
                    {
                        label:'Cantidad de empleados',

                        data:
                            datosPagina.graficoEmpleadosCategoria.valores,

                        borderWidth:1
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    legend:{
                        display:false
                    }
                },

                scales:{
                    y:{
                        beginAtZero:true,

                        ticks:{
                            precision:0
                        }
                    }
                }
            }
        }
    );

}


/*
|--------------------------------------------------------------------------
| TOTAL NETO POR PERÍODO
|--------------------------------------------------------------------------
*/

if (datosPagina.graficoNetoPeriodo.labels && datosPagina.graficoNetoPeriodo.labels.length) {

charts.periodo =
    new Chart(
        document.getElementById(
            'graficoPeriodo'
        ),
        {
            type:'line',

            data:{
                labels:
                    datosPagina.graficoNetoPeriodo.labels,

                datasets:[
                    {
                        label:'Total neto',

                        data:
                            datosPagina.graficoNetoPeriodo.valores,

                        borderWidth:2,
                        tension:.25
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    tooltip:{
                        callbacks:{
                            label:function(context){

                                return formatoPesos(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales:{
                    y:{
                        beginAtZero:true,

                        ticks:{
                            callback:function(valor){

                                return '$ '
                                    +
                                    Number(valor)
                                        .toLocaleString(
                                            'es-AR'
                                        );
                            }
                        }
                    }
                }
            }
        }
    );

}


/*
|--------------------------------------------------------------------------
| COMPOSICIÓN POR TIPO DE CONCEPTO
|--------------------------------------------------------------------------
*/

if (datosPagina.graficoConceptos.labels && datosPagina.graficoConceptos.labels.length) {

charts.conceptos =
    new Chart(
        document.getElementById(
            'graficoConceptos'
        ),
        {
            type:'doughnut',

            data:{
                labels:
                    datosPagina.graficoConceptos.labels,

                datasets:[
                    {
                        label:'Importe',

                        data:
                            datosPagina.graficoConceptos.valores,

                        borderWidth:1
                    }
                ]
            },

            options:{
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    tooltip:{
                        callbacks:{
                            label:function(context){

                                const valores =
                                    context.dataset.data;

                                const total =
                                    valores.reduce(
                                        function(acumulado, valor){

                                            return acumulado
                                                +
                                                Number(valor || 0);
                                        },
                                        0
                                    );

                                const valorActual =
                                    Number(
                                        context.raw
                                        || 0
                                    );

                                const porcentaje =
                                    total > 0
                                        ? (
                                            valorActual
                                            /
                                            total
                                        ) * 100
                                        : 0;

                                return context.label
                                    +
                                    ': '
                                    +
                                    formatoPesos(
                                        valorActual
                                    )
                                    +
                                    ' ('
                                    +
                                    porcentaje.toLocaleString(
                                        'es-AR',
                                        {
                                            minimumFractionDigits:2,
                                            maximumFractionDigits:2
                                        }
                                    )
                                    +
                                    '%)';
                            }
                        }
                    }
                }
            }
        }
    );

}


/*
|--------------------------------------------------------------------------
| PRINCIPALES DESCUENTOS
|--------------------------------------------------------------------------
*/

if (datosPagina.graficoDescuentos.labels && datosPagina.graficoDescuentos.labels.length) {

charts.descuentos =
    new Chart(
        document.getElementById(
            'graficoDescuentos'
        ),
        {
            type:'bar',

            data:{
                labels:
                    datosPagina.graficoDescuentos.labels,

                datasets:[
                    {
                        label:'Total descuentos',

                        data:
                            datosPagina.graficoDescuentos.valores,

                        borderWidth:1
                    }
                ]
            },

            options:{
                indexAxis:'y',
                responsive:true,
                maintainAspectRatio:false,

                plugins:{
                    legend:{
                        display:false
                    },

                    tooltip:{
                        callbacks:{
                            label:function(context){

                                return formatoPesos(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales:{
                    x:{
                        beginAtZero:true,

                        ticks:{
                            callback:function(valor){

                                return '$ '
                                    +
                                    Number(valor)
                                        .toLocaleString(
                                            'es-AR'
                                        );
                            }
                        }
                    }
                }
            }
        }
    );

}


/*
|--------------------------------------------------------------------------
| EXPORTAR PDF
|--------------------------------------------------------------------------
|
| Se envían:
|
| - imagenes: array posicional compatible con el PDF actual;
| - graficos: objeto identificado por nombre para la próxima versión del PDF.
|
| El array posicional conserva los lugares vacíos con null. De esa manera,
| aunque un gráfico no tenga datos, los títulos no se desplazan.
|
|--------------------------------------------------------------------------
*/

function exportarPDF(){

    const ordenGraficos = [
        'categoria',
        'periodo',
        'conceptos',
        'descuentos'
    ];


    const imagenes =
        ordenGraficos.map(
            function(clave){

                return charts[clave]
                    ? charts[clave].toBase64Image()
                    : null;
            }
        );


    const graficos = {};


    ordenGraficos.forEach(
        function(clave){

            if (charts[clave]) {

                graficos[clave] =
                    imagenGraficoParaExcel(
                        charts[clave]
                    );
            }
        }
    );


    const urlPdf =
        datosPagina.estadisticasUrlPdf;


    fetch(
        urlPdf,
        {
            method:'POST',

            headers:{
                'Content-Type':
                    'application/json'
            },

            body:
                JSON.stringify(
                    {
                        imagenes:
                            imagenes,

                        graficos:
                            graficos
                    }
                )
        }
    )
    .then(
        function(respuesta){

            if (!respuesta.ok){

                return respuesta
                    .text()
                    .then(
                        function(texto){

                            throw new Error(
                                texto
                                ||
                                'No se pudo generar el PDF.'
                            );
                        }
                    );
            }

            return respuesta.blob();
        }
    )
    .then(
        function(blob){

            const url =
                URL.createObjectURL(
                    blob
                );


            window.open(
                url,
                '_blank'
            );


            setTimeout(
                function(){

                    URL.revokeObjectURL(
                        url
                    );
                },
                60000
            );
        }
    )
    .catch(
        function(error){

            alert(
                error.message
            );
        }
    );
}


/*
|--------------------------------------------------------------------------
| IMAGEN DE GRÁFICO PARA EXCEL
|--------------------------------------------------------------------------
|
| Chart.js genera el canvas con fondo transparente. Para que el fondo viaje
| JUNTO con la imagen al moverla dentro de Excel, creamos un segundo canvas:
|
| 1. dibujamos un fondo gris muy claro;
| 2. copiamos encima el gráfico original;
| 3. agregamos un borde suave;
| 4. exportamos ese canvas como PNG.
|
| Así el fondo forma parte real de la imagen y no queda pegado a las celdas.
|
|--------------------------------------------------------------------------
*/

function imagenGraficoParaExcel(chart){

    const canvasOriginal =
        chart.canvas;


    const canvasExportacion =
        document.createElement(
            'canvas'
        );


    canvasExportacion.width =
        canvasOriginal.width;


    canvasExportacion.height =
        canvasOriginal.height;


    const contexto =
        canvasExportacion.getContext(
            '2d'
        );


    /*
    |--------------------------------------------------------------------------
    | FONDO
    |--------------------------------------------------------------------------
    */

    contexto.fillStyle =
        '#F3F4F6';


    contexto.fillRect(
        0,
        0,
        canvasExportacion.width,
        canvasExportacion.height
    );


    /*
    |--------------------------------------------------------------------------
    | GRÁFICO
    |--------------------------------------------------------------------------
    */

    contexto.drawImage(
        canvasOriginal,
        0,
        0
    );


    /*
    |--------------------------------------------------------------------------
    | BORDE
    |--------------------------------------------------------------------------
    */

    const escala =
        window.devicePixelRatio
        || 1;


    const grosor =
        Math.max(
            2,
            Math.round(
                escala
            )
        );


    contexto.strokeStyle =
        '#D1D5DB';


    contexto.lineWidth =
        grosor;


    contexto.strokeRect(
        grosor / 2,
        grosor / 2,
        canvasExportacion.width - grosor,
        canvasExportacion.height - grosor
    );


    return canvasExportacion
        .toDataURL(
            'image/png'
        );
}


/*
|--------------------------------------------------------------------------
| EXPORTAR EXCEL
|--------------------------------------------------------------------------
|
| El XLSX consulta nuevamente los datos desde la base de datos.
| Desde el navegador se envían solamente las imágenes de los gráficos.
|
|--------------------------------------------------------------------------
*/

function exportarExcel(){

    const ordenGraficos = [
        'categoria',
        'periodo',
        'conceptos',
        'descuentos'
    ];


    const graficos = {};


    ordenGraficos.forEach(
        function(clave){

            if (charts[clave]) {

                graficos[clave] =
                    charts[clave]
                        .toBase64Image();
            }
        }
    );


    const urlExcel =
        datosPagina.estadisticasUrlExcel;


    fetch(
        urlExcel,
        {
            method:'POST',

            headers:{
                'Content-Type':
                    'application/json'
            },

            body:
                JSON.stringify(
                    {
                        graficos:
                            graficos
                    }
                )
        }
    )
    .then(
        function(respuesta){

            if (!respuesta.ok){

                return respuesta
                    .text()
                    .then(
                        function(texto){

                            throw new Error(
                                texto
                                ||
                                'No se pudo generar el archivo Excel.'
                            );
                        }
                    );
            }


            const disposicion =
                respuesta.headers.get(
                    'Content-Disposition'
                )
                || '';


            let nombreArchivo =
                'estadisticas_sigenmuni.xlsx';


            const coincidencia =
                disposicion.match(
                    /filename="?([^";]+)"?/i
                );


            if (
                coincidencia
                &&
                coincidencia[1]
            ) {

                nombreArchivo =
                    coincidencia[1];
            }


            return respuesta
                .blob()
                .then(
                    function(blob){

                        return {
                            blob:
                                blob,

                            nombre:
                                nombreArchivo
                        };
                    }
                );
        }
    )
    .then(
        function(resultado){

            const url =
                URL.createObjectURL(
                    resultado.blob
                );


            const enlace =
                document.createElement(
                    'a'
                );


            enlace.href =
                url;


            enlace.download =
                resultado.nombre;


            document.body.appendChild(
                enlace
            );


            enlace.click();


            enlace.remove();


            setTimeout(
                function(){

                    URL.revokeObjectURL(
                        url
                    );
                },
                60000
            );
        }
    )
    .catch(
        function(error){

            alert(
                error.message
            );
        }
    );
}

