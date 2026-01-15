# 📚 BookTracker

Un gestor personal de libros que te permite registrarte, iniciar sesión y administrar tu colección de libros de forma sencilla y eficiente.

## 🌟 Características

- ✅ **Autenticación de usuarios**: Registro e inicio de sesión seguro
- 📖 **Gestión de libros**: Crear, ver, actualizar y eliminar libros
- 📋 **Estados de lectura**: Marca tus libros como Pendiente, Leyendo o Finalizado
- 📅 **Fechas de vencimiento**: Establecer fechas límite para tus lecturas
- 🔒 **Datos seguros**: Contraseñas encriptadas en la base de datos
- 🎨 **Interfaz intuitiva**: Diseño limpio y responsive

## 📋 Requisitos

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Navegador web** moderno
- Servidor web (Apache, Nginx, etc.)

## ⚙️ Instalación

### 1. Clonar o descargar el proyecto

```bash
git clone https://github.com/hugocortesrosado/BookTracker.git
cd BookTracker
```

### 2. Configurar la base de datos

Importa el esquema SQL en tu MySQL:

```bash
mysql -u root -p < sql/squema.sql
```

O manualmente:
- Accede a phpMyAdmin o a tu cliente MySQL
- Ejecuta el contenido del archivo `sql/squema.sql`

### 3. Crear archivo `.env`

En la raíz del proyecto, crea un archivo `.env` con tus credenciales:

```
DB_HOST=localhost
DB_NAME=booktracker_db
DB_USER=booktracker_admin
DB_PASS=BookTracker@2024
```

### 4. Configurar el servidor web

Asegúrate de que el directorio `public/` sea la raíz del servidor web.

**Para Apache**, añade en `.htaccess`:
```apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php?$1 [L,QSA]
```

**Para PHP Built-in Server** (desarrollo):
```bash
cd public
php -S localhost:8000
```

## 📁 Estructura del Proyecto

```
BookTracker/
├── api/                   # Endpoints de la API
│   ├── config.php         # Configuración y conexión a BD
│   ├── login.php          # Autenticación de login
│   ├── logout.php         # Cerrar sesión
│   ├── register.php       # Registro de nuevos usuarios
│   └── books.php          # Gestión de libros
├── public/                # Archivos públicos (frontend)
│   ├── index.php          # Página principal
│   ├── styles.css         # Estilos CSS
│   └── main.js            # Lógica del cliente
├── sql/
│   └── squema.sql         # Esquema de base de datos
├── .env                   # Variables de entorno (crear)
└── README.md              # Este archivo
```

## 🚀 Uso

1. **Accede a la aplicación** en tu navegador (`http://localhost:8000`)

2. **Registrate** con tu correo electrónico y contraseña

3. **Inicia sesión** con tus credenciales

4. **Gestiona tus libros**:
   - ➕ Agregar nuevos libros
   - 📖 Ver tu lista de libros
   - ✏️ Actualizar información
   - 🗑️ Eliminar libros
   - 🔄 Cambiar estado de lectura

## 🗄️ Base de Datos

### Tabla: `users`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único (PK) |
| name | VARCHAR(255) | Nombre del usuario |
| email | VARCHAR(255) | Email único |
| password | VARCHAR(255) | Contraseña encriptada |
| created_at | TIMESTAMP | Fecha de creación |

### Tabla: `books`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | ID único (PK) |
| user_id | INT | ID del usuario (FK) |
| title | VARCHAR(255) | Título del libro |
| author | VARCHAR(255) | Autor del libro |
| due_date | DATE | Fecha de vencimiento |
| status | ENUM | Pendiente/Leyendo/Finalizado |
| created_at | TIMESTAMP | Fecha de creación |

## 🔐 Seguridad

- Las contraseñas se encriptan usando `password_hash()` de PHP
- Se utiliza PDO con prepared statements para prevenir inyección SQL
- Validación de entrada en cliente y servidor
- Los tokens de sesión se manejan con `$_SESSION`

## 📡 API Endpoints

### Autenticación
- `POST /api/register.php` - Registrar nuevo usuario
- `POST /api/login.php` - Iniciar sesión
- `POST /api/logout.php` - Cerrar sesión

### Libros
- `GET /api/books.php` - Obtener libros del usuario
- `POST /api/books.php` - Crear nuevo libro
- `PUT /api/books.php` - Actualizar libro
- `DELETE /api/books.php` - Eliminar libro

## 🛠️ Desarrollo

### Variables útiles para depuración

El archivo `api/config.php` incluye:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

Desactívalas en producción por seguridad.

### Ejemplo de uso de la API

```javascript
// Registrar usuario
fetch('/api/register.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        name: 'Juan',
        email: 'juan@example.com',
        password: 'segura123'
    })
})
.then(res => res.json())
.then(data => console.log(data));
```

## 📝 Licencia

Este proyecto está bajo licencia MIT. Siéntete libre de usarlo y modificarlo.

## 👤 Autor

Creado por Hugo Cortés Rosado

## 💬 Soporte

Para reportar bugs o sugerir mejoras, abre un issue en el repositorio.

---

**¡Feliz lectura! 📚**