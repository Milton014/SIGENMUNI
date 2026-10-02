# Revisión de código y verificación de la migración

Fecha: 26/09/2026. Base: SIGENMUNI.zip recibido del usuario. Entorno de
comprobación: PHP 8.2.12, Node y navegador Edge sin interfaz.

**Estado:** separación de CSS/JavaScript realizada y comprobada en forma estática
y con escenarios aislados de navegador. La revisión encontró problemas previos
que requieren seguimiento. No se certifica el sistema completo para producción
ni la exactitud de las liquidaciones sin pruebas de integración y casos esperados.

## Verificaciones completadas

| Comprobación | Resultado y alcance |
|---|---|
| Sintaxis PHP | 150 archivos, incluidas bibliotecas y prueba incorporada, sin errores de sintaxis. |
| Sintaxis JavaScript | 56 archivos sin errores de sintaxis; no contienen PHP. |
| Recursos | Referencias CSS/JS locales comprobadas contra archivos existentes. URLs bajo la base del proyecto y versión de caché. |
| Conservación CSS | Los 70 bloques originales de estilos se conservaron íntegramente antes de las reglas de elementos extraídas. |
| Separación | Sin bloques JavaScript ejecutables ni atributos onclick/onchange/onsubmit/oninput en las vistas migradas. Los bloques application/json contienen sólo datos. |
| Router | 72 nombres de rutas registrados sin duplicados; procesamiento de liquidación registrado sólo para POST. |
| Seguridad añadida | Token CSRF ausente, incorrecto o de tipo array produce 403 y no alcanza la búsqueda de credenciales. Token válido continúa al modelo doble. |
| JSON | Conserva acentos, comillas y `</script>` sin crear una etiqueta ejecutable. |
| Navegador | 12 escenarios con recursos interceptados y servidos desde los archivos locales, sin errores JavaScript. Comparación de dimensiones y estilos calculados frente a las vistas originales. |

Los 12 escenarios de navegador fueron: login; categoría nueva; categoría editar
con registro y sin registro; valor de concepto nuevo; inicio; estadísticas vacías
y con datos; rol nuevo administrador y no administrador; exportación HTML Excel de
categorías; guía de categorías. Se comprobaron además el botón de visibilidad de
contraseña, la cancelación del formulario vacío de categorías, la visibilidad del
aviso de administrador y la escritura de la caché visual de inicio.

Las pruebas de estadísticas utilizan un doble de Chart para comprobar las cuatro
configuraciones y la transferencia de datos, sin descargar la dependencia. No
verifican el dibujo real de Chart.js ni la generación de PDF/XLSX. El Excel basado
en HTML se comprobó como documento HTML con estilos incrustados, no abierto en Excel.

## Corrección incluida

**CSRF en el login.** El controlador aceptaba credenciales POST sin comprobar un
token de sesión. Ahora valida el token antes de buscar credenciales y la vista
incluye el campo oculto. Archivos: `modulos/autenticacion/controlador/AutenticacionControlador.php`
y `modulos/autenticacion/vista/login.php`. Las pruebas cubren rechazo y continuación
del flujo; no se usaron credenciales reales.

## Hallazgos pendientes, por prioridad

### Alta — La sesión no vuelve a comprobar el usuario ni su rol asignado

En `core/seguridad.php:57`, `verificarSesion()` sólo verifica que exista
`$_SESSION['usuario']`. `obtenerRolActual()` (línea 125) consulta el rol guardado
en la sesión, sin volver a consultar `usuario.activo` ni el `rol_id` actual del
usuario. Desactivar un usuario o asignarle otro rol desde otra sesión puede dejar
operativa su sesión anterior con el rol previo. Revalidar esos datos contra la
cuenta actual antes de autorizar y rechazar sesiones de usuarios inactivos.
Probar con dos sesiones independientes y cambios de rol/estado.

### Alta — Recuperación de acceso sin límite de intentos en el código revisado

En `modulos/autenticacion/controlador/AutenticacionControlador.php`, los métodos
`recuperar()` y `actualizarAcceso()` generan y comparan un código de seis dígitos
con vencimiento, pero no encontré un contador persistente ni un bloqueo por
intentos fallidos. La rama «Código incorrecto» (línea 953) sólo devuelve un error.
Definir límites por cuenta y origen, invalidación por intentos y control de
reenvíos. No se comprobó si existe una protección adicional en Apache o un proxy.

### Alta — El consumo del código de recuperación no es atómico

El controlador comprueba el código antes de llamar a
`AutenticacionModelo::actualizarAcceso()` (`modulos/autenticacion/modelo/AutenticacionModelo.php:381`).
Ese UPDATE filtra sólo por id y luego limpia el código. Dos peticiones desde
sesiones distintas podrían validar el mismo código antes de que se borre y
actualizar ambas las credenciales. La actualización debe comprobar también el
código y vencimiento en una operación atómica, o bloquear el registro dentro de
una transacción; comprobar que exactamente una petición puede consumirlo.

### Alta — Procesamiento concurrente de una liquidación

En `modulos/liquidacion/controlador/LiquidacionControlador.php:3160`, `procesar()`
lee la liquidación y valida BORRADOR antes de iniciar la transacción (línea 3647).
Después limpia el procesamiento y vuelve a generar detalles. No encontré un
bloqueo del registro ni una segunda comprobación del estado bajo bloqueo. Dos
usuarios podrían superar la validación inicial y procesar el mismo borrador.
La transacción evita una escritura parcial, pero no demuestra exclusión entre
procesamientos. Revalidar estado dentro de una transacción con bloqueo y probar
dos solicitudes concurrentes. Este escenario no fue ejecutado contra MySQL.

### Media — Dependencia de gráficos sin versión fijada

`modulos/reportes/vista/estadisticas.php:247` carga Chart.js desde una URL CDN sin
versión. Una actualización externa o la falta de conexión puede afectar la
pantalla. Elegir una versión después de probar los gráficos y sus exportaciones,
y fijarla o servir una copia local. No se cambió la dependencia en esta migración.

## Verificación funcional que falta cerrar

En una copia de la base de datos, comprobar:

1. Login válido/inválido, logout y recuperación completa con correo de prueba.
2. Roles, permisos y rechazo del acceso directo a cada ruta protegida.
3. Altas, edición y cambios de estado de categorías, conceptos, empleados y usuarios.
4. Conceptos por empleado y valores por categoría, con y sin registros.
5. Mensual, complementaria, SAC y gastos protocolares usando casos cuyos resultados
   esperados estén definidos; contrastar detalles, totales y recibos.
6. Reportes impresos, PDF, Excel HTML y XLSX, incluidos filtros y acentos.
7. Envío de recibos sólo a un destinatario de prueba.
8. Sesiones simultáneas, recuperación simultánea y procesamiento concurrente.

No se conectó la copia de trabajo a MySQL, no se modificaron datos y no se enviaron
correos. Tampoco se cambió el cálculo de sueldos ni la configuración de la instalación.
