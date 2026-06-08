# Integración con Seguridad en APIs con JWT (Arquitectura Stateless)
1.	Los archivos que creas: login.php (emisor del token), seguridad.php (el middleware perimetral con try/catch) y api/products.php (el endpoint protegido).
2.	Las herramientas clave: Composer, la biblioteca externa firebase/php-jwt, y Postman para enviar los encabezados Authorization: Bearer.
3. Concepto de autenticación Stateless (sin estado), donde el servidor no recuerda al usuario mediante sesiones tradicionales, sino que exige el token en cada petición HTTP.

## 🌐 Tecnologías utilizadas  

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) 
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white) 
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) 
![XAMPP](https://img.shields.io/badge/XAMPP-F37623?style=for-the-badge&logo=xampp&logoColor=white) 
![WampServer](https://img.shields.io/badge/WampServer-FF0040?style=for-the-badge&logo=windows&logoColor=white)


### Recursos
Paso 1: 
Abre la terminal o consola de comandos, navega hasta la carpeta raíz donde tienes tus archivos de PHP puro (donde planeas poner tu seguridad.php y products.php) y ejecuta:
```bash
composer init
```

Paso 2: Descargar la biblioteca de Firebase JWT
En esa misma terminal, ejecuta el comando para requerir el paquete oficial:
```bash
composer require firebase/php-jwt
```

### ¿Qué es el Payload?
El nombre Payload significa literalmente "carga útil". Es la parte central del JWT donde viaja la información que el servidor quiere "recordar" sobre el usuario después de que este se ha logueado.
En tu código, el payload es el array asociativo que contiene tres tipos de datos:

### 1. Claims Registrados (Estándar)
Campos predefinidos por el protocolo JWT para asegurar la integridad y el tiempo de vida del token:

 - iss (Issuer): Identifica a la entidad emisora del token (en este caso, nuestro servidor local).
 - iat (Issued At): Registra el timestamp exacto (usando time()) de cuándo fue emitido el token.
 - exp (Expiration): Define el tiempo límite de validez. Se establece mediante time() + 3600, otorgando una sesión activa de 1 hora.

### 2. Claims Privados (Personalizados)
Contienen la información específica de negocio necesaria para la sesión:
 - data: Objeto que encapsula la identidad y permisos del usuario:
 - id: Identificador único del usuario en la base de datos.
 - usuario: Nombre de usuario (ej. admin).
 - rol: Perfil de acceso (ej. profesor).

 ## Postman
 
 <img width="1522" height="761" alt="image" src="https://github.com/user-attachments/assets/adc0fed3-360b-4e00-aa19-35b697d07c50" />



### 1. ¿Qué es x-www-form-urlencoded?
Lo que estás viendo en Postman bajo la opción x-www-form-urlencoded es el formato que el cliente (en este caso Postman) utiliza para enviar los datos al servidor.
Es el formato estándar que utilizan los formularios HTML tradicionales cuando envías un <form>. Imagínatelo como una cadena de texto larga donde los campos se concatenan con símbolos:

 - Cómo lo ve Postman: Una lista ordenada de "Key" (usuario) y "Value" (admin).
 - Cómo viaja realmente por internet (el "cable"): usuario=admin&clave=12345678

El servidor recibe esta cadena, la interpreta y, gracias a PHP, automáticamente la convierte en un arreglo asociativo que tú puedes leer fácilmente en tu código así: $_POST['usuario'] y $_POST['clave'].

### 2. ¿Por qué es el mejor formato para su Login?
Para un inicio de sesión, es el formato más simple y compatible porque:
- Es ligero: No requiere la sobrecarga de un formato más complejo como JSON.
- Compatibilidad: Cualquier servidor PHP lo entiende de forma nativa sin necesidad de configuraciones adicionales.

### 3. El proceso paso a paso: ¿Cómo se obtiene el token?
El flujo que implementaste es el correcto para una API moderna:
1.	Credenciales: El cliente envía el usuario y la contraseña por POST (como se ve en tu captura de Postman).
2.	Verificación: Tu archivo login.php consulta la base de datos (o la simulación que tienes) para verificar si esas credenciales son correctas.
3.	Firma (Encoding): Si todo está bien, la librería de Firebase JWT::encode toma ese payload, lo combina con tu clave_secreta y lo "empaqueta" en una cadena de texto base64.
4.	Respuesta: El servidor devuelve ese Token al cliente.

### 4. Encabezados HTTP Authorization
Cuando desarrollas APIs, el mayor dolor de cabeza es que cada servidor web (Apache, Nginx, IIS) y cada configuración de PHP maneja los encabezados HTTP de forma ligeramente distinta.
¿Qué estamos buscando?
El cliente envía su token en un encabezado llamado Authorization. El estándar es que luzca así: Authorization: Bearer <tu_token_aqui>.
El problema es que PHP no siempre pone ese dato en el mismo lugar de la variable global $_SERVER. Este código actúa como un "cazador de tesoros" que busca el token en tres niveles:
1.	$_SERVER['Authorization']:
o	Este es el caso ideal. Ocurre cuando PHP corre como un módulo dentro del servidor web (por ejemplo, en configuraciones muy específicas o cuando el servidor hace un esfuerzo por normalizar los datos). Es el acceso más directo.
2.	$_SERVER['HTTP_AUTHORIZATION']:
o	Este es el caso más común. La mayoría de las veces, cuando Apache o Nginx reciben un encabezado HTTP llamado Authorization, PHP lo renombra automáticamente agregándole el prefijo HTTP_ y convirtiéndolo a mayúsculas. Si tu código no buscara aquí, tu API simplemente "no vería" el token que el usuario envió.
3.	apache_request_headers():
o	Este es el "Plan de Emergencia". Si estás en un servidor Apache y por alguna razón de configuración (como ejecutar PHP en modo CGI o FastCGI) las variables anteriores no se llenaron, esta función le pregunta directamente a Apache: "Oye, ¿qué encabezados llegaron en esta petición?". Es la forma más infalible de obtener el dato en entornos Apache.

<img width="1518" height="808" alt="image" src="https://github.com/user-attachments/assets/86a2aeea-cd5b-46ad-8f5b-a49411702d94" />

### 5. Resumen pedagógico:

"Al seleccionar x-www-form-urlencoded, estamos hablando el idioma de los formularios web clásicos. Estamos enviando los datos como una lista de etiquetas (usuario, clave) que el servidor PHP sabe leer directamente en su variable global $_POST."
