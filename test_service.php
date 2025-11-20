<?php
require_once('../../config.php');
require_login();

echo "<h1>Test Service Configuration</h1>";

// Verificar si el servicio existe
$service = $DB->get_record('external_functions', ['name' => 'local_geniai_create_course']);
echo "<h2>Service Registration:</h2>";
if ($service) {
    echo "<pre>";
    print_r($service);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ Service NOT registered in database</p>";
    echo "<p>Running fix...</p>";
    
    // Intentar registrar manualmente
    require_once($CFG->dirroot . '/local/geniai/db/services.php');
    echo "<pre>";
    print_r($functions);
    echo "</pre>";
}

// Verificar capacidad
echo "<h2>User Capabilities:</h2>";
$context = context_system::instance();
$has_view = has_capability('local/geniai:view', $context);
$has_manage = has_capability('local/geniai:manage', $context);
$has_create = has_capability('local/geniai:createcourse', $context);

echo "<ul>";
echo "<li>local/geniai:view = " . ($has_view ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>local/geniai:manage = " . ($has_manage ? "✅ YES" : "❌ NO") . "</li>";
echo "<li>local/geniai:createcourse = " . ($has_create ? "✅ YES" : "❌ NO") . "</li>";
echo "</ul>";

// Verificar si la clase existe
echo "<h2>Class Verification:</h2>";
if (class_exists('\\local_geniai\\external\\course_creator')) {
    echo "<p style='color: green;'>✅ Class exists</p>";
    
    // Verificar métodos
    $methods = get_class_methods('\\local_geniai\\external\\course_creator');
    echo "<p>Available methods:</p><ul>";
    foreach ($methods as $method) {
        echo "<li>$method</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ Class NOT found</p>";
}

// Test API Key
$apikey = get_config('local_geniai', 'apikey');
echo "<h2>API Key:</h2>";
if (!empty($apikey)) {
    echo "<p style='color: green;'>✅ API Key configured (" . substr($apikey, 0, 10) . "...)</p>";
} else {
    echo "<p style='color: red;'>❌ API Key NOT configured</p>";
}
