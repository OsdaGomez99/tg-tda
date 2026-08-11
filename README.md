# TG-TDA · Sistema de encuestas para detección de TDA

Aplicación web para la detección y orientación integral de Trastorno de Deficit de Atención
(TDA) en los estudiantes de la Universidad Nacional Experimental de Guayana

Permite administrar carreras, semestres, estudiantes encuestados, usuarios y roles, construir encuestas y preguntas,
recolectar respuestas (mediante un enlace público con código de acceso), y generar automáticamente
un análisis de resultados (puntuaciones de inatención e hiperactividad) con estadísticas y
exportación a PDF.

El proyecto está configurado para ejecutarse íntegramente con Docker y Docker Compose. Incluye los
siguientes servicios:

- Laravel (PHP 8.2)
- MySQL 8.4
- phpMyAdmin

El entorno permite levantar todo el proyecto sin instalar PHP, Node o MySQL en el sistema.

---

# Requisitos

Antes de comenzar debes tener instalado:

Linux
- Docker
- Docker Compose

Windows
- Docker Desktop
- WSL2 habilitado

Verificar instalación:

```
docker -v
docker compose version
```

---

# Instalación del proyecto

## 1. Clonar el repositorio

```
git clone https://github.com/OsdaGomez99/tg-tda.git

cd tg-tda
```

---

## 2. Configurar variables de entorno

Crear el archivo `.env` a partir del ejemplo:

```
cp .env.example .env
```

`.env.example` trae la configuración genérica de Laravel (SQLite, host `127.0.0.1`, etc.), que
**no** corresponde a los servicios de Docker de este proyecto. Edita `.env` y ajusta al menos lo
siguiente para que la aplicación se conecte correctamente a los contenedores:

```
DB_CONNECTION=mysql
DB_HOST=db_app
DB_PORT=3306
DB_DATABASE=tg_tda
DB_USERNAME=sail
DB_PASSWORD=password

WWWUSER=1000
WWWGROUP=1000
```

`WWWUSER`/`WWWGROUP` deben coincidir con tu usuario del sistema (`id -u` / `id -g` en Linux) para
evitar problemas de permisos entre el contenedor y los archivos del proyecto.

Si hay conflictos de puertos en tu máquina puedes sobreescribir los que usa Docker Compose (los
valores por defecto son los que están entre paréntesis):

```
APP_PORT=8080        # por defecto 80
FORWARD_DB_PORT=3308  # por defecto 3307
VITE_PORT=5174        # por defecto 5173
```

---

## 3. Levantar los contenedores

```
docker compose up -d --build
```

Esto iniciará todos los servicios del proyecto.

---

## 4. Entrar al contenedor de la aplicación

```
docker exec -it tg-tda-app bash
```

Los siguientes pasos (5 al 10) se ejecutan **dentro** del contenedor.

---

## 5. Instalar dependencias de PHP

```
composer install
```

---

## 6. Instalar dependencias de frontend

```
npm install
```

---

## 7. Compilar los assets

```
npm run build
```

Esto generará:

```
public/build/manifest.json
```

archivo necesario para que Laravel cargue los assets.

> Durante el desarrollo, en vez de `npm run build` puedes usar `npm run dev` para levantar Vite en
> modo watch con hot-reload (usa el puerto `VITE_PORT`, expuesto en el `compose.yaml`).

---

## 8. Generar la key de Laravel

```
php artisan key:generate
```

---

## 9. Generar el secreto de JWT

La autenticación de la API usa `tymon/jwt-auth`, que requiere su propio secreto:

```
php artisan jwt:secret
```

---

## 10. Ejecutar migraciones y seeders

```
php artisan migrate --seed
```

Los seeders crean los roles y permisos base, un usuario administrador, carreras, semestres,
preguntas y encuestas de ejemplo. El usuario administrador queda disponible con:

```
Email:    admin@correo.com
Password: 123456789
```

---

## 11. Limpiar cache

```
php artisan optimize:clear
```

---

# Acceso al proyecto

Aplicación Laravel

```
http://localhost
```

phpMyAdmin

```
http://localhost:82
```

---

# Flujo de funcionamiento de proyecto

Al levantar el proyecto, los seeders dejan preparada una encuesta de ejemplo con 18 preguntas
(orientadas a evaluar inatención e hiperactividad).

1. **Semestres**: existe un módulo donde se crean los semestres de forma manual. Solo puede
   haber un semestre activo a la vez, y es contra ese semestre activo que se registran las
   respuestas de los estudiantes.

2. **Encuestas**: en el listado de encuestas cada registro tiene asociado un enlace público
   (con su propio código de acceso). Ese enlace es el que se comparte con los estudiantes para
   que respondan la encuesta durante el semestre activo.

3. **Respuesta del estudiante**: al completar la encuesta desde el enlace público, el estudiante
   puede ver de inmediato sus resultados, junto con recomendaciones asociadas al análisis
   obtenido, y descargar un PDF con dicho resultado.

4. **Gestión de resultados**: dentro de la aplicación, los usuarios administradores/gestores
   pueden ver el listado consolidado de todos los resultados de los estudiantes por encuesta,
   junto con sus estadísticas, y también generar un PDF con ese consolidado.

5. **Gestión de estudiantes encuestados**: los usuarios podran consultar en un listado, todos
    los estudiantes que fueron encuestados a lo largo de los diferentes semestres y monitorear
    sus resultados a través de una grafica individual 

---


# Comandos útiles

Levantar contenedores

```
docker compose up -d
```

Apagar contenedores

```
docker compose down
```

Entrar al contenedor de Laravel

```
docker exec -it tg-tda-app-1 bash
```

Reconstruir contenedores

```
docker compose up -d --build
```

Eliminar contenedores y volúmenes

```
docker compose down -v
```

---

# Estructura del entorno Docker

Servicios incluidos:

- app → Laravel
- db_app → MySQL
- phpmyadmin → interfaz para base de datos

Todos los servicios se comunican a través de la red Docker `sail`.

---

# Notas

Si el proyecto fue clonado por primera vez y aparece un error relacionado con Vite o assets
faltantes, ejecutar (dentro del contenedor):

```
npm install
npm run build
```

Si hay problemas de permisos en Linux:

```
chmod -R 775 storage bootstrap/cache
```

Si al autenticarte obtienes errores relacionados con JWT (token inválido, secret no configurado),
verifica que `JWT_SECRET` exista en `.env` (paso 9) y vuelve a limpiar la cache de configuración:

```
php artisan config:clear
```

---

# Soporte

Si el entorno no levanta correctamente verificar:

1. Docker instalado
2. Puertos disponibles
3. Archivo `.env` configurado (conexión a `db_app` y `JWT_SECRET`)
4. Dependencias instaladas con `composer install` y `npm install`
5. Migraciones y seeders ejecutados con `php artisan migrate --seed`
