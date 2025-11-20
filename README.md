# EdulabsAI - Asistente ChatGPT para Moodle

## Descripción

**EdulabsAI** es un plugin personalizado de Moodle desarrollado por Edu Labs Colombia, basado en el plugin original GeniAI. Este asistente interactivo potenciado por inteligencia artificial está diseñado para mejorar la experiencia educacional en Moodle, ofreciendo:

- **Chat inteligente:** Responde preguntas sobre el funcionamiento de Moodle y contenido de cursos
- **Creación automática de cursos:** Genera cursos completos con IA, incluyendo contenido estructurado e imágenes generadas con DALL-E 3
- **Interfaz personalizada:** Colores corporativos de Edu Labs (morado, naranja, amarillo)
- **Completamente en español:** Traducción completa de la interfaz

El plugin facilita la comunicación entre estudiantes y la plataforma, promoviendo la aprendizaje autónoma y permitiendo a los profesores crear cursos de manera rápida y eficiente usando inteligencia artificial.

---

## 📚 Documentación Completa

Este plugin incluye documentación detallada en el directorio `docs/`:

- **[INSTALACION.md](docs/INSTALACION.md)** - Guía paso a paso para instalar y configurar el plugin
- **[CAMBIOS_REALIZADOS.md](docs/CAMBIOS_REALIZADOS.md)** - Documentación técnica de todas las modificaciones realizadas
- **[API_REFERENCE.md](docs/API_REFERENCE.md)** - Referencia completa de la API, servicios web y clases PHP

---

## ✨ Características Principales

### 1. Chat Inteligente
- Integración con OpenAI GPT-4 / GPT-4o-mini
- Respuestas contextuales sobre Moodle y cursos
- Historial de conversación persistente
- Interfaz flotante accesible desde cualquier página

### 2. Creación Automática de Cursos ⭐
- Genera cursos completos con un solo comando
- Contenido estructurado por semanas
- Imágenes únicas generadas con DALL-E 3
- Descripción HTML enriquecida
- Configuración automática de formato semanal

**Ejemplo de uso:**
```
Crear un curso sobre Python con 8 semanas
```

### 3. Personalización Edu Labs
- Colores corporativos aplicados en toda la interfaz
- Logo personalizado del asistente
- Nombre configurable (EdulabsAI por defecto)
- Traducción completa al español

---

## 🚀 Inicio Rápido

### Requisitos

- Moodle 3.10 o superior
- PHP 7.4 o superior
- Cuenta de OpenAI con API Key
- Créditos en OpenAI (mínimo $5 para GPT-4 y DALL-E 3)

### Instalación Básica

1. **Clonar o descargar el plugin:**
   ```bash
   cd /ruta/a/moodle/local
   git clone https://github.com/lokogam/geniai.git
   ```

2. **Actualizar base de datos:**
   ```
   http://tu-moodle.com/admin/index.php
   ```

3. **Configurar API Key:**
   - Ir a: Administración → Plugins → Plugins locales → EdulabsAI
   - Ingresar API Key de OpenAI
   - Guardar cambios

4. **Limpiar cachés:**
   ```bash
   php admin/cli/purge_caches.php
   ```

Para instrucciones detalladas, consulta **[INSTALACION.md](docs/INSTALACION.md)**.

---

## 📖 Cómo Usar

### Chat Básico

1. Ingresa a cualquier página de Moodle
2. Haz clic en el botón flotante morado en la esquina inferior derecha
3. Escribe tu pregunta y presiona Enter
4. El asistente responderá usando OpenAI

### Crear un Curso con IA

**Como profesor o administrador:**

1. Abre el chat de EdulabsAI
2. Escribe un comando como:
   ```
   Crear un curso sobre Inteligencia Artificial con 6 semanas
   ```
3. Espera 1-2 minutos mientras se genera el curso
4. Recibirás un enlace directo al curso creado

**Comandos válidos:**
- `Crear un curso sobre [tema] con [N] semanas`
- `Generar un curso de [tema] con [N] semanas`
- `Diseñar un curso sobre [tema] con [N] semanas`

---

## 🔑 Obtener API Key de OpenAI

1. Crear cuenta en [OpenAI](https://platform.openai.com/)
2. Ir a [API Keys](https://platform.openai.com/api-keys)
3. Crear nueva API Key
4. Copiar la clave (empieza con `sk-proj-...`)

### Acceso a GPT-4 y DALL-E 3

Para usar GPT-4 y DALL-E 3, debes:
1. Agregar al menos $5 de crédito a tu cuenta
2. Alcanzar el nivel de uso 1
3. [Más información sobre prepago](https://help.openai.com/en/articles/8264644-what-is-prepaid-billing)

**Modelos recomendados:**
- **gpt-4o-mini:** Más rápido y económico (recomendado para inicio)
- **gpt-4:** Mayor calidad, requiere prepago

---

## 💰 Costos Estimados

### Por Curso Generado (8 semanas)
- **GPT-4o-mini:** ~$0.01 - 0.02
- **DALL-E 3:** ~$0.36 (9 imágenes x $0.04)
- **Total:** ~$0.37 - 0.38 por curso

### Por Conversación de Chat
- **GPT-4o-mini:** ~$0.001 - 0.002 por mensaje

---

## 🛠️ Configuración Avanzada

### Parámetros de OpenAI

En la configuración del plugin puedes ajustar:

| Parámetro | Descripción | Rango | Por Defecto |
|-----------|-------------|-------|-------------|
| **Temperature** | Creatividad de respuestas | 0.0 - 1.0 | 0.7 |
| **Top P** | Diversidad de respuestas | 0.0 - 1.0 | 0.8 |
| **Max Tokens** | Longitud máxima de respuesta | 100 - 4000 | 2000 |

### Permisos y Capacidades

| Capacidad | Descripción | Roles |
|-----------|-------------|-------|
| `local/geniai:view` | Ver y usar el chat | Todos |
| `local/geniai:manage` | Administrar configuración | Profesores, Managers |
| `local/geniai:createcourse` | Crear cursos con IA | Profesores, Managers |

---

## 📸 Capturas de Pantalla

![captura-01](docs/capture/Screenshot%202025-11-20%20161853.png)

![captura-02](docs/capture/Screenshot%202025-11-20%20161910.png)

![captura-03](docs/capture/Screenshot%202025-11-20%20161930.png)

---

## 🔧 Solución de Problemas

### El chat no aparece
```bash
php admin/cli/purge_caches.php
```

### Error: "API Key not configured"
1. Ir a configuración del plugin
2. Ingresar API Key válida
3. Guardar y limpiar cachés

### Servicios web no registrados
```bash
cd local/geniai
php force_upgrade.php
```

Para más detalles, consulta **[INSTALACION.md](docs/INSTALACION.md)**.

---

## 📚 Documentación Técnica

### Para Desarrolladores

- **[API_REFERENCE.md](docs/API_REFERENCE.md)** - Referencia completa de:
  - Servicios web (local_geniai_chat, local_geniai_create_course)
  - Clases PHP y métodos
  - Integración con OpenAI
  - JavaScript API
  - Eventos y hooks

- **[CAMBIOS_REALIZADOS.md](docs/CAMBIOS_REALIZADOS.md)** - Incluye:
  - Lista completa de archivos modificados
  - Diagramas de flujo
  - Código fuente de nuevas funcionalidades
  - Guía de compilación de assets

---

## 🌐 Enlaces de Referencia

### OpenAI
- [Documentación de API](https://platform.openai.com/docs/)
- [API Keys](https://platform.openai.com/api-keys)
- [Modelos disponibles](https://platform.openai.com/docs/models)

### Moodle
- [Documentación de Desarrollo](https://docs.moodle.org/dev/)
- [Plugin Development](https://docs.moodle.org/dev/Plugin_development)
- [Web Services API](https://docs.moodle.org/dev/Web_services_API)
- [Local Plugins](https://docs.moodle.org/dev/Local_plugins)

### Plugin Original
- [GeniAI en Moodle.org](https://moodle.org/plugins/local_geniai)

---

## 📝 Versionamiento

- **Versión actual:** 2.4.0-edulabs
- **Fecha:** Noviembre 2025
- **Basado en:** GeniAI 2.3.7

### Historial de Versiones

- **2.4.0-edulabs** - Versión personalizada Edu Labs
  - Creación automática de cursos con IA
  - Traducción completa al español
  - Colores corporativos Edu Labs
  - Integración con DALL-E 3

- **2.3.7** - Versión original GeniAI
  - Chat básico con OpenAI
  - Soporte multiidioma

---

## 👥 Créditos

- **Plugin Original:** Eduardo Kraus - [GeniAI](https://moodle.org/plugins/local_geniai)
- **Personalización:** Edu Labs Colombia
- **Desarrollador:** [lokogam](https://github.com/lokogam)

---

## 📄 Licencia

GNU GPL v3 or later

---

## 🆘 Soporte

Para reportar problemas o solicitar ayuda:

1. Revisar la [documentación de instalación](docs/INSTALACION.md)
2. Ejecutar script de diagnóstico: `test_service.php`
3. Revisar logs de Moodle
4. Contactar al equipo de desarrollo de Edu Labs

---

**¿Listo para empezar?** Consulta la [Guía de Instalación](docs/INSTALACION.md) para configurar EdulabsAI en tu Moodle.

