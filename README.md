# Proyecto Laravel con Docker

Este proyecto está configurado para ejecutarse utilizando Docker y Docker Compose.  
Incluye los siguientes servicios:

- Laravel (PHP 8.5)
- MySQL
- Redis
- Meilisearch
- Mailpit
- Selenium
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


docker -v
docker compose version


---

# Instalación del proyecto

## 1. Clonar el repositorio


git clone "nombre-del-proyecto"

cd "nombre-del-proyecto"


---

## 2. Configurar variables de entorno

Si no existe el archivo `.env`:


cp .env.example .env


Revisar al menos estas variables:


APP_URL=http://localhost

DB_CONNECTION=mysql

DB_HOST=db_app

DB_PORT=3306

DB_DATABASE=laravel

DB_USERNAME=sail

DB_PASSWORD=password


Si hay conflictos de puertos en tu máquina puedes modificar:


APP_PORT=8080
FORWARD_DB_PORT=3308


---

## 3. Levantar los contenedores


docker compose up -d --build


Esto iniciará todos los servicios del proyecto.

---

## 4. Entrar al contenedor de la aplicación


docker exec -it tg-tda-app-1 bash


---

## 5. Instalar dependencias de PHP

Dentro del contenedor ejecutar:


composer install


---

## 6. Instalar dependencias de frontend


npm install


---

## 7. Compilar los assets


npm run build


Esto generará:


public/build/manifest.json


archivo necesario para que Laravel cargue los assets.

---

## 8. Generar la key de Laravel


php artisan key:generate


---

## 9. Ejecutar migraciones


php artisan migrate


---

## 10. Limpiar cache


php artisan optimize:clear


---

# Acceso al proyecto

Aplicación Laravel


http://localhost


phpMyAdmin


http://localhost:82


Mailpit (testing de correos)


http://localhost:8025


---

# Comandos útiles

Levantar contenedores


docker compose up -d


Apagar contenedores


docker compose down


Entrar al contenedor de Laravel


docker exec -it tg-tda-app-1 bash


Reconstruir contenedores


docker compose up -d --build


Eliminar contenedores y volúmenes


docker compose down -v


---

# Estructura del entorno Docker

Servicios incluidos:

- app → Laravel
- db_app → MySQL
- redis → Redis
- meilisearch → motor de búsqueda
- mailpit → servidor SMTP para pruebas
- selenium → pruebas automatizadas
- phpmyadmin → interfaz para base de datos

Todos los servicios se comunican a través de la red Docker `sail`.

---

# Notas

Si el proyecto fue clonado por primera vez y aparece un error relacionado con Vite o assets faltantes, ejecutar:


npm install
npm run build


Si hay problemas de permisos en Linux:


chmod -R 775 storage bootstrap/cache


---

# Flujo de trabajo recomendado

Levantar entorno


docker compose up -d


Entrar al contenedor


docker exec -it tg-tda-app-1 bash


Trabajar normalmente con Laravel.


php artisan ...


---

# Soporte

Si el entorno no levanta correctamente verificar:

1. Docker instalado
2. Puertos disponibles
3. Archivo `.env` configurado
4. Dependencias instaladas con `composer install` y `npm install`
