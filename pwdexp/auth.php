<?php
/**
 *
 * @package    auth
 * @subpackage pwdexp
 * @copyright  2013 UP learning B.V.
 * @author     Anne Krijger & David Bezemer info@uplearning.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 * Authentication Plugin: Password Expire Authentication
 * 
 * Check if user has property auth_pwdexp_date set.
 * If not assume the password has expired
 * If date is set, check if it is today or earlier
 *  if so, password is expired
 * If Password is expired
 *  set new auth_pwdexp_date to today + #days as defined (default 30 days)
 *  force password reset and redirect to defined URL (default change password page)
 *   
 */

defined('MOODLE_INTERNAL') || die(); /// It must be included froma a Moodle page
define('PREF_FIELD_AUTH_PWDEXP_DATE', 'auth_pwdexp_date');

require_once($CFG->libdir.'/authlib.php');

/**
 * Password Expire authentication plugin.
 */
class auth_plugin_pwdexp extends auth_plugin_base {

    /**
     * Constructor.
     */
	const COMPONENT_NAME = 'auth_pwdexp';
	const LEGACY_COMPONENT_NAME = 'auth/pwdexp';
	public function __construct() {
		$this->authtype = 'pwdexp';
		$config = get_config(self::COMPONENT_NAME);
		$legacyconfig = get_config(self::LEGACY_COMPONENT_NAME);
		$this->config = (object)array_merge((array)$legacyconfig, (array)$config);
	}
	public function auth_plugin_pwdexp() {
		debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
		self::__construct();
	}

    /**
     * Returns false since username password is not checked yet.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
       return false;
    }

    /**
     * Post authentication hook.
     * This method is called from authenticate_user_login() for all enabled auth plugins.
     *
     * @param object $user user object, later used for $USER
     * @param string $username (with system magic quotes)
     * @param string $password plain text password (with system magic quotes)
     *
     * Hook is used to check if password needs to expire and if so
     * expired it and redirect to defined page (default new password page)
     * 
     */
    function user_authenticated_hook(&$user, $username, $password) {
    	$this->checkPasswordExpiration($user, $username, $password); 
    }
       
    /**
     * Password expiration check
     * Check if password needs to expire and if so
     * expired it and redirect to defined page (default new password page)
     *
     * @param object $user user object, later used for $USER
     * @param string $username (with system magic quotes)
     * @param string $password plain text password (with system magic quotes)
     * 
     */
    function checkPasswordExpiration(&$user, $username, $password) {
    	global $SESSION,$USER;
	$today = time();
	$expirationdays = $this->config->expirationdays;
	$defaultdate = mktime(0, 0, 0, date("m")  , (date("d") + $expirationdays+1), date("Y"));
	$defaultexp = mktime(0, 0, 0, date("m")  , (date("d") + $expirationdays), date("Y"));
        // default date to expiration days + 1 so if not found always allow the existing password
        $passwordExpDate = get_user_preferences(PREF_FIELD_AUTH_PWDEXP_DATE, $defaultdate, $user->id);
    	// If no settings found, set date and don't expire otherwise check date
	$passwordExpDate = get_user_preferences(PREF_FIELD_AUTH_PWDEXP_DATE, $defaultdate, $user->id);
	if ($passwordExpDate == $defaultdate) {
		set_user_preference(PREF_FIELD_AUTH_PWDEXP_DATE, $defaultexp, $user->id);
	}
        $passwordExpired = ((!empty($this->config->expirationdays)) && ($passwordExpDate <= $today));
        if ($passwordExpired && ($user->auth == 'manual')) {
        	$expirationdays = $this->config->expirationdays;
        	$redirecturl = $this->config->redirecturl; 
        	
        	// force new password
        	set_user_preference('auth_forcepasswordchange', 1, $user->id);
        	
        	// set new date
        	$newexpdate = mktime(0, 0, 0, date("m")  , (date("d") + $expirationdays), date("Y"));
        	set_user_preference(PREF_FIELD_AUTH_PWDEXP_DATE, $newexpdate, $user->id);
        	
        	// redirect when done
        	$SESSION->wantsurl = $redirecturl;
        }
    }
}
?>
