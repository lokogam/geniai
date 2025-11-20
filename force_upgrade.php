<?php
define('CLI_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

echo "Forcing plugin upgrade...\n\n";

// Actualizar versión en DB
$plugin = new stdClass();
$plugin->version = 2025112001;
$plugin->component = 'local_geniai';

// Eliminar capacidades antiguas
echo "Removing old capabilities...\n";
$DB->delete_records('capabilities', ['component' => 'local_geniai']);

// Forzar actualización de capacidades
echo "Updating capabilities from access.php...\n";
require($CFG->dirroot . '/local/geniai/db/access.php');

foreach ($capabilities as $capname => $capinfo) {
    $cap = new stdClass();
    $cap->name = $capname;
    $cap->captype = $capinfo['captype'];
    $cap->contextlevel = $capinfo['contextlevel'];
    $cap->component = 'local_geniai';
    $cap->riskbitmask = isset($capinfo['riskbitmask']) ? $capinfo['riskbitmask'] : 0;
    
    echo "  - Adding capability: $capname\n";
    
    if ($existing = $DB->get_record('capabilities', ['name' => $capname])) {
        $cap->id = $existing->id;
        $DB->update_record('capabilities', $cap);
    } else {
        $DB->insert_record('capabilities', $cap);
    }
    
    // Asignar a los roles definidos
    if (isset($capinfo['archetypes'])) {
        foreach ($capinfo['archetypes'] as $archetype => $permission) {
            echo "    - Assigning to archetype: $archetype\n";
            
            // Obtener roles con ese arquetipo
            $roles = $DB->get_records('role', ['archetype' => $archetype]);
            foreach ($roles as $role) {
                $context = context_system::instance();
                assign_capability($capname, $permission, $role->id, $context->id, true);
            }
        }
    }
}

// Actualizar servicios externos
echo "\nUpdating external services...\n";
require($CFG->dirroot . '/local/geniai/db/services.php');

foreach ($functions as $functionname => $functiondata) {
    echo "  - Checking function: $functionname\n";
    
    if ($existing = $DB->get_record('external_functions', ['name' => $functionname])) {
        echo "    - Already exists, updating...\n";
        $existing->classname = $functiondata['classname'];
        $existing->methodname = $functiondata['methodname'];
        $existing->classpath = isset($functiondata['classpath']) ? $functiondata['classpath'] : null;
        $existing->description = $functiondata['description'];
        $existing->type = $functiondata['type'];
        $existing->capabilities = isset($functiondata['capabilities']) ? $functiondata['capabilities'] : '';
        $DB->update_record('external_functions', $existing);
    } else {
        echo "    - Creating new function...\n";
        $function = new stdClass();
        $function->name = $functionname;
        $function->classname = $functiondata['classname'];
        $function->methodname = $functiondata['methodname'];
        $function->classpath = isset($functiondata['classpath']) ? $functiondata['classpath'] : null;
        $function->component = 'local_geniai';
        $function->description = $functiondata['description'];
        $function->type = $functiondata['type'];
        $function->capabilities = isset($functiondata['capabilities']) ? $functiondata['capabilities'] : '';
        $DB->insert_record('external_functions', $function);
    }
}

// Actualizar versión del plugin
echo "\nUpdating plugin version in database...\n";
set_config('version', 2025112001, 'local_geniai');

// Limpiar caché
echo "\nPurging caches...\n";
purge_all_caches();

echo "\n✅ Done! Plugin upgraded successfully.\n";
echo "Please refresh the test page: http://localhost/local/geniai/test_service.php\n";
