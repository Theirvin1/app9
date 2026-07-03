# Micro-Sistema Web de Administración de Materias

### 1. Datos del Estudiante
* **Apellidos:** [CAJAS IBARRA]
* **Nombres:** [IRVIN MARCELO]
* **Cédula:** [1251039606]
* **Paralelo:** [A]
* **Fecha del Examen:** 03 de Julio de 2026

### 2. Pila Tecnológica Elegida y Versión Exacta
* **PHP:** v8.2.12 (Nativo, sin dependencias ni librerías externas)
* **Base de Datos:** PostgreSQL v16 (Alpine)
* **Servidor Web:** Apache HTTP Server (XAMPP / Docker)

### 3. Requisitos Previos
* Docker Desktop v4.x activo con WSL 2 backend o Servidor XAMPP local con módulo Apache activo.

### 4. Instrucciones de Arranque en un Solo Comando
Para desplegar la infraestructura contenerizada completa de forma automática, ejecute:
```bash
cp .env.example .env && docker compose up -d --build
```
*Plan de Contingencia:* Si utiliza Apache local mediante XAMPP, asegúrese de arrancar el servicio desde el panel de control y abrir la aplicación directamente en la ruta de su servidor local.

### 5. Credenciales del Usuario Semilla
* **Usuario:** `admin`
* **Contraseña:** `Admin*2026`
* *Nota: Credenciales exclusivas integradas automáticamente mediante seed.sql para la evaluación académica.*

### 6. URL Local del Sistema
* Acceso directo al formulario de autenticación: [http://localhost/APP_EVA_SEM9/index.php/login](http://localhost/APP_EVA_SEM9/index.php/login)

### 7. Cómo Probar el CRUD
La suite completa de endpoints y llamadas de prueba asíncronas para el inicio de sesión y validación del CRUD de Materias se encuentra documentada y ejecutable dentro del archivo técnico `docs/requests.http`.

### 8. Cómo Verificar las Defensas de Seguridad
Ejecute el siguiente comando cURL en su consola de comandos para validar que las cabeceras HTTP de protección activa contra el OWASP Top 10 se inyecten de forma correcta en el cliente:
```bash
curl -I http://localhost/APP_EVA_SEM9/index.php/login
```

### 9. Hash del Último Commit anterior a las 10H45
* **Hash:** [Se genera al ejecutar git rev-parse --short HEAD]
