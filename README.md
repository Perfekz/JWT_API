# Integración con Seguridad en APIs con JWT (Arquitectura Stateless)
1.	Los archivos que creas: login.php (emisor del token), seguridad.php (el middleware perimetral con try/catch) y api/products.php (el endpoint protegido).
2.	Las herramientas clave: Composer, la biblioteca externa firebase/php-jwt, y Postman / Thunder Client para enviar los encabezados Authorization: Bearer.
3.	Lo que aprende el estudiante: Aprenden cómo asegurar un backend completo para aplicaciones móviles o frontend modernos (Single Page Applications). Entienden el concepto de autenticación Stateless (sin estado), donde el servidor no recuerda al usuario mediante sesiones tradicionales, sino que exige el token en cada petición HTTP.

## ¿Qué es el Payload?
El nombre Payload significa literalmente "carga útil". Es la parte central del JWT donde viaja la información que el servidor quiere "recordar" sobre el usuario después de que este se ha logueado.
En tu código, el payload es el array asociativo que contiene tres tipos de datos:

1. Claims Registrados (Estándar)
Campos predefinidos por el protocolo JWT para asegurar la integridad y el tiempo de vida del token:

 - iss (Issuer): Identifica a la entidad emisora del token (en este caso, nuestro servidor local).
 - iat (Issued At): Registra el timestamp exacto (usando time()) de cuándo fue emitido el token.
 - exp (Expiration): Define el tiempo límite de validez. Se establece mediante time() + 3600, otorgando una sesión activa de 1 hora.

2. Claims Privados (Personalizados)
Contienen la información específica de negocio necesaria para la sesión:
 - data: Objeto que encapsula la identidad y permisos del usuario:
 - id: Identificador único del usuario en la base de datos.
 - usuario: Nombre de usuario (ej. admin).
 - rol: Perfil de acceso (ej. profesor).

 ## Postman
 
 <img width="1522" height="761" alt="image" src="https://github.com/user-attachments/assets/adc0fed3-360b-4e00-aa19-35b697d07c50" />



1. ¿Qué es x-www-form-urlencoded?
Lo que estás viendo en Postman bajo la opción x-www-form-urlencoded es el formato que el cliente (en este caso Postman) utiliza para enviar los datos al servidor.
Es el formato estándar que utilizan los formularios HTML tradicionales cuando envías un <form>. Imagínatelo como una cadena de texto larga donde los campos se concatenan con símbolos:

*** Cómo lo ve Postman: Una lista ordenada de "Key" (usuario) y "Value" (admin).
*** Cómo viaja realmente por internet (el "cable"): usuario=admin&clave=12345678

El servidor recibe esta cadena, la interpreta y, gracias a PHP, automáticamente la convierte en un arreglo asociativo que tú puedes leer fácilmente en tu código así: $_POST['usuario'] y $_POST['clave'].

2. ¿Por qué es el mejor formato para su Login?
Para un inicio de sesión, es el formato más simple y compatible porque:
- Es ligero: No requiere la sobrecarga de un formato más complejo como JSON.
- Compatibilidad: Cualquier servidor PHP lo entiende de forma nativa sin necesidad de configuraciones adicionales.

## Configuración de Postman para emular un formulario de inicio real
Cuando se configura Postman así, se está emulando un formulario de inicio de sesión real. Si más adelante deciden hacer una App móvil o un frontend con React, probablemente prefieran usar raw (JSON), pero para aprender la base de un API con PHP puro, x-www-form-urlencoded es el camino más directo.

## Resumen pedagógico:

"Al seleccionar x-www-form-urlencoded, estamos hablando el idioma de los formularios web clásicos. Estamos enviando los datos como una lista de etiquetas (usuario, clave) que el servidor PHP sabe leer directamente en su variable global $_POST."
