# Integración con Seguridad en APIs con JWT (Arquitectura Stateless)
1.	Los archivos que creas: login.php (emisor del token), seguridad.php (el middleware perimetral con try/catch) y api/products.php (el endpoint protegido).
2.	Las herramientas clave: Composer, la biblioteca externa firebase/php-jwt, y Postman para enviar los encabezados Authorization: Bearer.
3. Concepto de autenticación Stateless (sin estado), donde el servidor no recuerda al usuario mediante sesiones tradicionales, sino que exige el token en cada petición HTTP.

## ¿Por qué es REST?
No es "solo una API" porque el protocolo HTTP tiene un significado específico para cada método. Si usas Postman para enviar estos métodos, estás cumpliendo con los principios de una API REST:

GET (Lectura): Obtienes datos del servidor.<br>
POST (Creación): Envías nuevos datos para crear un recurso.<br>
PUT (Actualización): Envías datos para reemplazar un recurso existente.<br>
DELETE (Eliminación): Solicitas borrar un recurso.<br>

## 🌐 Tecnologías utilizadas  

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) 
![Apache](https://img.shields.io/badge/Apache-D22128?style=for-the-badge&logo=apache&logoColor=white) 
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white) 
![Postman](https://img.shields.io/badge/Postman-FF6C37?style=for-the-badge&logo=postman&logoColor=white)
![API REST](https://img.shields.io/badge/API_REST-005571?style=for-the-badge&logo=openapi&logoColor=white)

## Códigos de Estados HTTP
Cuando el servidor (tu API REST) responde a una petición, utiliza los Códigos de Estado HTTP. <br>
Estos códigos son el lenguaje con el que tu API le dice al cliente (Postman o el Frontend) si todo salió bien o si hubo un problema.

Los Códigos de Estado más importantes <br>
200 OK: La petición fue exitosa. (Ejemplo: Al listar productos o hacer login).<br>
201 Created: Se creó un recurso correctamente. (Ejemplo: Al registrar un usuario nuevo).<br>
400 Bad Request: El servidor no entiende la petición porque faltan datos o están mal formados.<br>
401 Unauthorized: El usuario no está logueado o el token es inválido/expirado.<br>
403 Forbidden: El usuario está logueado, pero no tiene permiso para esa acción.<br>
404 Not Found: Recurso no encontrado. El cliente busca una URL que no existe o un registro que no está en la base de datos (ej. buscar producto/999 cuando solo hay 10 productos).<br>
500 Internal Server Error: Ocurrió un error en el servidor (un error de código PHP, conexión a base de datos caída, etc.).<br>

### http_response_code(401);

La función http_response_code() en PHP es la forma directa y moderna de decirle al navegador o a la herramienta que está consultando tu API (como Postman) cuál fue el resultado de la operación.
Por defecto, PHP siempre responde con un código 200 OK si todo el código se ejecutó sin errores fatales. Pero en una API REST, tú necesitas ser más específico.
- Si usas http_response_code(404); le envías un mensaje al cliente diciendo: "Lo que pediste no existe".<br>
- Si usas http_response_code(401); le dices: "No estás autorizado para entrar aquí".<br>
- Es como ponerle una etiqueta de estado a la respuesta de tu servidor.<br>
<br>
### Recursos
Paso 1: 
Abre la terminal o consola de comandos, navega hasta la carpeta raíz donde tienes tus archivos de PHP puro (donde planeas poner tu seguridad.php y products.php) y ejecuta:
<br>
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

### Uso en Postman

#### 1. Login con `login.php`
- Método: `POST`
- URL: `http://localhost/path/to/login.php`
- Body puede ser:
  - `x-www-form-urlencoded` con `usuario` y `clave`
  - o `raw` JSON con `Content-Type: application/json`

Ejemplo JSON:
```json
{
  "usuario": "houzheng",
  "clave": "houzheng67"
}
```

La respuesta debe regresar el token:
```json
{
  "token": "<jwt>",
  "expira_en": "3600 segundos"
}
```

#### 2. Acceder a `api/products.php`
- Método: `GET`, `POST`, `PUT` o `DELETE`
- URL: `http://localhost/path/to/api/products.php`
- Header obligatorio:
  - `Authorization: Bearer <token>`
  - `Content-Type: application/json` (para `POST`/`PUT`)

Para `GET` y `DELETE` puedes usar `?id=1` en la URL.
Para `POST` y `PUT` envía JSON en el cuerpo.

#### 3. Cómo el proyecto procesa los headers
`seguridad.php` busca el token en:
- `$_SERVER['Authorization']`
- `$_SERVER['HTTP_AUTHORIZATION']`
- `apache_request_headers()`

Esto asegura que el proyecto funcione en diferentes configuraciones de PHP/Apache.

### 4. ¿Por qué x-www-form-urlencoded se usa en el login?
Es el formato más compatible con PHP nativo y con formularios HTML.
Postman lo envía como `usuario=...&clave=...` y PHP lo convierte en `$_POST['usuario']` y `$_POST['clave']`.

### 5. El proceso de obtención del token
El flujo que implementaste es el correcto para una API moderna:
1.	Credenciales: El cliente envía el usuario y la contraseña por POST.
2.	Verificación: `login.php` valida las credenciales contra la base de datos.
3.	Firma: Firebase JWT convierte el payload en token firmado.
4.	Respuesta: El servidor devuelve el token al cliente.

# Autor

Este proyecto fue desarrollado por los estudiantes de la Universidad Tecnológica de Panamá:

Nombre: Erick Hou 8-1017-473 y Jessica Zheng 8-1033-370

Correo: erick.hou@utp.ac.pa y jessica.zheng@utp.ac.pa

Curso: Desarrollo de Software VII

Instructor del Laboratorio: Irina Fong

Fecha de ejecución: 29/06/2026