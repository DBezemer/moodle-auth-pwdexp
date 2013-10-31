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

/**
 * Version details
 *
 * @package    auth_pwdexp
 * @copyright  UP learning B.V. 2013 www.uplearning.nl
 * @author     Anne Krijger
 * @author     David Bezemer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 *
 *
 * Authentication Plugin: Password Expire Authentication
 * 
 * Check if user has property auth_pwdexp_date set.
 * If not assume the password has expired
 * If date is set, check if it is today or earlier
 *  - if so, password is expired
 * If Password is expired
 *  - set new auth_pwdexp_date to today + #days as defined (default 30 days)
 *  - force password reset and redirect to defined URL (default change password page)
 *   
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

define('PREF_FIELD_AUTH_PWDEXP_DATE', 'auth_pwdexp_date');

require_once($CFG->libdir.'/authlib.php');

/**
 * Password Expire authentication plugin.
 */
class auth_plugin_pwdexp extends auth_plugin_base {

    /**
     * Constructor.
     */
    function auth_plugin_pwdexp() {
        $this->authtype = 'pwdexp';
        $this->config = get_config('auth/pwdexp');
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
    	global $SESSION;
        $config = get_config('auth/pwdexp');
        $today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
        // default date to -1 so if not found always before today
        $passwordExpDate = get_user_preferences(PREF_FIELD_AUTH_PWDEXP_DATE, -1, $user->id);
    	// If not settings found don't expire otherwise check date
        $passwordExpired = (($config != null && $config !== false) && ($passwordExpDate <= $today));
        if ($passwordExpired) {
        	$expirationdays = $config->expirationdays;
        	$redirecturl = $config->redirecturl; 
        	
        	// force new password
        	set_user_preference('auth_forcepasswordchange', 1, $user->id);
        	
        	// set new date
        	$newexpdate = mktime(0, 0, 0, date("m")  , (date("d") + $expirationdays), date("Y"));
        	set_user_preference(PREF_FIELD_AUTH_PWDEXP_DATE, $newexpdate, $user->id);
        	
        	// redirect when done
        	$SESSION->wantsurl = $redirecturl;
        }
    }
    
    /**
     * Prints a form for configuring this authentication plugin.
     *
     * This function is called from admin/auth.php, and outputs a full page with
     * a form for configuring this plugin.
     *
     * @param array $page An object containing all the data for this page.
     */
    function config_form($config, $err, $user_fields) {
        include "config.html";
    }

    /**
     * Processes and stores configuration data for this authentication plugin.
     */
    function process_config($config) {
    	global $CFG;
        // set to defaults if undefined
        if (!isset ($config->expirationdays)) {
            $config->expirationdays = 30;
        }
        if (!isset ($config->redirecturl)) {
            $config->redirecturl = $CFG->httpswwwroot .'/login/change_password.php';
        }

        // save settings
        set_config('expirationdays', $config->expirationdays, 'auth/pwdexp');
        set_config('redirecturl', $config->redirecturl, 'auth/pwdexp');
        
        return true;
    }
}
?>