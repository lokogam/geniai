# Guía de Instalación - Plugin EdulabsAI

## Requisitos Previos

- Moodle 3.10 o superior
- PHP 7.4 o superior
- Cuenta de OpenAI con acceso a API
- Créditos en cuenta de OpenAI (mínimo $5 para usar GPT-4 y DALL-E 3)

---

## Paso 1: Instalación del Plugin

### Opción A: Instalación Manual

1. **Descargar el plugin:**
   - Descarga el código desde el repositorio
   - Descomprime el archivo

2. **Copiar archivos:**
   ```bash
   cp -r local_geniai /ruta/a/moodle/local/geniai
   ```

3. **Establecer permisos:**
   ```bash
   cd /ruta/a/moodle/local/geniai
   chmod -R 755 .
   chown -R www-data:www-data .
   ```

### Opción B: Instalación desde Moodle

1. Ir a: **Administración del sitio** → **Plugins** → **Instalar plugins**
2. Subir el archivo ZIP del plugin
3. Hacer clic en **Instalar plugin desde archivo ZIP**

---

## Paso 2: Actualización de la Base de Datos

1. **Navegar a la página de notificaciones:**
   ```
   http://tu-moodle.com/admin/index.php
   ```

2. **Hacer clic en:**
   - "Actualizar base de datos de Moodle ahora"

3. **O ejecutar desde CLI:**
   ```bash
   cd /ruta/a/moodle
   php admin/cli/upgrade.php --non-interactive
   ```

---

## Paso 3: Configuración del Plugin

### 3.1. Configurar API Key de OpenAI

1. **Obtener API Key:**
   - Ir a: https://platform.openai.com/api-keys
   - Crear nueva API Key
   - Copiar la clave (empieza con `sk-proj-...`)

2. **Configurar en Moodle:**
   - Ir a: **Administración del sitio** → **Plugins** → **Plugins locales** → **EdulabsAI**
   - Pegar la API Key en el campo correspondiente
   - Guardar cambios

### 3.2. Configurar Modelo de OpenAI

**Opciones disponibles:**

- **gpt-4o-mini** (Recomendado para inicio)
  - Más rápido
  - Más económico
  - No requiere prepago
  
- **gpt-4** (Mayor calidad)
  - Más potente
  - Requiere prepago de $1
  - Respuestas más elaboradas

### 3.3. Configurar Nombre del Asistente

- Campo: "Nombre del Asistente"
- Valor sugerido: **EdulabsAI**

### 3.4. Cargar Foto del Agente (Opcional)

- Subir logo de Edu Labs
- Tamaño recomendado: 200x200 px
- Formato: PNG con fondo transparente

---

## Paso 4: Verificar Permisos

### 4.1. Verificar Capacidades

**Ejecutar script de diagnóstico:**
```
http://tu-moodle.com/local/geniai/test_service.php
```

**Debe mostrar:**
- ✅ local/geniai:view = YES
- ✅ local/geniai:manage = YES
- ✅ local/geniai:createcourse = YES

### 4.2. Asignar Permisos Manualmente (si es necesario)

1. Ir a: **Administración del sitio** → **Usuarios** → **Permisos** → **Definir roles**

2. **Para Estudiantes:**
   - Editar rol "Estudiante"
   - Buscar `local/geniai:view`
   - Marcar como **Permitir**

3. **Para Profesores:**
   - Editar rol "Profesor con permiso de edición"
   - Buscar `local/geniai:createcourse`
   - Marcar como **Permitir**

---

## Paso 5: Compilar Assets

### 5.1. Compilar SCSS

**Instalar SASS (si no está instalado):**
```bash
npm install -g sass
```

**Compilar estilos:**
```bash
cd /ruta/a/moodle/local/geniai
sass styles.scss styles.css --style compressed
```

### 5.2. Compilar JavaScript (Opcional)

Si tienes Grunt instalado:
```bash
npx grunt amd
```

Si no, copiar manualmente:
```bash
cd /ruta/a/moodle/local/geniai/amd
cp src/chat.js build/chat.min.js
```

---

## Paso 6: Limpiar Cachés

**Desde CLI:**
```bash
cd /ruta/a/moodle
php admin/cli/purge_caches.php
```

**Desde interfaz web:**
1. Ir a: **Administración del sitio** → **Desarrollo** → **Purgar todas las cachés**
2. Hacer clic en **Purgar todas las cachés**

---

## Paso 7: Verificación Final

### 7.1. Verificar que el Chat Aparezca

1. Iniciar sesión en Moodle
2. Ir a cualquier curso
3. Verificar que aparece el botón flotante en la esquina inferior derecha
4. Hacer clic para abrir el chat

### 7.2. Probar Funcionalidad Básica

**En el chat, escribir:**
```
Hola, ¿cómo estás?
```

**Debe responder:**
El asistente debe responder de manera coherente usando OpenAI.

### 7.3. Probar Creación de Cursos

**Como administrador o profesor, escribir:**
```
Crear un curso sobre Python con 8 semanas
```

**Debe mostrar:**
1. Mensaje de "Creando curso..."
2. Esperar 1-2 minutos
3. Mensaje de éxito con enlace al curso

---

## Paso 8: Configuración Avanzada (Opcional)

### 8.1. Activar Modo Debug

**Solo para desarrollo/pruebas:**
```bash
php admin/cli/cfg.php --name=debug --set=32767
php admin/cli/cfg.php --name=debugdisplay --set=1
```

### 8.2. Configurar Temperatura y Top_p

En la configuración del plugin:
- **Temperatura:** Controla la creatividad (0.0 - 1.0)
- **Top_p:** Controla la diversidad de respuestas (0.0 - 1.0)

Valores recomendados para chatbot:
- Temperatura: 0.5
- Top_p: 0.8

### 8.3. Configurar Módulos Ocultos

Seleccionar qué módulos de Moodle el asistente NO debe mencionar en ejercicios.

---

## Solución de Problemas Comunes

### El chat no aparece

**Solución 1:** Limpiar cachés
```bash
php admin/cli/purge_caches.php
```

**Solución 2:** Verificar permisos
```
http://tu-moodle.com/local/geniai/test_service.php
```

**Solución 3:** Verificar configuración
- Ir a configuración del plugin
- Verificar "Modo de Uso" = "GeniAI Tutor"

### Error: "API Key not configured"

**Solución:**
1. Ir a configuración del plugin
2. Ingresar API Key válida de OpenAI
3. Guardar cambios
4. Limpiar cachés

### Error al crear curso: "You do not have permission"

**Solución:**
```bash
cd /ruta/a/moodle/local/geniai
php force_upgrade.php
```

Luego limpiar cachés.

### Servicios web no registrados

**Solución:**
1. Ir a: `http://tu-moodle.com/admin/index.php`
2. Hacer clic en "Actualizar base de datos"

O ejecutar:
```bash
php force_upgrade.php
```

---

## Desinstalación

### Desde Moodle

1. Ir a: **Administración del sitio** → **Plugins** → **Resumen de plugins**
2. Buscar "EdulabsAI"
3. Hacer clic en "Desinstalar"
4. Confirmar

### Manual

```bash
# Eliminar archivos
rm -rf /ruta/a/moodle/local/geniai

# Limpiar base de datos
php admin/cli/uninstall_plugins.php --plugins=local_geniai --run

# Limpiar cachés
php admin/cli/purge_caches.php
```

---

## Soporte

Para reportar problemas o solicitar ayuda:

1. Revisar este documento
2. Ejecutar script de diagnóstico: `test_service.php`
3. Revisar logs de Moodle: **Administración → Informes → Registros**
4. Contactar al equipo de desarrollo de Edu Labs

---

## Recursos Adicionales

- [Documentación de OpenAI API](https://platform.openai.com/docs/)
- [Documentación de Moodle](https://docs.moodle.org/)
- [Plugin Original GeniAI](https://moodle.org/plugins/local_geniai)

---

**Versión:** 2.4.0-edulabs  
**Última actualización:** Noviembre 2025
