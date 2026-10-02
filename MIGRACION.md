# Migración de vistas — 26/09/2026

Esta entrega separa la presentación de SIGENMUNI manteniendo el HTML y las
expresiones PHP de presentación dentro de las vistas de cada módulo.
Se preparó sobre el ZIP recibido; no se modificó la instalación de XAMPP.

## Cambios

- 70 vistas/componentes con CSS extraído a archivos propios.
- 71 archivos CSS en total, incluyendo la pantalla de error de liquidación.
- 56 archivos JavaScript; 77 atributos de eventos se reemplazaron por listeners.
- Los estilos de elementos se trasladaron al CSS de su pantalla.
- Los datos dinámicos de PHP se entregan en bloques JSON inertes, con escape
  para evitar que caracteres como `</script>` rompan el documento.
- Las URLs usan la base existente y una versión calculada a partir del contenido
  del recurso para actualizar la caché al cambiar un archivo.
- El formulario de login incorpora token CSRF y el servidor rechaza con 403
  las solicitudes que no presentan un token válido.

```text
public/assets/
├── css/
│   ├── compartido/header_sigenmuni.css
│   ├── autenticacion/
│   ├── ayuda/
│   ├── categorias/
│   ├── conceptos/
│   ├── empleados/
│   ├── inicio/
│   ├── liquidacion/
│   ├── reportes/
│   └── usuarios/
├── js/
│   └── [módulo]/[nombre_de_la_vista].js
└── img/
```

Cada pantalla carga sólo sus recursos. Las reglas CSS originales se conservaron
en el mismo orden; esta entrega no intenta unificar todos los diseños bajo una
única hoja global. Esa unificación sería una etapa posterior.

En las cinco exportaciones Excel basadas en HTML, los estilos se leen desde el
archivo CSS y se incrustan en la respuesta descargada. Esto conserva el formato
del documento sin exigir una conexión al sitio cuando se abre la descarga.
Los estilos del correo y el XML de formato del exportador XLSX mantienen su
formato propio; no son recursos de una página web.

## Aplicación de la actualización

El ZIP de entrega es una **actualización**, no una instalación completa.
Contiene únicamente archivos nuevos o modificados respecto del ZIP recibido.

1. Hacer una copia de la carpeta SIGENMUNI actual.
2. Descomprimir el ZIP de entrega en una ubicación temporal.
3. Copiar el contenido de su carpeta `SIGENMUNI` dentro de la copia del proyecto,
   combinando las carpetas y reemplazando los archivos coincidentes. No colocar
   una carpeta SIGENMUNI dentro de otra ni borrar los directorios existentes.
4. Conservar `.env`, `vendor/`, las bibliotecas y la base de datos de la instalación.
   La actualización no incluye ni requiere modificaciones de la base de datos.
5. Abrir la copia desde Apache, recargar el login y ejecutar las comprobaciones
   manuales indicadas en REVISION_CODIGO.md antes de actualizar la instalación usada.

La dirección informada fue:
`http://localhost/PROGRAMACIONIII/SIGENMUNI/public/index.php?r=login`.
La base sigue detectándose automáticamente. No se codificó esta carpeta dentro
de los archivos CSS/JS ni se cambió la configuración de Apache.

El formulario de login que estuviera abierto antes de actualizar debe recargarse
para recibir el nuevo token CSRF.

## Mantenimiento

- Editar apariencia en `public/assets/css/<módulo>/<vista>.css`.
- Editar comportamiento en `public/assets/js/<módulo>/<vista>.js`.
- Mantener HTML, condicionales de presentación y datos iniciales en la vista PHP.
- Usar `sigenmuniUrlAsset()` para referenciar archivos y escapar la URL al
  insertarla en atributos HTML.
- Usar `sigenmuniJsonVista()` para datos en bloques `application/json`.
- Los atributos `data-event-*` identifican elementos para sus listeners; no contienen
  código que se ejecute mediante `eval`.

## Prueba aislada incluida

Desde la raíz del proyecto:

```powershell
C:\xampp\php\php.exe tests\verificar_base.php
```

Comprueba URLs, JSON, rechazo/aceptación CSRF del login con un modelo doble y
registro de rutas sin conectar a MySQL ni enviar correo. No sustituye las pruebas
del sistema con la base de datos.
