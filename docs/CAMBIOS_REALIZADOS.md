# Documentación de Cambios - Plugin EdulabsAI

## Resumen del Proyecto

Modificación del plugin GeniAI de Moodle para Edu Labs, incluyendo:
- Cambio de nombre y branding
- Traducción completa al español
- Colores corporativos de Edu Labs
- Funcionalidad de creación automática de cursos con IA

---

## 1. ARCHIVOS MODIFICADOS

### 1.1. `version.php`
**Ubicación:** `local/geniai/version.php`

**Cambios realizados:**
- Actualización del número de versión de `2025102900` a `2025112001`
- Cambio del release de `2.3.7` a `2.4.0-edulabs`

**Código modificado:**
```php
$plugin->version = 2025112001;
$plugin->release = "2.4.0-edulabs";
```

**Propósito:** Identificar la versión personalizada para Edu Labs y forzar actualización de la base de datos.

---

### 1.2. `db/access.php`
**Ubicación:** `local/geniai/db/access.php`

**Cambios realizados:**
- Agregada nueva capacidad `local/geniai:createcourse`
- Asignada a roles: editingteacher, coursecreator, manager

**Código agregado:**
```php
"local/geniai:createcourse" => [
    "captype" => "write",
    "contextlevel" => CONTEXT_SYSTEM,
    "archetypes" => [
        "editingteacher" => CAP_ALLOW,
        "coursecreator" => CAP_ALLOW,
        "manager" => CAP_ALLOW,
    ],
],
```

**Propósito:** Controlar qué usuarios pueden crear cursos automáticamente (solo profesores y administradores).

---

### 1.3. `db/services.php`
**Ubicación:** `local/geniai/db/services.php`

**Cambios realizados:**
- Agregado nuevo servicio web `local_geniai_create_course`

**Código agregado:**
```php
'local_geniai_create_course' => [
    'classname' => 'local_geniai\external\course_creator',
    'methodname' => 'create_course',
    'description' => 'Create a new course using OpenAI',
    'type' => 'write',
    'ajax' => true,
    'capabilities' => 'local/geniai:createcourse',
],
```

**Propósito:** Registrar el servicio web AJAX para la creación de cursos desde el chat.

---

### 1.4. `classes/external/course_creator.php`
**Ubicación:** `local/geniai/classes/external/course_creator.php`

**Archivo NUEVO creado**

**Funcionalidades implementadas:**

#### Método `create_course()`
- Valida parámetros (topic, weeks, description)
- Verifica permisos del usuario
- Genera contenido con OpenAI
- Crea el curso en Moodle
- Retorna URL del curso creado

#### Método `generate_course_content()`
Llama a la API de OpenAI GPT para generar:
- Nombre del curso
- Descripción HTML del curso
- Prompt para imagen principal
- Para cada semana:
  - Nombre de la semana
  - Descripción HTML
  - Prompt para imagen de la semana

#### Método `generate_image()`
Llama a DALL-E 3 para generar imágenes:
- Tamaño: 1024x1024
- Calidad: standard
- Modelo: dall-e-3

#### Método `create_moodle_course()`
Crea el curso en Moodle con:
- Datos básicos del curso
- Imagen de portada
- Secciones semanales con nombres y descripciones
- Imágenes integradas en cada semana

#### Método `download_and_set_course_image()`
- Descarga imagen desde URL de OpenAI
- Guarda en el sistema de archivos de Moodle
- Asigna como imagen de portada del curso

**Código completo:** Ver archivo `course_creator.php`

**Propósito:** Lógica principal para creación automática de cursos con IA.

---

### 1.5. `amd/src/chat.js`
**Ubicación:** `local/geniai/amd/src/chat.js`

**Cambios realizados:**

#### Función `detectCourseCreation()`
Detecta patrones de solicitud de creación de cursos:
- "Crear un curso sobre [tema] con [N] semanas"
- "Generar un curso sobre [tema] con [N] semanas"
- "Diseñar un curso sobre [tema] con [N] semanas"

```javascript
function detectCourseCreation(message) {
    var patterns = [
        /crear?\s+(?:un\s+)?curso\s+(?:sobre|de|acerca\s+de)\s+(.+?)(?:\s+(?:con|de)\s+(\d+)\s+semanas?)?$/i,
        /generar?\s+(?:un\s+)?curso\s+(?:sobre|de)\s+(.+?)(?:\s+(?:con|de)\s+(\d+)\s+semanas?)?$/i,
        /diseñar?\s+(?:un\s+)?curso\s+(?:sobre|de)\s+(.+?)(?:\s+(?:con|de)\s+(\d+)\s+semanas?)?$/i
    ];
    // ...
}
```

#### Función `handleCourseCreation()`
Maneja el proceso completo:
1. Muestra mensaje del usuario
2. Muestra indicador de "Creando curso..."
3. Llama al servicio web `local_geniai_create_course`
4. Muestra resultado:
   - ✅ Éxito: Nombre del curso + enlace
   - ❌ Error: Mensaje detallado con información técnica

#### Modificación en `sendMessage()`
Intercepta mensajes antes de enviarlos a OpenAI:
```javascript
var courseDetection = detectCourseCreation(messagesend);
if (courseDetection.isCourseRequest) {
    handleCourseCreation(courseDetection, messagesend);
    return; // No enviar a OpenAI normal
}
```

**Propósito:** Detectar y procesar solicitudes de creación de cursos desde el chat.

---

### 1.6. `lang/es/local_geniai.php`
**Ubicación:** `local/geniai/lang/es/local_geniai.php`

**Archivo NUEVO creado**

**Contenido:**
- Traducción completa al español de todas las cadenas del plugin
- 131 strings traducidos
- Strings específicos para creación de cursos:

```php
$string['geniai:createcourse'] = 'Crear cursos con GeniAI';
$string['course_created_success'] = 'Curso creado exitosamente';
$string['noapikey'] = 'No se ha configurado la API key de OpenAI';
$string['openai_error'] = 'Error de OpenAI: {$a}';
$string['invalid_response'] = 'Respuesta inválida de OpenAI';
$string['creating_course'] = 'Creando curso, por favor espera...';
$string['course_creation_failed'] = 'Error al crear el curso';
```

**Propósito:** Interfaz completamente en español para usuarios hispanohablantes.

---

### 1.7. `lang/en/local_geniai.php`
**Ubicación:** `local/geniai/lang/en/local_geniai.php`

**Cambios realizados:**
- Cambio de `pluginname` de `GeniAI` a `EdulabsAI`
- Agregados nuevos strings para funcionalidad de cursos:

```php
$string['pluginname'] = 'EdulabsAI';
$string['geniai:createcourse'] = 'Create courses with GeniAI';
$string['course_created_success'] = 'Course created successfully';
// ... más strings
```

**Propósito:** Mantener soporte en inglés y renombrar el plugin.

---

### 1.8. `styles/_edulabs-colors.scss`
**Ubicación:** `local/geniai/styles/_edulabs-colors.scss`

**Archivo NUEVO creado**

**Paleta de colores Edu Labs:**
```scss
$primary-color: #561682;    // Morado
$secondary-color: #f58020;  // Naranja
$tertiary-color: #e8bc40;   // Amarillo
$neutral-color: #dadada;    // Gris
```

**Elementos personalizados:**

#### Chat
- Header: color primario (#561682)
- Botón flotante: color primario
- Mensajes del servidor: amarillo claro
- Botón de grabación activo: naranja

#### H5P Manager
- Acordeón: color primario
- Encabezados de diálogo: color primario
- Switch activo: color primario
- Barra de reproducción: gradiente morado-naranja

**Código completo:** Ver archivo `_edulabs-colors.scss`

**Propósito:** Aplicar identidad visual de Edu Labs al plugin.

---

### 1.9. `styles.scss`
**Ubicación:** `local/geniai/styles.scss`

**Cambios realizados:**
- Agregada importación al final del archivo:

```scss
// Edu Labs Theme Colors - Sobrescribe los colores por defecto
@import "styles/edulabs-colors";
```

**Propósito:** Incluir los estilos personalizados de Edu Labs.

---

## 2. ARCHIVOS AUXILIARES CREADOS

### 2.1. `test_service.php`
**Ubicación:** `local/geniai/test_service.php`

**Propósito:** Script de diagnóstico para verificar:
- Registro del servicio web en la base de datos
- Capacidades del usuario actual
- Existencia de la clase `course_creator`
- Configuración de la API Key de OpenAI

**Uso:** `http://localhost/local/geniai/test_service.php`

---

### 2.2. `force_upgrade.php`
**Ubicación:** `local/geniai/force_upgrade.php`

**Propósito:** Script CLI para forzar actualización del plugin:
- Eliminar capacidades antiguas
- Registrar nuevas capacidades
- Actualizar servicios web externos
- Asignar permisos a roles
- Actualizar versión en base de datos
- Limpiar cachés

**Uso:** `php force_upgrade.php`

---

## 3. COMPILACIÓN DE ASSETS

### JavaScript
```bash
cd local/geniai/amd
cp src/chat.js build/chat.min.js
```

### CSS/SCSS
```bash
cd local/geniai
sass styles.scss styles.css --style compressed
```

### Limpiar cachés
```bash
cd moodle
php admin/cli/purge_caches.php
```

---

## 4. FLUJO DE FUNCIONAMIENTO

### Creación de Curso - Diagrama de Flujo

```
Usuario escribe: "Crear un curso sobre Python con 8 semanas"
    ↓
JavaScript detecta patrón (detectCourseCreation)
    ↓
Muestra mensaje de carga en el chat
    ↓
Llama a servicio web: local_geniai_create_course
    ↓
Servidor PHP (course_creator.php):
    - Valida permisos
    - Llama a OpenAI GPT para generar contenido
    - Llama a DALL-E 3 para generar imágenes
    - Crea curso en Moodle
    - Asigna imagen de portada
    - Crea secciones semanales con imágenes
    ↓
Retorna al chat:
    - ID del curso
    - Nombre del curso
    - URL para acceder
    ↓
Muestra mensaje de éxito con enlace
```

---

## 5. REQUISITOS DE API

### OpenAI GPT
- **Modelo:** gpt-4o-mini (o gpt-4)
- **Tokens máximos:** 2000
- **Temperatura:** 0.7
- **Uso:** Generación de contenido del curso

### OpenAI DALL-E 3
- **Tamaño:** 1024x1024
- **Calidad:** standard
- **Uso:** 1 imagen principal + 1 imagen por semana

**Costo estimado por curso de 8 semanas:**
- GPT: ~$0.01 - 0.05
- DALL-E 3: ~$0.36 (9 imágenes x $0.04)
- **Total:** ~$0.37 - 0.41 por curso

---

## 6. PERMISOS Y ROLES

### Capacidades del Plugin

| Capacidad | Descripción | Roles |
|-----------|-------------|-------|
| `local/geniai:view` | Ver el chat | Todos (guest, student, teacher, manager) |
| `local/geniai:manage` | Administrar plugin | teacher, editingteacher, manager |
| `local/geniai:createcourse` | Crear cursos con IA | editingteacher, coursecreator, manager |

---

## 7. EJEMPLOS DE USO

### Comandos válidos para crear cursos:

1. `Crear un curso sobre Python con 8 semanas`
2. `Generar un curso de Marketing Digital con 10 semanas`
3. `Diseñar un curso sobre Fotografía con 5 semanas`
4. `Crear un curso sobre Inteligencia Artificial con 6 semanas`

### Respuesta esperada:

```
✅ ¡Curso creado exitosamente!

Introducción a Python para Principiantes
8 semanas

[📚 Ver curso →]
```

---

## 8. CONFIGURACIÓN REQUERIDA

### En Moodle

1. **API Key de OpenAI:**
   - Ir a: Administración → Plugins → Plugins locales → EdulabsAI
   - Configurar: API Key de OpenAI

2. **Permisos:**
   - Verificar que los roles tienen las capacidades correctas
   - Administración → Usuarios → Permisos → Definir roles

3. **Depuración (opcional):**
   ```bash
   php admin/cli/cfg.php --name=debug --set=32767
   php admin/cli/cfg.php --name=debugdisplay --set=1
   ```

---

## 9. SOLUCIÓN DE PROBLEMAS

### El chat no aparece
1. Verificar que el plugin esté instalado
2. Limpiar cachés: `php admin/cli/purge_caches.php`
3. Verificar permisos: `local/geniai:view`

### Error al crear curso
1. Verificar API Key configurada
2. Verificar permisos: `local/geniai:createcourse`
3. Revisar logs de Moodle
4. Ejecutar: `http://localhost/local/geniai/test_service.php`

### Servicios no registrados
1. Ejecutar: `php local/geniai/force_upgrade.php`
2. O ir a: `http://localhost/admin/index.php` y actualizar

---

## 10. LISTA COMPLETA DE ARCHIVOS MODIFICADOS

```
local/geniai/
├── version.php                          [MODIFICADO]
├── db/
│   ├── access.php                       [MODIFICADO]
│   └── services.php                     [MODIFICADO]
├── classes/external/
│   └── course_creator.php               [NUEVO]
├── amd/src/
│   └── chat.js                          [MODIFICADO]
├── amd/build/
│   └── chat.min.js                      [MODIFICADO]
├── lang/en/
│   └── local_geniai.php                 [MODIFICADO]
├── lang/es/
│   └── local_geniai.php                 [NUEVO]
├── styles/
│   └── _edulabs-colors.scss             [NUEVO]
├── styles.scss                          [MODIFICADO]
├── styles.css                           [MODIFICADO]
├── test_service.php                     [NUEVO - AUXILIAR]
├── force_upgrade.php                    [NUEVO - AUXILIAR]
└── docs/
    └── CAMBIOS_REALIZADOS.md            [NUEVO - ESTE ARCHIVO]
```

---

## 11. CRÉDITOS

- **Plugin Original:** Eduardo Kraus - [GeniAI](https://moodle.org/plugins/local_geniai)
- **Personalización:** Edu Labs Colombia
- **Versión:** 2.4.0-edulabs
- **Fecha:** Noviembre 2025

---

## 12. LICENCIA

GNU GPL v3 or later
