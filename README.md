# SIGENMUNI

## Sistema de Gestión Municipal y Liquidación de Sueldos

**SIGENMUNI** es un sistema web desarrollado en **PHP + MySQL** para la gestión administrativa y la liquidación de sueldos de una municipalidad.

El proyecto fue desarrollado para la **Municipalidad de Fortín Lugones** y actualmente se encuentra organizado con una arquitectura basada en:

- Front Controller.
- Router propio.
- MVC.
- Vertical Slice por módulo.
- Control de acceso por roles y permisos.
- Protección CSRF.
- Auditoría.
- Generación de PDF.
- Exportación de reportes.
- Envío de correos electrónicos.
- Composer para gestión de dependencias.
- Variables de entorno mediante `vlucas/phpdotenv`.
- Separación de configuraciones sensibles del código fuente.

---

# 1. Objetivo del sistema

SIGENMUNI tiene como objetivo centralizar en una única aplicación la gestión de:

- empleados municipales;
- conceptos salariales;
- conceptos particulares por empleado;
- categorías;
- liquidaciones;
- recibos de sueldo;
- reportes;
- estadísticas;
- auditoría;
- usuarios;
- roles;
- permisos;
- recuperación de acceso;
- ayuda del sistema.

La aplicación permite administrar la información necesaria para calcular y consultar liquidaciones municipales, manteniendo además controles de seguridad, trazabilidad y separación de responsabilidades.

---

# 2. Tecnologías utilizadas

El proyecto utiliza principalmente:

- PHP 8.x.
- MySQL / MariaDB.
- HTML5.
- CSS3.
- JavaScript.
- `mysqli`.
- FPDF.
- PHPMailer.
- Composer.
- `vlucas/phpdotenv` v5.7.0.

Entorno de desarrollo utilizado:

- XAMPP.
- Apache.
- PHP 8.2.
- MySQL / MariaDB.
- Visual Studio Code.
- Windows PowerShell.
- Git y GitHub.

Composer se utiliza para administrar dependencias instalables del proyecto. Actualmente se incorporó `vlucas/phpdotenv` para gestionar variables de entorno.

---

# 3. Arquitectura general

El sistema utiliza un **Front Controller** ubicado en:

```text
public/index.php
```

Las solicitudes de los módulos ingresan por este archivo.

Ejemplo:

```text
public/index.php?r=empleados
```

Flujo general:

```text
Navegador
   ↓
public/index.php
   ↓
core/bootstrap.php
   ↓
Composer / vendor/autoload.php
   ↓
vlucas/phpdotenv / .env
   ↓
Router
   ↓
rutas.php del módulo
   ↓
Controlador
   ↓
Modelo / Servicio
   ↓
Vista
```

El archivo `index.php` ubicado en la raíz del proyecto funciona como punto de entrada amigable y redirige al Login administrado por el Router:

```text
SIGENMUNI/
   ↓
index.php
   ↓
public/index.php?r=login
```

---

# 4. Estructura de carpetas

La estructura principal actual es:

```text
SIGENMUNI/
│
├── componentes/
│   └── header_sigenmuni.php
│
├── config/
│   ├── app.php
│   ├── conexion.php
│   ├── conexion_example.php
│   ├── config_correo.php
│   ├── config_correo_example.php
│   └── modulos.php
│
├── core/
│   ├── Auditoria.php
│   ├── bootstrap.php
│   ├── Csrf.php
│   ├── Router.php
│   ├── seguridad.php
│   └── Url.php
│
├── database/
│   └── sigenmuni.sql
│
├── lib/
│   ├── fpdf/
│   └── phpmailer/
│
├── modulos/
│   ├── autenticacion/
│   ├── ayuda/
│   ├── categorias/
│   ├── conceptos/
│   ├── empleados/
│   ├── inicio/
│   ├── liquidacion/
│   ├── reportes/
│   └── usuarios/
│
├── public/
│   ├── assets/
│   │   └── img/
│   │       └── escudo.jpg
│   └── index.php
│
├── storage/
│   └── temp_recibos/
│       └── .gitkeep
│
├── vendor/                 # generado por Composer, no versionado
├── .env                    # configuración real, no versionada
├── .env.example            # plantilla de variables de entorno
├── .gitignore
├── composer.json
├── composer.lock
├── README.md
└── index.php
```

> `vendor/` se genera ejecutando `composer install` y no se almacena en GitHub.  
> `.env` contiene la configuración real y tampoco se versiona.

Los archivos `conexion_example.php` y `config_correo_example.php`, si se mantienen en el proyecto, quedan como referencia heredada. El mecanismo principal de configuración sensible es actualmente `.env` + `.env.example`.

---

# 5. Organización por Vertical Slice

Cada módulo contiene su propia lógica.

Ejemplo:

```text
modulos/empleados/
│
├── controlador/
│   └── EmpleadoControlador.php
│
├── modelo/
│   └── EmpleadoModelo.php
│
├── vista/
│   ├── empleado_nuevo.php
│   ├── empleado_editar.php
│   ├── empleado_ver.php
│   ├── empleado_estado.php
│   └── empleados.php
│
└── rutas.php
```

Ventajas:

- mejor organización;
- menor dependencia entre módulos;
- mantenimiento más sencillo;
- rutas claramente definidas;
- mayor facilidad para incorporar nuevas funcionalidades;
- separación entre presentación, acceso a datos y lógica de aplicación.

---

# 6. Core del sistema

## Router.php

Registra rutas explícitas y las despacha según:

- método HTTP;
- nombre de ruta;
- tipo de acceso;
- permiso requerido.

### Ruta con permiso

Requiere sesión, rol válido y permiso habilitado.

```php
$router->get(
    'empleados',
    $accion,
    'empleados.php'
);
```

### Ruta con sesión

Requiere un usuario autenticado.

Ejemplo:

```text
inicio
```

### Ruta pública

No requiere sesión.

Se utiliza principalmente en:

```text
login
recuperar
verificar-codigo
actualizar-acceso
```

## bootstrap.php

`core/bootstrap.php` centraliza el arranque general de SIGENMUNI.

Actualmente realiza, en orden:

1. obtiene la raíz del proyecto;
2. carga `vendor/autoload.php`;
3. inicializa `vlucas/phpdotenv`;
4. carga las variables desde `.env`;
5. valida variables obligatorias;
6. valida tipos de datos de variables importantes;
7. configura la zona horaria;
8. carga utilidades de URL y seguridad;
9. inicia la sesión;
10. resuelve la URL base;
11. carga la conexión MySQL;
12. crea el Router;
13. carga los módulos habilitados;
14. registra las rutas de cada módulo;
15. devuelve la instancia del Router.

Las variables obligatorias principales validadas son:

```text
APP_NAME
APP_ENV
APP_DEBUG
APP_TIMEZONE

DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME

MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
```

Además:

- `DB_PORT` y `MAIL_PORT` deben ser enteros;
- `APP_DEBUG` debe ser booleano;
- `APP_ENV` solo admite `development`, `testing` o `production`;
- `APP_TIMEZONE` debe corresponder a una zona horaria válida.

`DB_PASSWORD` puede existir con valor vacío para permitir determinados entornos locales de MySQL.

## seguridad.php

Centraliza la seguridad general.

Funciones principales:

```text
iniciarSesionSiHaceFalta()
verificarSesion()
obtenerConexionSeguridad()
obtenerRolActual()
soloAdmin()
verificarPermisoModulo()
```

## Csrf.php

Implementa protección CSRF para acciones POST que modifican información.

## Url.php

Centraliza la generación de URLs.

Funciones principales:

```text
sigenmuniBaseUrl()
sigenmuniUrlEntrada()
sigenmuniUrlRuta()
```

## Auditoria.php

Centraliza el registro de acciones relevantes realizadas por usuarios.

---

# 7. Módulo de Autenticación

Ubicación:

```text
modulos/autenticacion/
```

Estructura:

```text
autenticacion/
├── controlador/
│   └── AutenticacionControlador.php
├── modelo/
│   └── AutenticacionModelo.php
├── vista/
│   ├── acceso_actualizado.php
│   ├── error.php
│   ├── login.php
│   ├── logout.php
│   ├── recuperar.php
│   └── verificar_codigo.php
└── rutas.php
```

Funciones:

- iniciar sesión;
- cerrar sesión;
- recuperación de acceso;
- envío de código;
- verificación del código;
- actualización de acceso;
- cambio de contraseña.

Las contraseñas se almacenan mediante `password_hash()` y se verifican con `password_verify()`.

---

# 8. Política de contraseñas

La contraseña debe cumplir como mínimo:

- 8 caracteres;
- una letra mayúscula;
- un carácter especial.

La validación se realiza del lado del servidor.

---

# 9. Roles y permisos

Tablas principales relacionadas:

```text
usuario
rol
rol_modulo_permiso
```

Un rol puede:

- estar activo o inactivo;
- ser administrador;
- tener permisos específicos por módulo.

El administrador tiene acceso completo. Los demás roles acceden solamente a los módulos habilitados.

---

# 10. Módulo Inicio y localStorage

Ubicación:

```text
modulos/inicio/
```

Representa el Menú Principal y muestra únicamente los módulos habilitados para el usuario autenticado.

Ruta:

```text
public/index.php?r=inicio
```

En la vista de Inicio se utiliza `localStorage` como **caché visual de sesión**.

La clave utilizada es:

```text
sigenmuni_sesion
```

La caché puede contener datos no sensibles como:

- identificador del usuario;
- nombre de usuario;
- nombre completo;
- rol;
- fecha y hora de actualización.

`localStorage` **no reemplaza la sesión PHP**. La autenticación, los roles y los permisos continúan siendo controlados desde el servidor.

Cuando se vuelve al Login sin una sesión válida, la caché `sigenmuni_sesion` se elimina para evitar mostrar información visual perteneciente a una sesión anterior.

Nunca se almacenan en `localStorage`:

- contraseñas;
- hashes;
- credenciales de base de datos;
- credenciales SMTP;
- permisos sensibles.

---

# 11. Gestión de Empleados

Ubicación:

```text
modulos/empleados/
```

Permite:

- listar empleados;
- buscar por apellido;
- registrar;
- editar;
- consultar;
- activar/inactivar;
- administrar conceptos por empleado.

Datos principales:

- legajo;
- apellido;
- nombre;
- DNI;
- CUIL;
- teléfono;
- email;
- fecha de alta;
- categoría;
- situación;
- unidad de organización.

Se validan, entre otros aspectos:

- email obligatorio;
- DNI único;
- legajo único;
- estado activo/inactivo;
- fecha de baja cuando corresponde.

---

# 12. Gestión de Conceptos

Ubicación:

```text
modulos/conceptos/
```

Permite administrar conceptos de liquidación.

Tipos principales:

```text
REMUNERATIVO
NO_REMUNERATIVO
ASIGNACION
DESCUENTO
APORTE_PATRONAL
```

Entre sus datos se encuentran:

- código;
- nombre;
- categoría;
- forma de cálculo;
- porcentaje;
- monto;
- base de cálculo;
- estado;
- valores por categoría.

También se administran valores asociados a conceptos y conceptos particulares asignados a empleados.

---

# 13. Gestión de Categorías

Ubicación:

```text
modulos/categorias/
```

Permite:

- listar;
- crear;
- editar;
- activar/inactivar;
- consultar valores salariales asociados.

---

# 14. Liquidación

Ubicación:

```text
modulos/liquidacion/
```

Incluye servicios especializados:

```text
servicio/
├── CalculadoraLiquidacion.php
└── GeneradorReciboPDF.php
```

`CalculadoraLiquidacion.php` centraliza reglas de cálculo.

`GeneradorReciboPDF.php` genera recibos utilizando FPDF.

Los PDF temporales utilizados para envío por correo se crean en:

```text
storage/temp_recibos/
```

y se eliminan luego de su utilización.

Estados principales de una liquidación:

```text
BORRADOR
CERRADA
ANULADA
```

---

# 15. Reglas principales de liquidación

## Remunerativos

```text
101 Básico
102 Dedicación Funcional
103 Jerárquica
104 Suplemento Especial
108 Antigüedad
109 Presentismo
110 Título
111 Otros remunerativos
```

## No remunerativos

```text
112 No remunerativo
```

## Asignaciones

```text
201 Hijo
202 Hijo con discapacidad
203 Prenatal
204 Ayuda escolar
205 Ayuda escolar discapacidad
206 Nacimiento
207 Adopción
208 Matrimonio
209 Otra asignación
```

## Descuentos

```text
301 Caja Previsión
302 IASEP
303 Sepelio
304 IASEP Voluntario
306 IPS
307 IPS
```

## Aportes patronales

```text
401
402
403
```

Cálculo conceptual del Neto:

```text
Neto =
Total remunerativo
- Total descuentos
+ Total no remunerativo
+ Total asignaciones
```

Los aportes patronales no afectan el Neto a Cobrar.

---

# 16. Reportes

Ubicación:

```text
modulos/reportes/
```

Incluye:

- empleados;
- historial por empleado;
- conceptos;
- categorías;
- liquidaciones;
- estadísticas;
- auditoría.

Según el reporte puede incluir:

- filtros;
- impresión;
- exportación PDF;
- exportación Excel.

La Auditoría actualmente permite exportación PDF. Los reportes que disponen de Excel utilizan su mecanismo específico de exportación.

---

# 17. Librerías y dependencias externas

## FPDF

Ubicación:

```text
lib/fpdf/
```

Usos principales:

- recibos;
- estadísticas;
- auditoría;
- reportes PDF.

## PHPMailer

Ubicación:

```text
lib/phpmailer/
```

Usos principales:

- recuperación de acceso;
- envío de códigos;
- envío de recibos por correo.

## Composer

Composer se utiliza como gestor de dependencias PHP.

Archivos versionados:

```text
composer.json
composer.lock
```

Dependencias instaladas:

```text
vendor/
```

La carpeta `vendor/` no se versiona. Para reconstruirla se ejecuta:

```powershell
composer install
```

## vlucas/phpdotenv

`vlucas/phpdotenv` permite cargar variables de entorno desde el archivo `.env`.

Fue incorporado mediante Composer:

```powershell
composer require vlucas/phpdotenv
```

La aplicación lo carga desde:

```text
vendor/autoload.php
```

y su inicialización se realiza en:

```text
core/bootstrap.php
```

---

# 18. Configuración y variables de entorno

La configuración sensible ya no se escribe directamente en los archivos PHP.

SIGENMUNI utiliza:

```text
.env
.env.example
```

## `.env`

Contiene la configuración real de cada instalación.

Ejemplo de estructura:

```env
APP_NAME=SIGENMUNI
APP_ENV=development
APP_DEBUG=true
APP_TIMEZONE=America/Argentina/Buenos_Aires

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sigenmuni4
DB_USERNAME=root
DB_PASSWORD=

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Municipalidad de Fortín Lugones"
```

El archivo `.env` **no debe subirse a GitHub**.

## `.env.example`

Es la plantilla que se incluye en el repositorio.

Contiene los nombres de las variables necesarias pero no debe contener contraseñas ni credenciales reales.

Para una instalación nueva se puede crear `.env` a partir de esta plantilla.

En PowerShell:

```powershell
Copy-Item .env.example .env
```

## Variables generales

```text
APP_NAME
APP_ENV
APP_DEBUG
APP_TIMEZONE
```

## Variables de base de datos

```text
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

## Variables de correo

```text
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_FROM_ADDRESS
MAIL_FROM_NAME
```

## Base de datos

Archivo:

```text
config/conexion.php
```

`config/conexion.php` ya no contiene las credenciales reales. Obtiene los valores mediante:

```php
$_ENV['DB_HOST']
$_ENV['DB_PORT']
$_ENV['DB_DATABASE']
$_ENV['DB_USERNAME']
$_ENV['DB_PASSWORD']
```

El flujo es:

```text
.env
   ↓
vlucas/phpdotenv
   ↓
core/bootstrap.php
   ↓
$_ENV
   ↓
config/conexion.php
   ↓
MySQL
```

## Correo SMTP

Archivo:

```text
config/config_correo.php
```

La configuración real también se obtiene desde `.env`.

Para mantener compatibilidad con las partes existentes de SIGENMUNI, `config_correo.php` crea las constantes:

```text
SMTP_HOST
SMTP_PORT
SMTP_USER
SMTP_PASS
SMTP_FROM
SMTP_FROM_NAME
```

a partir de las variables `MAIL_*`.

Flujo:

```text
.env
   ↓
vlucas/phpdotenv
   ↓
core/bootstrap.php
   ↓
config/config_correo.php
   ↓
SMTP_*
   ↓
PHPMailer
   ↓
Servidor SMTP
```

El funcionamiento SMTP fue comprobado mediante un envío real de recibo de sueldo.

---

# 19. Git y archivos sensibles

El archivo `.gitignore` evita subir información sensible, dependencias generadas y archivos temporales.

Configuración principal:

```gitignore
/storage/temp_recibos/*
!/storage/temp_recibos/.gitkeep

.DS_Store
Thumbs.db

.vscode/
.idea/

*.log
*.tmp
*.temp

/vendor/

.env
.env.*
!.env.example
```

Por lo tanto:

```text
.env                       → NO se versiona
vendor/                    → NO se versiona

.env.example               → SÍ se versiona
composer.json              → SÍ se versiona
composer.lock              → SÍ se versiona
config/conexion.php        → SÍ se versiona
config/config_correo.php   → SÍ se versiona
```

`config/conexion.php` y `config/config_correo.php` pueden versionarse porque ya no almacenan las credenciales directamente.

El archivo:

```text
storage/temp_recibos/.gitkeep
```

permite conservar la carpeta temporal aunque esté vacía.

---

# 20. Instalación local

## Requisitos

- Apache.
- PHP 8.x.
- MySQL o MariaDB.
- extensión `mysqli`.
- extensión `zip` habilitada para Composer o una herramienta compatible como 7-Zip/unzip.
- Composer 2.x.
- XAMPP o entorno equivalente.

En XAMPP sobre Windows se recomienda que:

```text
C:\xampp\php
```

esté agregado al `PATH`.

Comprobaciones:

```powershell
php -v
composer --version
```

## Paso 1. Copiar o clonar el proyecto

Ubicación utilizada en desarrollo:

```text
C:\xampp\htdocs\PROGRAMACIONIII\SIGENMUNI
```

## Paso 2. Instalar dependencias Composer

Desde la raíz del proyecto:

```powershell
composer install
```

Esto reconstruye la carpeta:

```text
vendor/
```

a partir de `composer.json` y `composer.lock`.

## Paso 3. Crear la base de datos

La base de datos actualmente utilizada es:

```text
sigenmuni4
```

El proyecto incluye:

```text
database/sigenmuni.sql
```

para disponer de la estructura SQL correspondiente.

## Paso 4. Crear el archivo `.env`

Copiar:

```text
.env.example
```

como:

```text
.env
```

En PowerShell:

```powershell
Copy-Item .env.example .env
```

## Paso 5. Configurar la base de datos

Dentro de `.env` completar:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=sigenmuni4
DB_USERNAME=root
DB_PASSWORD=
```

Los valores deben adaptarse a la instalación local.

## Paso 6. Configurar el correo

Dentro de `.env` completar:

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=correo@ejemplo.com
MAIL_PASSWORD="CONTRASEÑA_DE_APLICACION"
MAIL_FROM_ADDRESS=correo@ejemplo.com
MAIL_FROM_NAME="Municipalidad de Fortín Lugones"
```

Para Gmail se recomienda utilizar una **contraseña de aplicación**, no la contraseña normal de la cuenta.

## Paso 7. Iniciar servicios

Desde XAMPP iniciar:

```text
Apache
MySQL
```

## Paso 8. Abrir el sistema

```text
http://localhost/PROGRAMACIONIII/SIGENMUNI/
```

El sistema redirige al Login administrado por el Router.

---

# 21. Flujo de una solicitud

Ejemplo para Empleados:

```text
Usuario
   ↓
public/index.php?r=empleados
   ↓
core/bootstrap.php
   ↓
Router.php
   ↓
modulos/empleados/rutas.php
   ↓
EmpleadoControlador.php
   ↓
EmpleadoModelo.php
   ↓
empleados.php
```

Antes de ejecutar una ruta protegida se verifica:

- sesión;
- rol;
- estado del rol;
- permiso.

---

# 22. Seguridad aplicada

El sistema implementa:

- sesiones PHP;
- regeneración del ID de sesión tras login;
- `password_hash()`;
- `password_verify()`;
- consultas preparadas;
- validaciones del servidor;
- protección CSRF;
- roles;
- permisos;
- `htmlspecialchars()`;
- validación de parámetros;
- separación de archivos sensibles;
- variables de entorno;
- `.env` excluido del repositorio;
- `.gitignore`;
- validación de configuración al iniciar;
- mensajes de conexión que evitan exponer detalles sensibles;
- limpieza de archivos temporales;
- caché visual en `localStorage` sin almacenar secretos.

Las credenciales reales de MySQL y SMTP se almacenan en `.env` y no en el código fuente.

---

# 23. Auditoría

La auditoría se centraliza en:

```text
core/Auditoria.php
```

Permite registrar acciones relevantes y consultarlas posteriormente desde los reportes.

Flujo conceptual:

```text
Modelo guarda una operación
   ↓
Controlador obtiene la información necesaria
   ↓
Auditoria::registrar(...)
   ↓
Registro en la base de datos
```

---

# 24. Estado actual de la migración

La migración estructural principal está completada.

Se migraron al Router:

- Inicio.
- Autenticación.
- Empleados.
- Conceptos por Empleado.
- Conceptos.
- Categorías.
- Liquidación.
- Reportes.
- Usuarios.
- Roles.
- Permisos.
- Ayuda.

También se reorganizaron:

```text
conexion.php
→ config/conexion.php

config_correo.php
→ config/config_correo.php

seguridad.php
→ core/seguridad.php

fpdf/
→ lib/fpdf/

phpmailer/
→ lib/phpmailer/

img/
→ public/assets/img/

temp_recibos/
→ storage/temp_recibos/
```

El nombre actual de la carpeta del proyecto es:

```text
SIGENMUNI
```

La base de datos conserva actualmente el nombre:

```text
sigenmuni4
```

También se incorporó:

```text
Composer
vlucas/phpdotenv
.env
.env.example
composer.json
composer.lock
```

Con este cambio:

```text
credenciales en PHP
        ↓
variables de entorno
        ↓
.env
```

`config/conexion.php` y `config/config_correo.php` dejaron de contener credenciales reales y pueden mantenerse versionados.

---

# 25. Ventajas de la estructura actual

La arquitectura actual permite:

- centralizar las solicitudes;
- centralizar seguridad;
- separar presentación y lógica;
- organizar funcionalidades por módulo;
- reducir código duplicado;
- proteger configuraciones sensibles;
- separar credenciales del código;
- gestionar dependencias con Composer;
- validar la configuración al iniciar la aplicación;
- separar librerías externas;
- separar almacenamiento temporal;
- facilitar mantenimiento;
- facilitar futuras ampliaciones;
- facilitar el despliegue en distintos entornos.

---

# 26. Recomendaciones para nuevas funcionalidades

Al agregar una funcionalidad se recomienda:

1. ubicarla en el módulo correspondiente;
2. registrar su ruta en `rutas.php`;
3. procesar la solicitud en el controlador;
4. realizar consultas SQL en el modelo;
5. utilizar servicios para lógica especializada;
6. mantener la vista enfocada en presentación;
7. usar POST para modificaciones;
8. usar CSRF en operaciones sensibles;
9. utilizar consultas preparadas;
10. generar URLs mediante `core/Url.php`;
11. no colocar credenciales directamente en el código;
12. agregar nuevas configuraciones sensibles a `.env`;
13. documentar las nuevas variables en `.env.example`;
14. instalar nuevas dependencias mediante Composer cuando corresponda.

---

# 27. Resumen para exposición

Una explicación breve del proyecto puede ser:

> SIGENMUNI utiliza una arquitectura MVC organizada mediante Vertical Slice. Todas las solicitudes de los módulos ingresan por un Front Controller ubicado en `public/index.php`. Antes de registrar y ejecutar las rutas, `core/bootstrap.php` carga las dependencias mediante Composer, inicializa `vlucas/phpdotenv`, lee y valida las variables de entorno, carga la conexión y crea el Router. El Router determina qué módulo y controlador debe ejecutarse y, antes de permitir el acceso, verifica la sesión, el rol y los permisos del usuario. Cada módulo contiene sus propias rutas, controlador, modelo y vistas. La configuración se encuentra en `config`, la infraestructura compartida en `core`, las librerías externas en `lib`, los recursos públicos en `public` y los archivos temporales en `storage`. Las credenciales reales de la base de datos y del correo SMTP se encuentran en `.env`, archivo excluido de Git mediante `.gitignore`.

Para explicar específicamente las variables de entorno:

> SIGENMUNI utiliza Composer y la librería `vlucas/phpdotenv` para separar las credenciales y configuraciones sensibles del código fuente. `core/bootstrap.php` carga `vendor/autoload.php`, lee el archivo `.env` y valida las variables obligatorias. `config/conexion.php` obtiene desde allí los datos de MySQL y `config/config_correo.php` obtiene la configuración SMTP utilizada por PHPMailer. El archivo `.env` no se sube al repositorio, mientras que `.env.example` documenta las variables necesarias para instalar el sistema.

---

# 28. Contexto académico

Proyecto desarrollado como sistema de gestión y liquidación municipal en el marco de actividades académicas de programación.

```text
SIGENMUNI
Sistema de Gestión Municipal
Municipalidad de Fortín Lugones
```

La implementación busca aplicar de manera práctica conceptos de:

- arquitectura de software;
- MVC;
- Front Controller;
- Router;
- Vertical Slice;
- seguridad web;
- sesiones;
- control de acceso;
- auditoría;
- versionado con Git;
- administración de dependencias;
- variables de entorno;
- configuración segura.

---

# 29. Recomendaciones para producción

Antes de utilizar el sistema en producción se recomienda:

- utilizar HTTPS;
- configurar `APP_ENV=production`;
- configurar `APP_DEBUG=false`;
- definir las variables sensibles directamente en el entorno del servidor cuando sea posible;
- nunca publicar `.env`;
- utilizar credenciales independientes para producción;
- utilizar contraseñas robustas;
- limitar permisos del usuario de base de datos;
- realizar auditoría de seguridad;
- realizar copias de seguridad;
- probar restauración de backups;
- realizar pruebas integrales;
- configurar PHP de forma segura;
- utilizar permisos adecuados del sistema operativo;
- revisar logs;
- actualizar periódicamente dependencias;
- ejecutar `composer install` con la configuración apropiada para producción;
- proteger el acceso al servidor y a la base de datos.

---

# 30. Comandos útiles

## Composer

Verificar instalación:

```powershell
composer --version
```

Instalar dependencias:

```powershell
composer install
```

Consultar `phpdotenv`:

```powershell
composer show vlucas/phpdotenv
```

## PHP

Verificar PHP:

```powershell
php -v
```

Validar sintaxis del bootstrap:

```powershell
php -l .\core\bootstrap.php
```

## Variables de entorno

Comprobar que `.env` está ignorado:

```powershell
git check-ignore -v .env
```

Comprobar que `.env.example` queda disponible para versionar:

```powershell
git check-ignore -v .env.example
```

## Git

Consultar cambios:

```powershell
git status --short
```

Preparar cambios:

```powershell
git add -A
```

Crear commit:

```powershell
git commit -m "Descripción del cambio"
```

Subir cambios:

```powershell
git push origin main
```
