<?php
defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {
	$settings->add(new admin_setting_heading('auth_pwdexp/pluginname',
	new lang_string('auth_server_settings', 'auth_pwdexp'),
	new lang_string('auth_pwdexpdescription', 'auth_pwdexp')));
	$expirationtimeoptions = array(
	'5' => new lang_string('numdays', '', 5),
	'30' => new lang_string('numdays', '', 30),
	'60' => new lang_string('numdays', '', 60),
        '90' => new lang_string('numdays', '', 90),
        '120' => new lang_string('numdays', '', 120),
	);
	$settings->add(new admin_setting_configselect('auth_pwdexp/expirationdays',
	new lang_string('auth_expirationdays_key', 'auth_pwdexp'),
	new lang_string('auth_expirationdays', 'auth_pwdexp'), 60, $expirationtimeoptions));
	$redirecturl = $CFG->httpswwwroot .'/login/change_password.php';
	$settings->add(new admin_setting_configtext('auth_pwdexp/redirecturl',
	new lang_string('auth_redirecturl_key', 'auth_pwdexp'),
	new lang_string('auth_redirecturl', 'auth_pwdexp'), $redirecturl));
}
