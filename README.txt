Moodle Password Expiry

Introduction
For a customer of Stoas, a special Authentication Module has been developed to handle password expiry in Moodle 1.9 environments.

Installing
Place the code in the folder pwdexp in the Moodle auth folder. ({Moodle Root\auth\pwdexp)

Configuration
After installation the authentication module has to be enabled and configured.
Users - Authentication - Manage Authentication will now list the added module as: 'Password Expiration check'

Choose enable to activate the plugin.
Remark: Before the plugin is fully active, its settings need to be saved once.
The module will apply to all logged in users, regardless of the authentication plugin order.

On the 'Settings' page there are two configurable options:
1.	The amount of days for password expiration
2.	The URL to redirect to when the password has expired (ie Moodle Password change page)

Remark: After the activation of the plugin there might not be a password change history for all users. To set this history either force all users to change their passwords, or set the password update field in the database to reflect today’s timestamp value.
