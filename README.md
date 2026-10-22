UTicket - Sistema de Gestión de Tickets de Soporte Técnico
UTicket (v0.0.1) es una plataforma web desarrollada para la administración, seguimiento y control de tickets de soporte técnico e incidencias informáticas dentro de la institución. Diseñada bajo una arquitectura MVC (Modelo-Vista-Controlador) en PHP nativo, proporciona una interfaz moderna, intuitiva y optimizada para la atención de solicitudes internas.

* Características Principales
Generación Automatizada de Tickets: Asignación de un código único e de auditoría por ticket (ej. UTIC-YYYYMM-XXXX).

Panel de Administración (Dashboard): Vista consolidada para técnicos con filtrado de estados (Nuevos, En Proceso, Resueltos), búsqueda por cédula/solicitante y asignación de personal.

Mapeo Organizacional: Integración relacional entre dependencias, pisos/ubicaciones y categorías de atención (Soporte General, Redes / Internet, Sistemas, etc.).

Rastreo Público de Solicitudes: Módulo de consulta de estado para los funcionarios mediante su cédula o código de ticket.

Personalización Institucional: Interfaz adaptada con identidad visual (logotipo y favicons dinámicos mediante BASE_URL).

* Requisitos del Sistema
Para el despliegue del proyecto en servidores de desarrollo o producción, se requiere el siguiente stack tecnológico:

Servidor Web: Apache 2.4+ con el módulo mod_rewrite habilitado.

PHP: Versión 8.0 o superior (requiere extensiones: pdo, pdo_mysql, mbstring, json, fileinfo).

Base de Datos: MySQL 8.0+ o MariaDB 10.5+ (Cotejamiento utf8mb4_unicode_ci).

Zona Horaria: Configurada a America/Asuncion (UTC-3).

* Instalación y Configuración
Sigue estos pasos para desplegar el proyecto localmente o en el servidor de producción:

1. Clonar el Repositorio
Bash
git clone https://github.com/manufscudero/sistema-ticket.git
cd sistema-ticket
2. Configurar Variables de Entorno (.env)
Por razones de seguridad, el archivo de configuración .env está excluido del control de versiones. Debes crear una copia basada en la plantilla provista:

Bash
cp .env.example .env
Abre el archivo .env recien creado y define las credenciales reales de tu entorno:

Ini, TOML
DB_HOST=localhost
DB_PORT=3306
DB_NAME=nombre_de_tu_bd
DB_USER=usuario_bd
DB_PASS=contraseña_segura
BASE_URL=http://localhost/sistema-tickets-utic/public
3. Configurar el Archivo de Base de Datos (src/config/config.php o database.php)
Si tu entorno utiliza un archivo de configuración de base de datos local (excluido del repositorio por seguridad), asegúrate de instanciar la conexión leyendo las variables del archivo .env o configurando los parámetros de producción correspondientes:

PHP
// Ejemplo de conexión PDO segura leyendo variables de entorno
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/public');
4. Importar la Base de Datos
Importa la estructura inicial de tablas en tu servidor MySQL/MariaDB utilizando el archivo de migración o script SQL base disponible en el proyecto (database.sql o similar).

5. Configuración del Servidor Web (Apache)
Asegúrate de que el DocumentRoot apunte al directorio /public de la aplicación o que el archivo .htaccess en la raíz procese adecuadamente las redirecciones del módulo mod_rewrite.

* Estructura del Proyecto
Plaintext
sistema-ticket/
├── public/                  # Punto de entrada público (DocumentRoot)
│   ├── assets/              # Estilos CSS, scripts JS, imágenes y logos
│   │   ├── css/
│   │   ├── js/
│   │   └── img/             # Logotipos e isotipos (uticket.png)
│   ├── .htaccess            # Reglas de reescritura de URLs
│   └── index.php            # Front Controller del sistema
├── src/                     # Código fuente de la aplicación (MVC)
│   ├── config/              # Archivos de configuración (Excluidos de Git)
│   ├── controllers/         # Controladores (AdminController, TicketController, etc.)
│   ├── models/              # Modelos de datos (Ticket, Usuario, Piso, etc.)
│   └── views/               # Plantillas e interfaces HTML/PHP
│       ├── admin/           # Vistas del panel de administración
│       ├── layouts/         # Componentes reutilizables (header, footer, nav)
│       └── public/          # Formulario público de tickets
├── .env.example             # Plantilla de variables de entorno
├── .gitignore                # Reglas de exclusión para Git
└── README.md                # Documentación del proyecto
* Seguridad y Buenas Prácticas
Credenciales: Nunca incluyas archivos con contraseñas de producción (.env, src/config/config.php o database.php) en los commits de Git.

Modo Producción: En entornos de producción, asegúrate de desactivar la visualización directa de errores (display_errors = Off en php.ini) y canalizarlos a archivos de registro.

HTTPS: Se recomienda la instalación de un certificado SSL (como Let's Encrypt) para garantizar que el intercambio de credenciales e información viaje cifrado.

* Desarrollo y Soporte
Desarrollado para la Unidad de Tecnologías de la Información y Comunicación (UTIC) / DGVS.
