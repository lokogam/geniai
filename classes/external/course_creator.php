<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_geniai\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

/**
 * Class course_creator
 *
 * @package    local_geniai
 * @copyright  2025 Edu Labs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_creator extends external_api {

        public static function create_course_parameters() {
        return new external_function_parameters([
            'topic' => new external_value(PARAM_TEXT, 'Course topic'),
            'weeks' => new external_value(PARAM_INT, 'Number of weeks'),
            'description' => new external_value(PARAM_TEXT, 'Brief description'),
        ]);
    }

    public static function create_course($topic, $weeks, $description) {
        global $USER, $DB, $CFG;

        // Validar parámetros
        $params = self::validate_parameters(self::create_course_parameters(), [
            'topic' => $topic,
            'weeks' => $weeks,
            'description' => $description,
        ]);

        // Verificar contexto y permisos
        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('local/geniai:createcourse', $context);

        // Obtener API key
        $apikey = get_config('local_geniai', 'apikey');
        if (empty($apikey)) {
            throw new \moodle_exception('noapikey', 'local_geniai');
        }

        // Generar contenido con OpenAI
        $coursedata = self::generate_course_content($apikey, $topic, $weeks, $description);

        // Crear curso en Moodle
        $courseid = self::create_moodle_course($coursedata);

        return [
            'success' => true,
            'courseid' => $courseid,
            'coursename' => $coursedata['coursename'],
            'courseurl' => (new \moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
            'message' => get_string('course_created_success', 'local_geniai'),
        ];
    }

    private static function generate_course_content($apikey, $topic, $weeks, $description) {
        $model = get_config('local_geniai', 'model') ?: 'gpt-4o-mini';

        $prompt = "Crea un curso educativo completo en español sobre '{$topic}' con exactamente {$weeks} semanas de duración.

Descripción del curso: {$description}

IMPORTANTE: Devuelve ÚNICAMENTE un objeto JSON válido con esta estructura exacta (sin texto adicional antes o después):

{
    \"coursename\": \"Nombre atractivo del curso (máximo 100 caracteres)\",
    \"description\": \"<p>Descripción detallada del curso en HTML con al menos 3 párrafos explicando: objetivos, metodología y beneficios</p>\",
    \"image_prompt\": \"Descripción en inglés para generar una imagen del curso (máximo 500 caracteres)\",
    \"weeks\": [
        {
            \"number\": 1,
            \"name\": \"Introducción al tema\",
            \"description\": \"<p>Descripción detallada de la primera semana de introducción</p>\",
            \"image_prompt\": \"English description for week image\"
        },
        {
            \"number\": 2,
            \"name\": \"Tema de la semana 2\",
            \"description\": \"<p>Descripción detallada</p>\",
            \"image_prompt\": \"English description for week image\"
        }
    ]
}

REGLAS OBLIGATORIAS:
- La semana 1 SIEMPRE debe ser de introducción
- La semana {$weeks} SIEMPRE debe ser de cierre/conclusión
- Cada semana debe tener un nombre descriptivo y una descripción en HTML
- La descripción del curso debe estar en formato HTML con etiquetas <p>
- IMPORTANTE: Incluye 'image_prompt' para el curso y para cada semana (en inglés)
- Los prompts de imagen deben ser descriptivos y educativos
- Devuelve SOLO el JSON, sin explicaciones adicionales";

        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Eres un experto diseñador instruccional que crea contenido educativo estructurado. Respondes ÚNICAMENTE en formato JSON válido, sin texto adicional.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apikey,
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            throw new \moodle_exception('openai_error', 'local_geniai', '', 'HTTP ' . $httpcode);
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            throw new \moodle_exception('openai_error', 'local_geniai', '', $result['error']['message']);
        }

        $content = json_decode($result['choices'][0]['message']['content'], true);
        
        if (!$content || !isset($content['coursename']) || !isset($content['weeks'])) {
            throw new \moodle_exception('invalid_response', 'local_geniai');
        }

        // Generar imágenes con DALL-E
        if (isset($content['image_prompt'])) {
            $content['course_image_url'] = self::generate_image($apikey, $content['image_prompt']);
        }

        foreach ($content['weeks'] as $index => &$week) {
            if (isset($week['image_prompt'])) {
                $week['image_url'] = self::generate_image($apikey, $week['image_prompt']);
            }
        }

        return $content;
    }

    private static function generate_image($apikey, $prompt) {
        $data = [
            'model' => 'dall-e-3',
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'quality' => 'standard',
        ];

        $ch = curl_init('https://api.openai.com/v1/images/generations');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apikey,
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            // Si falla la generación de imagen, continuar sin ella
            return null;
        }

        $result = json_decode($response, true);
        
        if (isset($result['data'][0]['url'])) {
            return $result['data'][0]['url'];
        }

        return null;
    }

    private static function create_moodle_course($coursedata) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');

        // Preparar datos del curso
        $course = new \stdClass();
        $course->fullname = $coursedata['coursename'];
        $course->shortname = 'CURSO' . time();
        $course->category = 1;
        $course->summary = $coursedata['description'];
        $course->summaryformat = FORMAT_HTML;
        $course->format = 'weeks';
        $course->numsections = count($coursedata['weeks']);
        $course->visible = 1;
        $course->startdate = time();

        // Crear el curso
        $newcourse = create_course($course);

        // Descargar y asignar imagen del curso si existe
        if (isset($coursedata['course_image_url'])) {
            self::download_and_set_course_image($newcourse->id, $coursedata['course_image_url']);
        }

        // Actualizar las secciones con los nombres y descripciones
        foreach ($coursedata['weeks'] as $index => $week) {
            $section = $DB->get_record('course_sections', [
                'course' => $newcourse->id,
                'section' => $index
            ]);

            if ($section) {
                $section->name = $week['name'];
                $section->summary = $week['description'];
                $section->summaryformat = FORMAT_HTML;
                
                // Agregar imagen a la descripción de la semana si existe
                if (isset($week['image_url'])) {
                    $imagehtml = '<div style="text-align: center; margin: 20px 0;">' .
                                '<img src="' . $week['image_url'] . '" alt="' . htmlspecialchars($week['name']) . '" ' .
                                'style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">' .
                                '</div>';
                    $section->summary = $imagehtml . $section->summary;
                }
                
                $DB->update_record('course_sections', $section);
            }
        }

        // Reconstruir caché del curso
        rebuild_course_cache($newcourse->id);

        return $newcourse->id;
    }

    private static function download_and_set_course_image($courseid, $imageurl) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        try {
            // Descargar imagen
            $imagedata = file_get_contents($imageurl);
            if ($imagedata === false) {
                return false;
            }

            // Guardar temporalmente
            $tempfile = tempnam(sys_get_temp_dir(), 'course_image_');
            file_put_contents($tempfile, $imagedata);

            // Obtener contexto del curso
            $context = \context_course::instance($courseid);
            $fs = get_file_storage();

            // Preparar archivo
            $filerecord = [
                'contextid' => $context->id,
                'component' => 'course',
                'filearea' => 'overviewfiles',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => 'course_image_' . time() . '.png',
            ];

            // Eliminar imágenes anteriores
            $fs->delete_area_files($context->id, 'course', 'overviewfiles');

            // Guardar nueva imagen
            $fs->create_file_from_pathname($filerecord, $tempfile);

            // Limpiar archivo temporal
            unlink($tempfile);

            return true;
        } catch (\Exception $e) {
            // Si falla, continuar sin imagen
            return false;
        }
    }

    public static function create_course_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'coursename' => new external_value(PARAM_TEXT, 'Course name'),
            'courseurl' => new external_value(PARAM_URL, 'Course URL'),
            'message' => new external_value(PARAM_TEXT, 'Success message'),
        ]);
    }
}
