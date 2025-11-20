# Referencia de API - EdulabsAI

## Servicios Web (Web Services)

### 1. local_geniai_chat

**Descripción:** Envía mensajes al chatbot y recibe respuestas de OpenAI.

**Clase:** `\local_geniai\external\chat`  
**Método:** `api`  
**Tipo:** write  
**AJAX:** Sí

**Parámetros de entrada:**
```php
[
    'courseid' => int,      // ID del curso actual
    'message' => string,    // Mensaje del usuario
    'audio' => string       // Audio en base64 (opcional)
]
```

**Retorno:**
```php
[
    'content' => string,    // Respuesta del chatbot
    'audio' => string       // URL del audio generado (opcional)
]
```

---

### 2. local_geniai_history

**Descripción:** Obtiene o limpia el historial de conversación.

**Clase:** `\local_geniai\external\history`  
**Método:** `api`  
**Tipo:** write  
**AJAX:** Sí

**Parámetros de entrada:**
```php
[
    'courseid' => int,      // ID del curso actual
    'action' => string      // 'history' o 'clear'
]
```

**Retorno (action='history'):**
```php
[
    'content' => string     // JSON con historial de mensajes
]
```

**Retorno (action='clear'):**
```php
[
    'success' => bool       // true si se limpió correctamente
]
```

---

### 3. local_geniai_create_course ⭐ NUEVO

**Descripción:** Crea un curso completo usando OpenAI GPT y DALL-E.

**Clase:** `\local_geniai\external\course_creator`  
**Método:** `create_course`  
**Tipo:** write  
**AJAX:** Sí  
**Capacidad requerida:** `local/geniai:createcourse`

**Parámetros de entrada:**
```php
[
    'topic' => string,          // Tema del curso
    'weeks' => int,             // Número de semanas (1-52)
    'description' => string     // Descripción breve del curso
]
```

**Retorno:**
```php
[
    'success' => bool,          // true si se creó correctamente
    'courseid' => int,          // ID del curso creado
    'coursename' => string,     // Nombre del curso
    'courseurl' => string,      // URL para acceder al curso
    'message' => string         // Mensaje de éxito
]
```

**Ejemplo de llamada AJAX:**
```javascript
require(['core/ajax'], function(ajax) {
    ajax.call([{
        methodname: 'local_geniai_create_course',
        args: {
            topic: 'Inteligencia Artificial',
            weeks: 8,
            description: 'Curso introductorio sobre IA'
        }
    }])[0].done(function(result) {
        console.log('Curso creado:', result.coursename);
        console.log('URL:', result.courseurl);
    }).fail(function(error) {
        console.error('Error:', error);
    });
});
```

---

## Capacidades (Capabilities)

### local/geniai:view

**Descripción:** Permite ver y usar el chat del asistente.

**Tipo:** read  
**Contexto:** CONTEXT_SYSTEM

**Roles con acceso por defecto:**
- guest
- student
- teacher
- editingteacher
- manager

---

### local/geniai:manage

**Descripción:** Permite administrar la configuración del plugin.

**Tipo:** write  
**Contexto:** CONTEXT_SYSTEM

**Roles con acceso por defecto:**
- teacher
- editingteacher
- coursecreator
- manager

---

### local/geniai:createcourse ⭐ NUEVO

**Descripción:** Permite crear cursos automáticamente con IA.

**Tipo:** write  
**Contexto:** CONTEXT_SYSTEM

**Roles con acceso por defecto:**
- editingteacher
- coursecreator
- manager

**Nota:** Los estudiantes NO tienen acceso a esta capacidad.

---

## Clases PHP

### course_creator

**Namespace:** `local_geniai\external`  
**Archivo:** `classes/external/course_creator.php`

#### Métodos Públicos

##### create_course_parameters()
```php
public static function create_course_parameters()
```
Retorna la definición de parámetros esperados.

##### create_course($topic, $weeks, $description)
```php
public static function create_course($topic, $weeks, $description)
```
Crea un curso completo con IA.

**Parámetros:**
- `$topic` (string): Tema del curso
- `$weeks` (int): Número de semanas
- `$description` (string): Descripción del curso

**Retorno:** array con datos del curso creado

**Excepciones:**
- `moodle_exception` si falta API key
- `moodle_exception` si hay error de OpenAI
- `moodle_exception` si la respuesta es inválida

##### create_course_returns()
```php
public static function create_course_returns()
```
Retorna la definición de la estructura de retorno.

#### Métodos Privados

##### generate_course_content($apikey, $topic, $weeks, $description)
```php
private static function generate_course_content($apikey, $topic, $weeks, $description)
```
Genera el contenido del curso usando OpenAI GPT.

**Retorno:**
```php
[
    'coursename' => string,
    'description' => string,        // HTML
    'course_image_url' => string,   // URL de imagen
    'image_prompt' => string,
    'weeks' => [
        [
            'number' => int,
            'name' => string,
            'description' => string, // HTML
            'image_url' => string,   // URL de imagen
            'image_prompt' => string
        ],
        // ... más semanas
    ]
]
```

##### generate_image($apikey, $prompt)
```php
private static function generate_image($apikey, $prompt)
```
Genera una imagen usando DALL-E 3.

**Parámetros:**
- `$apikey` (string): API Key de OpenAI
- `$prompt` (string): Descripción de la imagen en inglés

**Retorno:** string (URL de la imagen) o null si falla

##### create_moodle_course($coursedata)
```php
private static function create_moodle_course($coursedata)
```
Crea el curso en Moodle con todos los datos.

**Retorno:** int (ID del curso creado)

##### download_and_set_course_image($courseid, $imageurl)
```php
private static function download_and_set_course_image($courseid, $imageurl)
```
Descarga y asigna la imagen de portada del curso.

**Retorno:** bool (true si tiene éxito)

---

## Integración con OpenAI

### GPT-4 / GPT-4o-mini

**Endpoint:** `https://api.openai.com/v1/chat/completions`

**Configuración:**
```php
[
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => '...'],
        ['role' => 'user', 'content' => '...']
    ],
    'temperature' => 0.7,
    'max_tokens' => 2000
]
```

**Headers requeridos:**
```
Content-Type: application/json
Authorization: Bearer {API_KEY}
```

### DALL-E 3

**Endpoint:** `https://api.openai.com/v1/images/generations`

**Configuración:**
```php
[
    'model' => 'dall-e-3',
    'prompt' => '...',
    'n' => 1,
    'size' => '1024x1024',
    'quality' => 'standard'
]
```

**Headers requeridos:**
```
Content-Type: application/json
Authorization: Bearer {API_KEY}
```

---

## JavaScript API

### Funciones Principales

#### detectCourseCreation(message)
```javascript
function detectCourseCreation(message)
```

**Parámetros:**
- `message` (string): Mensaje del usuario

**Retorno:**
```javascript
{
    isCourseRequest: boolean,
    topic: string,
    weeks: number
}
```

#### handleCourseCreation(detection, fullMessage)
```javascript
function handleCourseCreation(detection, fullMessage)
```

**Parámetros:**
- `detection` (object): Resultado de detectCourseCreation
- `fullMessage` (string): Mensaje completo del usuario

**Comportamiento:**
1. Muestra mensaje del usuario en el chat
2. Muestra indicador de carga
3. Llama al servicio web
4. Muestra resultado (éxito o error)

---

## Eventos (Hooks)

### before_footer

**Descripción:** Inyecta el HTML del chat en todas las páginas.

**Función:** `local_geniai_before_footer()`  
**Archivo:** `lib.php`

**Comportamiento:**
- Carga el template `chat.mustache`
- Inyecta estilos CSS
- Inyecta JavaScript del chat
- Pasa datos de configuración al frontend

---

## Base de Datos

### Tablas Utilizadas

#### mdl_capabilities
Almacena las capacidades del plugin.

**Registros:**
- `local/geniai:view`
- `local/geniai:manage`
- `local/geniai:createcourse`

#### mdl_external_functions
Almacena los servicios web externos.

**Registros:**
- `local_geniai_chat`
- `local_geniai_history`
- `local_geniai_create_course`

#### mdl_config_plugins
Almacena configuración del plugin.

**Claves importantes:**
- `apikey`: API Key de OpenAI
- `model`: Modelo de GPT a usar
- `geniainame`: Nombre del asistente
- `temperature`: Temperatura de GPT
- `top_p`: Top P de GPT

---

## Configuración

### Variables de Configuración

```php
// Obtener configuración
$apikey = get_config('local_geniai', 'apikey');
$model = get_config('local_geniai', 'model');
$name = get_config('local_geniai', 'geniainame');

// Establecer configuración
set_config('apikey', 'sk-proj-...', 'local_geniai');
set_config('model', 'gpt-4o-mini', 'local_geniai');
```

### Configuraciones Disponibles

| Clave | Tipo | Por Defecto | Descripción |
|-------|------|-------------|-------------|
| `apikey` | string | - | API Key de OpenAI |
| `model` | string | gpt-4o-mini | Modelo de GPT |
| `geniainame` | string | EdulabsAI | Nombre del asistente |
| `temperature` | float | 0.7 | Temperatura de GPT |
| `top_p` | float | 0.8 | Top P de GPT |
| `max_tokens` | int | 2000 | Tokens máximos |
| `frequency_penalty` | float | 0.0 | Penalización de frecuencia |
| `presence_penalty` | float | 0.0 | Penalización de presencia |

---

## Costos Estimados

### Por Curso Generado (8 semanas)

**GPT-4o-mini:**
- Tokens: ~1500 prompt + 500 completion
- Costo: $0.01 - 0.02

**DALL-E 3:**
- Imágenes: 9 (1 curso + 8 semanas)
- Costo por imagen: $0.04
- Costo total: $0.36

**Total por curso:** ~$0.37 - 0.38

### Por Conversación de Chat

**GPT-4o-mini:**
- Tokens promedio: 500 prompt + 200 completion
- Costo: $0.001 - 0.002

---

## Límites y Restricciones

### OpenAI API

- **Rate Limits:** Varía según plan (60 RPM para tier 1)
- **Token Limits:** gpt-4o-mini: 128K contexto
- **DALL-E 3:** 7 imágenes por minuto

### Moodle

- **Máximo de semanas:** 52
- **Mínimo de semanas:** 1
- **Tamaño máximo de imagen:** 10 MB
- **Formato de imagen:** PNG, JPG

---

**Versión de API:** 2.4.0-edulabs  
**Última actualización:** Noviembre 2025
