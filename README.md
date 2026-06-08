Integración con Seguridad en APIs con JWT (Arquitectura Stateless)
1.	Los archivos que creas: login.php (emisor del token), seguridad.php (el middleware perimetral con try/catch) y api/products.php (el endpoint protegido).
2.	Las herramientas clave: Composer, la biblioteca externa firebase/php-jwt, y Postman / Thunder Client para enviar los encabezados Authorization: Bearer.
3.	Lo que aprende el estudiante: Aprenden cómo asegurar un backend completo para aplicaciones móviles o frontend modernos (Single Page Applications). Entienden el concepto de autenticación Stateless (sin estado), donde el servidor no recuerda al usuario mediante sesiones tradicionales, sino que exige el token en cada petición HTTP.

## ¿Qué es el Payload?
El nombre Payload significa literalmente "carga útil". Es la parte central del JWT donde viaja la información que el servidor quiere "recordar" sobre el usuario después de que este se ha logueado.
En tu código, el payload es el array asociativo que contiene tres tipos de datos:

1. Claims Registrados (Estándar)
Campos predefinidos por el protocolo JWT para asegurar la integridad y el tiempo de vida del token:

iss (Issuer): Identifica a la entidad emisora del token (en este caso, nuestro servidor local).

iat (Issued At): Registra el timestamp exacto (usando time()) de cuándo fue emitido el token.

exp (Expiration): Define el tiempo límite de validez. Se establece mediante time() + 3600, otorgando una sesión activa de 1 hora.

2. Claims Privados (Personalizados)
Contienen la información específica de negocio necesaria para la sesión:

data: Objeto que encapsula la identidad y permisos del usuario:

id: Identificador único del usuario en la base de datos.

usuario: Nombre de usuario (ej. admin).

rol: Perfil de acceso (ej. profesor).
