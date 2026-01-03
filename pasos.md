Estoy desarrollando un proyecto web llamado **Control de Gastos** usando:

- PHP puro (sin frameworks)
- MySQL con PDO
- Apache (WampServer)
- phpMyAdmin
- HTML, CSS y JavaScript
- Chart.js para gráficos

📁 Estructura aproximada del proyecto:
- /index.php
- /database/conexion.php
- /movimientos/
   - crear.php
   - guardar.php
   - editar.php
   - actualizar.php
   - eliminar.php
- /static/css/style.css
- /static/js/charts.js

====================================
📌 ESTADO ACTUAL DEL PROYECTO
====================================

✔ Base de datos creada (`control_gastos`)
✔ Conexión PDO funcionando
✔ Tablas principales:
   - categorias
   - movimientos

✔ CRUD completo de movimientos:
   - Crear ingresos y gastos
   - Editar
   - Eliminar
   - Listar

✔ Campos de movimientos:
   - id
   - tipo (ingreso | gasto)
   - monto
   - fecha
   - descripcion
   - categoria_id

✔ Relación con categorías (JOIN)

✔ Filtro por mes y año en index.php

✔ Resumen financiero:
   - Total ingresos
   - Total gastos
   - Balance

✔ Gráfico de gastos por categoría:
   - Chart.js (pie)
   - Datos enviados desde PHP a JS
   - Canvas funcional

✔ Interfaz ya visible y funcional
✔ Datos reales cargados
✔ Código comentado y ordenado
✔ Prepared Statements (PDO)
✔ Confirmación al eliminar

====================================
📈 FUNCIONALIDADES QUE YA SE VEN
====================================

- Tabla con movimientos
- Botón “Nuevo movimiento”
- Acciones editar / eliminar
- Filtro mensual
- Gráfico de gastos
- Estilo CSS aplicado

====================================
🚀 ROADMAP / LO QUE FALTA HACER
====================================

1️⃣ LOGIN DE USUARIOS (siguiente paso)
- Tabla usuarios
- Registro
- Login
- Sesiones
- Relación usuario_id en movimientos
- Mostrar solo datos del usuario logueado

2️⃣ PRESUPUESTO MENSUAL
- Tabla presupuestos
- Definir monto mensual
- Comparar con gastos
- Alertas visuales

3️⃣ EXPORTAR A EXCEL
- Exportar movimientos por mes/año
- CSV o XLSX
- Botón “Exportar”

4️⃣ SEGURIDAD
- Validaciones backend
- Sanitización de inputs
- CSRF tokens
- Control de acceso (si no hay sesión, redirigir a login)

5️⃣ DEPLOY + README FINAL
- Subir proyecto a hosting
- README profesional
- Screenshots
- Instrucciones de instalación

====================================
🎯 OBJETIVO FINAL
====================================

Tener un proyecto CRUD completo, profesional y presentable para portafolio, con lógica de negocio real y buenas prácticas en PHP.
