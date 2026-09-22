# SIGENMUNI4

## Sistema de Gestión Municipal y Liquidación de Sueldos

**SIGENMUNI4** es un sistema web desarrollado en **PHP + MySQL** para la gestión administrativa y la liquidación de sueldos de una municipalidad.

El proyecto fue desarrollado para la **Municipalidad de Fortín Lugones** y se encuentra organizado con una arquitectura basada en:

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

La aplicación permite administrar la información necesaria para calcular y consultar liquidaciones municipales, manteniendo además controles de seguridad y trazabilidad.

---

# 2. Tecnologías utilizadas

El proyecto utiliza principalmente:

- PHP.
- MySQL.
- HTML5.
- CSS3.
- JavaScript.
- mysqli.
- FPDF.
- PHPMailer.

Entorno de desarrollo:

- XAMPP.
- Apache.
- MySQL / MariaDB.
- Visual Studio Code.

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

El archivo `index.php` ubicado en la raíz del proyecto funciona solamente como punto de entrada amigable y redirige al Login administrado por el Router:

```text
SIGENMUNI4/
   ↓
index.php
   ↓
public/index.php?r=login
```

---

# 4. Estructura de carpetas

```text
SIGENMUNI4/
│
├── componentes/
│   └── header_sigenmuni.php
│
├── config/
│   ├── app.php
│   ├── conexion.php
│   ├── conexion.example.php
│   ├── config_correo.php
│   ├── config_correo.example.php
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
├── .gitignore
└── index.php
```

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
- mayor facilidad para incorporar nuevas funcionalidades.

---

# 6. Core del sistema

## Router.php

Registra rutas explícitas y las despacha según:

- método HTTP;
- nombre de ruta;
- tipo de acceso;
- permiso requerido.

Tipos de rutas:

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

Requiere solamente un usuario autenticado.

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

Inicializa la aplicación:

- carga utilidades de URL;
- carga seguridad;
- inicia sesión;
- carga la conexión;
- crea el Router;
- carga módulos registrados;
- registra rutas.

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
- cambio de usuario;
- cambio de contraseña.

Las contraseñas se guardan mediante `password_hash()` y se verifican con `password_verify()`.

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

# 10. Módulo Inicio

Ubicación:

```text
modulos/inicio/
```

Representa el Menú Principal y muestra únicamente los módulos habilitados para el usuario autenticado.

Ruta:

```text
public/index.php?r=inicio
```

---

# 11. Gestión de Empleados

Ubicación:

```text
modulos/empleados/
```

Permite:

- listar empleados;
- buscar;
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

Puede incluir:

- filtros;
- impresión;
- exportación PDF;
- exportación Excel.

---

# 17. Librerías externas

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

---

# 18. Configuración

## Base de datos

Archivo real:

```text
config/conexion.php
```

Archivo de ejemplo:

```text
config/conexion.example.php
```

Para instalar el sistema:

1. copiar `conexion.example.php` como `conexion.php`;
2. completar host, usuario, contraseña y nombre de base.

## Correo

Archivo real:

```text
config/config_correo.php
```

Archivo de ejemplo:

```text
config/config_correo.example.php
```

Configura:

```text
SMTP_HOST
SMTP_PORT
SMTP_USER
SMTP_PASS
SMTP_FROM
SMTP_FROM_NAME
```

---

# 19. Git y archivos sensibles

El archivo `.gitignore` evita subir información sensible o temporal.

Entre otros, se ignoran:

```text
/config/conexion.php
/config/config_correo.php
/storage/temp_recibos/*
*.log
*.tmp
*.temp
```

El archivo:

```text
storage/temp_recibos/.gitkeep
```

permite conservar la carpeta aunque esté vacía.

---

# 20. Instalación local

## Requisitos

- Apache.
- PHP 8.x.
- MySQL o MariaDB.
- extensión mysqli.
- XAMPP o entorno equivalente.

## Paso 1. Copiar el proyecto

Ejemplo:

```text
C:\xampp\htdocs\PROGRAMACIONIII\SIGENMUNI4
```

## Paso 2. Crear la base de datos

Crear:

```text
sigenmuni4
```

e importar la estructura y los datos correspondientes.

## Paso 3. Configurar conexión

Copiar:

```text
config/conexion.example.php
```

como:

```text
config/conexion.php
```

y completar los datos reales.

## Paso 4. Configurar correo

Copiar:

```text
config/config_correo.example.php
```

como:

```text
config/config_correo.php
```

y completar la configuración SMTP.

## Paso 5. Iniciar servicios

Desde XAMPP iniciar:

```text
Apache
MySQL
```

## Paso 6. Abrir el sistema

```text
http://localhost/PROGRAMACIONIII/SIGENMUNI4/
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
- `.gitignore`;
- limpieza de archivos temporales.

---

# 23. Auditoría

La auditoría se centraliza en:

```text
core/Auditoria.php
```

Permite registrar acciones relevantes y consultarlas posteriormente desde los reportes.

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

---

# 25. Ventajas de la estructura actual

La arquitectura actual permite:

- centralizar las solicitudes;
- centralizar seguridad;
- separar presentación y lógica;
- organizar funcionalidades por módulo;
- reducir código duplicado;
- proteger configuraciones sensibles;
- separar librerías externas;
- separar almacenamiento temporal;
- facilitar mantenimiento;
- facilitar futuras ampliaciones.

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
10. generar URLs mediante `core/Url.php`.

---

# 27. Resumen para exposición

Una explicación breve del proyecto puede ser:

> SIGENMUNI utiliza una arquitectura MVC organizada mediante Vertical Slice. Todas las solicitudes de los módulos ingresan por un Front Controller ubicado en `public/index.php`. El Router determina qué módulo y controlador debe ejecutarse y, antes de permitir el acceso, verifica la sesión, el rol y los permisos del usuario. Cada módulo contiene sus propias rutas, controlador, modelo y vistas. La configuración se encuentra en `config`, la infraestructura compartida en `core`, las librerías externas en `lib`, los recursos públicos en `public` y los archivos temporales en `storage`. De esta manera el sistema queda modular, mantenible y con una separación clara de responsabilidades.

---

# 28. Contexto académico

Proyecto desarrollado como sistema de gestión y liquidación municipal en el marco de actividades académicas de programación.

```text
SIGENMUNI
Sistema de Gestión Municipal
Municipalidad de Fortín Lugones
```

---

# 29. Recomendaciones para producción

Antes de utilizar el sistema en producción se recomienda:

- HTTPS;
- manejo seguro de secretos;
- auditoría de seguridad;
- copias de seguridad;
- pruebas integrales;
- configuración segura de PHP;
- permisos adecuados del sistema operativo;
- revisión de logs;
- actualización periódica de dependencias.
