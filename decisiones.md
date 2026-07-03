# Decisiones Técnicas Justificadas - Proyecto Materias

1. **Separación de Responsabilidades por Capas (MVC Puro):** Se estructuró el software aislando la lógica en un enrutador unificado (`index.php`), controladores del lado del servidor (`MateriaController.php`), repositorios de persistencia (`MateriaRepository.php`) y servicios transversales (`AuthService.php`). Esto garantiza una arquitectura limpia y autocontenida sin frameworks.

2. **Mitigación Estricta de Inyecciones SQL (Anti-SQLi):** Se prohibió categóricamente la concatenación de variables de usuario en las consultas. Se implementó el uso exclusivo de sentencias preparadas nativas de PDO con marcadores de posición posicionales (`?`) tanto para la consulta del usuario semilla como para el CRUD completo de la entidad Materia.

3. **Blindaje de Sesiones y Protección OWASP Top 10:** El manejo de sesiones se configuró imperativamente con las directivas de seguridad `cookie_httponly` y `cookie_samesite=Strict`. Además, en cada respuesta HTTP se inyectan las cabeceras defensivas obligatorias (`X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff` y `Content-Security-Policy`).

4. **Prevención de Ataques CSRF y XSS:** Cada formulario mutador de datos (POST) exige un token de seguridad único generado mediante hash aleatorio que se valida en el servidor usando `hash_equals`. Toda salida dinâmica dirigida hacia las vistas HTML se procesa mediante la función universal de escape `htmlspecialchars` con la bandera `ENT_QUOTES` en codificación UTF-8.

5. **Validación Dual e Integridad de Reglas de Negocio:** La interfaz de usuario implementa validaciones HTML5 nativas (`required`, `minlength`, `maxlength`), pero el servidor actúa como fuente de verdad validando estrictamente los tipos de datos y rangos de créditos (1-6) y semestres (1-10) con `filter_input`, retornando códigos HTTP `422` y `400` si existen violaciones.
