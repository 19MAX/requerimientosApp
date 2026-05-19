# Implementacion en cPanel - RequerimientosApp

cPanel no permite configurar Virtual Hosts directamente como en VPS, asi que se usa una estructura diferente. El truco es colocar el proyecto fuera de la carpeta public_html y usar un `.htaccess` para redirigir las peticiones a `public/`.

---

## Estructura en cPanel

```
/home/usuario/
├── RequerimientosApp/          ← Carpeta del proyecto (fuera de public_html)
│   ├── app/
│   ├── public/                 ← Aqui esta index.php
│   ├── writable/
│   ├── .env
│   └── ...
├── public_html/                ← Carpeta raiz web de cPanel
│   └── (esta vacia o tiene solo el .htaccess de redireccion)
```

El `.htaccess` en `public_html/` redirige todas las peticiones hacia `RequerimientosApp/public/`.

---

## Paso a Paso

### 1. Preparar el proyecto en tu maquina local

Antes de comprimir, ejecuta migraciones y seeds para tener la base de datos completa:

```bash
# Ejecutar migraciones
php spark migrate

# Crear datos iniciales
php spark db:seed AdminSeeder
php spark db:seed LeaderCategorySeeder

# Exportar base de datos (con estructura y datos)
mysqldump -u root -p requerimiento_app > export_db.sql
```

### 2. Comprimir el proyecto

Desde tu maquina local, comprime toda la carpeta del proyecto:

```bash
# En la carpeta padre de RequerimientosApp
zip -r RequerimientosApp.zip RequerimientosApp/
```

**Importante**: No incluyas archivos innecesarios como `.git/`, `node_modules/` (si hubiera), etc.

### 3. Subir al hosting

1. En cPanel, ve a **File Manager**
2. Navega a `/home/usuario/` (tu carpeta home)
3. Sube el archivo `RequerimientosApp.zip`
4. Extrae el archivo (click derecho → Extract)
5. Verifica que la carpeta se llame `RequerimientosApp` y este en `/home/usuario/`

### 4. Crear base de datos en cPanel

1. Ve a **MySQL Databases** en cPanel
2. Crea una base de datos nueva (ej: `usuario_requerimientos`)
3. Crea un usuario con contrasena
4. Asigna todos los privilegios al usuario sobre la base de datos

### 5. Importar la base de datos

1. Ve a **phpMyAdmin** en cPanel
2. Selecciona la base de datos creada
3. Click en **Importar**
4. Sube el archivo `export_db.sql` que exportaste anteriormente
5. Click en **Continuar**

### 6. Configurar el archivo .env

1. En File Manager, ve a `/home/usuario/RequerimientosApp/`
2. Busca el archivo `.env.example`, copialo y renombralo a `.env`
3. Edita el `.env` con las credenciales del hosting:

```env
CI_ENVIRONMENT = production

app.baseURL = 'http://tu-dominio.com'

database.default.hostname = 127.0.0.1
database.default.database = usuario_requerimientos
database.default.username = usuario_db
database.default.password = password_db
database.default.DBDriver = MySQLi

SMTP_HOST = smtp.gmail.com
SMTP_PORT = 587
SMTP_USER = tu_email@gmail.com
SMTP_PASSWORD = password_de_aplicacion_16_chars
SMTP_NAME = Requerimientos CNEL

SEEDER_ADMIN_EMAIL = admin@tu-dominio.com
SEEDER_ADMIN_PASSWORD = tu_password_seguro
```

### 7. Crear el archivo .htaccess de redireccion

Ahora necesitas crear un `.htaccess` en `public_html/` que redirija todas las peticiones hacia la carpeta `RequerimientosApp/public/`.

1. Ve a `/home/usuario/public_html/`
2. Crea un archivo llamado `.htaccess` (si no existe)
3. Agrega el siguiente contenido:

```apache
RewriteEngine On

# Redirigir todas las peticiones a la carpeta public del proyecto
RewriteRule ^(.*)$ /RequerimientosApp/public/$1 [L]
```

**Nota**: Si ya tienes un archivo `.htaccess` en `public_html/`, haz un backup primero y luego agrega solo las lineas de RewriteEngine y RewriteRule al principio.

### 8. Verificar estructura

La estructura final debe ser:

```
/home/usuario/
├── RequerimientosApp/
│   ├── app/
│   ├── public/          ← Aqui esta el index.php real
│   ├── writable/         ← Carpeta de logs, cache, sesiones
│   ├── .env
│   └── ...
└── public_html/
    └── .htaccess         ← Este redirige a RequerimientosApp/public/
```

### 9. Configurar permisos (solo si hay errores)

Si al acceder aparece error de permisos en `writable/`:

1. Ve a `/home/usuario/RequerimientosApp/writable/`
2. Click derecho → **Change Permissions**
3. Establece en **755** o **775**

Lo mismo si tienes carpeta `public/uploads/`:

```
/home/usuario/RequerimientosApp/public/uploads/
```

### 10. Verificar funcionamiento

Accede a tu dominio:
- `http://tu-dominio.com/login`
- Deberias ver la pagina de login

Si hay errores:
1. Revisa los logs en `/home/usuario/RequerimientosApp/writable/logs/`
2. Cambia temporalmente `CI_ENVIRONMENT = development` en `.env` para ver errores en pantalla

---

## Solucion de Problemas

### Error 404 en todas las paginas

El `.htaccess` no esta redireccionando correctamente. Verifica que:
1. El archivo `.htaccess` este en `/home/usuario/public_html/` (no en `RequerimientosApp/`)
2. El contenido tenga las reglas de Rewrite correctamente
3. mod_rewrite este habilitado en Apache de cPanel

### Error 500

1. Revisar `writable/logs/` para ver el error especifico
2. Verificar que el `.env` tenga la syntaxis correcta (sin espacios extra, etc)
3. Cambiar temporalmente a development para ver errores

### Pagina en blanco

1. Aumentar PHP memory limit desde cPanel
2. Revisar logs en `writable/logs/`

### No llegan los correos

1. Verificar que `SMTP_PASSWORD` sea una password de aplicacion (16 chars)
2. Verificar que el correo de Gmail tenga 2FA activado
3. Revisar la carpeta de logs para errores de envio

---

## Checklist cPanel

- [ ] Proyecto comprimido y subido
- [ ] Base de datos importada
- [ ] Archivo `.env` configurado con datos del hosting
- [ ] `.htaccess` creado en `public_html/` apuntando a `RequerimientosApp/public/`
- [ ] Permisos de `writable/` correctos (si hay errores)
- [ ] Login funciona correctamente
- [ ] Correos se envian correctamente