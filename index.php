<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/form/import_form.php');
require_once(__DIR__ . '/classes/helper.php');

admin_externalpage_setup('local_parentmanager_csv');

$PAGE->set_url(new moodle_url('/local/parentmanager/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_parentmanager'));
$PAGE->set_heading(get_string('import_nav', 'local_parentmanager'));

echo $OUTPUT->header();

$mform = new \local_parentmanager\form\import_form();

if ($data = $mform->get_data()) {
    $content = $mform->get_file_content('importfile');
    $lines = explode("\n", $content);
    $roleid = $data->roleid;

    echo $OUTPUT->box_start();
    
    $templatedata = [
        'title' => get_string('report', 'local_parentmanager'),
        'results' => []
    ];

    // --- CORRECTION N+1 : Phase 1 - On récupère tous les emails ---
    $allemails = [];
    $validlines = [];

    foreach ($lines as $i => $line) {
        $line = trim($line);
        if (empty($line) || ($i === 0 && stripos($line, '@') === false)) continue; 

        $sep = (strpos($line, ';') !== false) ? ';' : ',';
        $parts = str_getcsv($line, $sep);

        if (count($parts) >= 2) {
            $p_email = trim($parts[0]);
            $c_email = trim($parts[1]);
            
            $allemails[] = $p_email;
            $allemails[] = $c_email;
            $validlines[] = ['parent' => $p_email, 'child' => $c_email];
        }
    }

    // --- CORRECTION N+1 : Phase 2 - On requête Moodle 1 seule fois ---
    $usersbyemail = [];
    if (!empty($allemails)) {
        $allemails = array_unique($allemails);
        list($in, $params) = $DB->get_in_or_equal($allemails);
        
        $rs = $DB->get_recordset_select('user', "email $in AND deleted = 0", $params);
        foreach ($rs as $u) {
            $usersbyemail[strtolower($u->email)] = $u;
        }
        $rs->close(); // Ne jamais oublier de fermer un recordset
    }

    // --- Phase 3 - Création des liens en lisant la mémoire ---
    foreach ($validlines as $pair) {
        $p_email = strtolower($pair['parent']);
        $c_email = strtolower($pair['child']);

        if (isset($usersbyemail[$p_email]) && isset($usersbyemail[$c_email])) {
            $parent = $usersbyemail[$p_email];
            $child = $usersbyemail[$c_email];

            try {
                \local_parentmanager\helper::create_link($parent->id, $child->id, $roleid);
                
                $ok_obj = new \stdClass();
                $ok_obj->parent = fullname($parent);
                $ok_obj->child = fullname($child);
                
                $templatedata['results'][] = [
                    'is_success' => true,
                    'message' => get_string('import_ok', 'local_parentmanager', $ok_obj)
                ];
            } catch (Exception $e) {
                $err_obj = new \stdClass();
                $err_obj->email = $parent->email;
                $err_obj->msg = $e->getMessage();
                
                $templatedata['results'][] = [
                    'is_error' => true,
                    'message' => get_string('import_err', 'local_parentmanager', $err_obj)
                ];
            }
        } else {
            $nf_obj = new \stdClass();
            $nf_obj->p_email = $pair['parent'];
            $nf_obj->c_email = $pair['child'];
            
            $templatedata['results'][] = [
                'is_warning' => true,
                'message' => get_string('import_not_found', 'local_parentmanager', $nf_obj)
            ];
        }
    }
    
    echo $OUTPUT->render_from_template('local_parentmanager/action_results', $templatedata);
    echo $OUTPUT->continue_button(new moodle_url('/local/parentmanager/index.php'));
    echo $OUTPUT->box_end();
} else {
    $mform->display();
}
echo $OUTPUT->footer();
