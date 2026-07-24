# Café Aurora - Sistema de Gestión de Cafetería

¡Bienvenido al repositorio de **Café Aurora**! Este proyecto es un sistema de administración completo para restaurantes y cafeterías, desarrollado con las mejores prácticas en Laravel 12 y diseñado con Bootstrap 5 para ofrecer una interfaz moderna, robusta y completamente interactiva.

## 🚀 Módulos Principales

- **Dashboard:** Panel central con métricas en tiempo real y gráficas interactivas con Chart.js.
- **Reservas (Command Center):** Calendario y plano de mesas 100% interactivo mediante AJAX (Drag & Drop, sincronización visual en tiempo real).
- **Pedidos e Inventario:** Control total del flujo de ventas, comandas y stock de productos.
- **Opiniones y Calificaciones:** Sistema estilo E-Commerce para reseñas de clientes con promedios autocalculados.
- **Seguridad y Roles:** Implementación de roles administrativos y de clientes, junto con bitácora de auditoría detallada (`audit_logs`) para rastrear cada modificación.
- **Reportes:** Generación de reportes operativos en tiempo real.

## 🛠️ Tecnologías

- **Backend:** Laravel 12 (PHP 8.2+), Eloquent ORM.
- **Base de Datos:** MySQL / MariaDB.
- **Frontend:** Blade, Vanilla JS, Bootstrap 5, SASS/Vite.
- **Librerías Extra:** FullCalendar v6, Chart.js.

## 📋 Requisitos Previos

Asegúrate de contar con lo siguiente instalado en tu entorno de desarrollo local:
- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [Node.js y NPM](https://nodejs.org/)
- [MySQL](https://www.mysql.com/) / MariaDB

## ⚙️ Instalación Paso a Paso

1. **Clonar el repositorio:**
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   cd cafe-aurora
   ```

2. **Instalar dependencias de PHP y Node:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   ```
   > Abre el archivo `.env` generado y configura las variables `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` para que coincidan con tu servidor MySQL local.

4. **Generar la llave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones (con datos semilla):**
   ```bash
   php artisan migrate --seed
   ```
   *(Nota: Asegúrate de tener la base de datos creada en MySQL antes de ejecutar este comando).*

6. **Compilar recursos estáticos (Vite):**
   ```bash
   npm run build
   ```

7. **Levantar el servidor local:**
   ```bash
   php artisan serve
   ```
   *Accede a http://localhost:8000 en tu navegador.*

## 🔒 Usuarios de Prueba (Seeders)

Si ejecutaste las migraciones con `--seed`, podrás ingresar con los siguientes usuarios de prueba:

- **Administrador:**
  - Correo: `admin@cafeaurora.com`
  - Contraseña: `password`

- **Cliente:**
  - Correo: `cliente@correo.com`
  - Contraseña: `password`

## 🛡️ Seguridad y Buenas Prácticas
Este proyecto implementa defensas contra inyección SQL utilizando Eloquent, transacciones manuales (`DB::beginTransaction`) para evitar inconsistencias de datos, validación robusta de Request en todos los controladores y un sistema de escape completo en las vistas de Blade para protección XSS.

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [MIT License](https://opensource.org/licenses/MIT).

---
**Café Aurora** - Construido con dedicación para dominar la gestión de servicios alimentarios.
