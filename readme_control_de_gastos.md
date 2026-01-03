# 💰 Sistema de Control de Gastos  
### PHP • MySQL • Seguridad • Portfolio Project

![PHP](https://img.shields.io/badge/PHP-8+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![Security](https://img.shields.io/badge/Security-CSRF%20%7C%20XSS%20%7C%20PDO-success)
![License](https://img.shields.io/badge/License-MIT-blue)

Aplicación web desarrollada en **PHP puro** para la gestión de ingresos y gastos personales, enfocada en **buenas prácticas, seguridad y estructura profesional**, ideal como proyecto de **portfolio backend**.

---

## 🚀 Funcionalidades principales

- Autenticación de usuarios (registro / login / logout)
- CRUD completo de ingresos y gastos
- Categorías personalizadas
- Presupuesto mensual configurable
- Alertas visuales al superar el presupuesto
- Dashboard con resumen financiero
- Gráfico de gastos por categoría (Chart.js)
- Exportación de datos a CSV

---

## 🔐 Seguridad implementada

- Sesiones seguras
- Prepared Statements con PDO
- Protección CSRF en formularios críticos
- Validación estricta de inputs
- Sanitización de salidas (XSS)
- Control de acceso por usuario
- Contraseñas encriptadas con `password_hash()`
- Eliminaciones solo por método POST

---

## 🧱 Tecnologías utilizadas

- **PHP 8+**
- **MySQL / MariaDB**
- HTML5 / CSS3
- JavaScript
- PDO
- Chart.js

---

## 📂 Estructura del proyecto
```
control-gastos/
│
├── auth/
│ ├── csrf.php
│ ├── login.php
│ ├── login_post.php
│ ├── register.php
│ ├── register_post.php
│ ├── logout.php
│ └── proteger.php
│
├── movimientos/
│ ├── crear.php
│ ├── insertar.php
│ ├── editar.php
│ └── eliminar.php
│
├── presupuesto/
│ ├── crear.php
│ └── guardar.php
│
├── exportar/
│ └── exportar_csv.php
│
├── static/
│ ├── css/
│ └── js/
│ └── charts.js
│
├── screenshot/
│ ├── login.png
│ ├── registro.png
│ ├── dashboard.png
│ ├── agregar_movimiento.png
│ └── presu_mensual.png
│
├── sql/
│ └── control_gastos.sql
│
├── .htaccess
├── conexion.php
├── index.php
└── README.md
```

---

## 🛠 Instalación local

### 1️⃣ Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/control-gastos.git

2. Crear base de datos en MySQL
```sql
CREATE DATABASE control_gastos;
```

3. Importar el archivo SQL (tablas)

4. Configurar conexión
Editar `conexion.php`:
```php
$host = "localhost";
$db   = "control_gastos";
$user = "root";
$pass = "";
```

5. Ejecutar en servidor local (XAMPP / Laragon / WAMP)

---

## 🔐 Seguridad implementada

- Cada consulta filtra por `usuario_id`
- Eliminaciones solo por POST
- Tokens CSRF en formularios críticos
- Escapado de datos con `htmlspecialchars()`
- Validación estricta de inputs

---

## 🌍 Deploy (Producción)

### Recomendado
- Hosting con PHP 8+
- MySQL

### Pasos generales

1. Subir archivos por FTP
2. Crear base de datos en el hosting
3. Actualizar credenciales en `conexion.php`
4. Verificar permisos de carpetas

---

## 📸 Capturas

/screenshot/

---

## 👨‍💻 Autor

**Matías Henríquez**

Proyecto desarrollado con fines educativos y de portfolio.

