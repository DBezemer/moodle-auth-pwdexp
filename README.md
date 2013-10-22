moodle-auth-pwdexp
==================

A Moodle Auth plugin that handles password expiry

Introduction
For a customer of UP learning, a special Authentication Module has been developed to handle password expiry in Moodle environments.

Installing
Place the code in the folder pwdexp in the Moodle auth folder. ($CFG->dataroot\auth\pwdexp)

Configuration
After installation the authentication module has to be enabled and configured.
Users - Authentication - Manage Authentication will now list the added module as: 'Password Expiration check'

Choose enable to activate the plugin.
Remark: Before the plugin is fully active, its settings need to be saved once.
The module will apply to all logged in users, regardless of the authentication plugin order.

On the 'Settings' page there are two configurable options:
1.	The amount of days for password expiration
2.	The URL to redirect to when the password has expired (ie Moodle Password change page)

Remark: After the activation of the plugin there might not be a password change history for all users. 
For this reason the plugin will enforce a password change for every user logging in after activating the plugin directly, and not after the first interval.
