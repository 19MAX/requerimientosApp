# Guia de Implementacion - RequerimientosApp

## Requisitos Previos

- PHP 8.2+ con extensiones: mysqli, mbstring, xml, zip, gd
- Composer 2.x
- MySQL 5.7+ o MariaDB 10.3+
- Servidor web (Apache/Nginx)

---

## 1. Configuracion del Virtual Host

### Apache

```apache
<VirtualHost *:80>
    ServerName requerimientos.test
    DocumentRoot "/var/www/html/CodeIgniter/RequerimientosApp/public"

    <Directory "/var/www/html/CodeIgniter/RequerimientosApp/public">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted

        <FilesMatch \.php$>
            SetHandler "proxy:unix:/var/run/php/php8.2-fpm.sock|fcgi://localhost"
        </FilesMatch>
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/requerimientos_error.log
    CustomLog ${APACHE_LOG_DIR}/requerimientos_access.log combined
</VirtualHost>
```

### Nginx

```nginx
server {
    listen 80;
    server_name requerimientos.test;
    root /var/www/html/CodeIgniter/RequerimientosApp/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

**Importante**: El DocumentRoot/Root DEBE apuntar a `RequerimientosApp/public`, NO a la raiz del proyecto. Dentro de `public/` esta el `index.php` que es el punto de entrada de la aplicacion.

---

## 2. Instalacion del Proyecto

### Paso 1: Clonar o copiar el proyecto

```bash
cd /var/www/html/CodeIgniter
git clone <repo-url> RequerimientosApp
cd RequerimientosApp
```

### Paso 2: Instalar dependencias Composer

```bash
composer install
```

### Paso 3: Configurar archivo de entorno

Copia el archivo `.env.example` a `.env`:

```bash
cp .env.example .env
```

### Paso 4: Editar .env con tus credenciales

Abre el archivo `.env` y configura segun tu entorno:

```env
# Entorno (development para ver errores, production para produccion)
CI_ENVIRONMENT = development

# URL de tu aplicacion
app.baseURL = 'http://tu-dominio.com'

# ====================
# Base de Datos
# ====================
database.default.hostname = 127.0.0.1
database.default.database = requerimiento_app
database.default.username = tu_usuario_db
database.default.password = tu_password_db
database.default.DBDriver = MySQLi

# ====================
# Email (SMTP Gmail)
# ====================
SMTP_HOST = smtp.gmail.com
SMTP_PORT = 587
SMTP_USER = tu_email@gmail.com
SMTP_PASSWORD = password_de_aplicacion_16_chars
SMTP_NAME = Requerimientos CNEL

# ====================
# Administrador (para el seeder)
# ====================
SEEDER_ADMIN_EMAIL = admin@ejemplo.com
SEEDER_ADMIN_PASSWORD = tu_password_seguro
```

---

## 3. Base de Datos

### Crear la base de datos

Accede a MySQL y ejecuta:

```sql
CREATE DATABASE requerimiento_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### O si prefieres crear un usuario dedicado (opcional)

```sql
CREATE DATABASE requerimiento_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'requerimientos_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON requerimiento_app.* TO 'requerimientos_user'@'localhost';
FLUSH PRIVILEGES;
```

Luego en tu `.env`:

```env
database.default.username = requerimientos_user
database.default.password = tu_password_seguro
```

---

## 4. Ejecutar Migraciones y Seeds

```bash
# Ejecutar todas las migraciones (crea las tablas)
php spark migrate

# Crear el usuario administrador (usa los valores de SEEDER_ADMIN_EMAIL y SEEDER_ADMIN_PASSWORD del .env)
php spark db:seed AdminSeeder

# Crear categorias de lider
php spark db:seed LeaderCategorySeeder
```

---

## 5. Configuracion de Correo Electronico

### Como funcionan los correos

El sistema usa `EmailService.php` para enviar notificaciones automaticas:

```
Secretaria crea documento
        ↓
EmailService::notifyDocumentRegistration()
        ↓
Se envia email de confirmacion al cliente

Director aprueba y asigna
        ↓
EmailService::notifyLeaderAssignment()
        ↓
Se envia email al lider con instrucciones
```

### Metodos disponibles en EmailService

| Metodo | Cuando se usa |
|--------|---------------|
| `notifyDocumentRegistration()` | Cuando secretaria crea un nuevo documento |
| `notifyDocumentStatusChange()` | Cuando cambia el estado del documento |
| `notifyDirectorAssignment()` | Cuando se asigna un documento a un director |
| `notifyLeaderAssignment()` | Cuando se asigna una tarea a un lider de area |

### Credenciales SMTP

Las credenciales se configuran en el archivo `.env`:

```env
SMTP_HOST = smtp.gmail.com
SMTP_PORT = 587
SMTP_USER = tu_correo@gmail.com
SMTP_PASSWORD = password_de_aplicacion_16_chars
SMTP_NAME = Nombre que aparecera como remitente
```

### Configurar Gmail para SMTP

Google requiere una "Password de Aplicacion" (no tu contrasena normal):

1. Ve a https://myaccount.google.com/security
2. Busca "Contraseñas de aplicaciones" (App passwords)
3. Selecciona "Correo" como app y "Otro" como dispositivo
4. Ingresa un nombre (ej: RequerimientosApp)
5. Copia la contrasena de 16 caracteres generada
6. Usa esa contrasena en `SMTP_PASSWORD` del `.env`

**Importante**: El correo de Gmail debe tener verificacion en dos pasos activada.

### Plantillas de email

Las plantillas estan en `app/Views/emails/`:
- `document_registered.php` - Confirmacion al cliente
- `document_status_changed.php` - Cambio de estado
- `director_assignment.php` - Asignacion a director
- `leader_assignment.php` - Asignacion a lider

---

## 6. Verificacion y Logs

### Probar la instalacion

```bash
# Ver rutas disponibles
php spark routes

# Iniciar servidor de desarrollo
php spark serve
```

### Revisar Logs

Si algo no funciona, revisa los logs en `writable/logs/`. Ahi encontraras errores de PHP, consultas SQL y eventos del sistema.

Para ver los logs en tiempo real:

```bash
tail -f writable/logs/*.log
```

---

## 7. Login Inicial

Una vez todo configurado:

- **URL**: `http://tu-dominio.com/login`
- **Email**: El que configuraste en `SEEDER_ADMIN_EMAIL`
- **Password**: El que configuraste en `SEEDER_ADMIN_PASSWORD`

---

## 8. Problemas Comunes

### Error 500 sin mensaje

1. Revisar `writable/logs/` para ver el error exacto
2. Cambiar `CI_ENVIRONMENT = development` en `.env` para ver errores en pantalla

### Errores de permisos (solo si ocurren)

Si aparece error de "Permission Denied" en carpetas writables:

```bash
# Linux (Apache/Nginx como www-data)
sudo chown -R www-data:www-data writable/
sudo chmod -R 775 writable/
```

---

## 9. Implementacion en cPanel

El proceso de desplegar en cPanel es diferente a un servidor VPS. Consulta la guia completa aqui:

**[IMPLEMENTATION_CPANEL.md](./IMPLEMENTATION_CPANEL.md)**

En resumen, el proceso en cPanel es:
1. Comprimir el proyecto desde tu maquina local
2. Subir el ZIP al hosting
3. Exportar la base de datos e importar en el hosting
4. Extraer, configurar `.env` y ajustar `.htaccess` para que apunte a `public/`

---

## 10. Checklist

- [ ] Proyecto copiado al hosting/servidor
- [ ] `.env` configurado con credenciales
- [ ] Base de datos creada
- [ ] Migraciones ejecutadas
- [ ] Seeds ejecutados
- [ ] Credenciales SMTP configuradas
- [ ] Login funciona correctamente
- [ ] Logs sin errores criticos