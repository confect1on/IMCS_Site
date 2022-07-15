<?php

# CAS-server URL (with slash)
$CAS_CLIENT_CONFIG['server'] = "https://profile.sfu-kras.ru/cas/";

# force to re-enter login/password on login
$CAS_CLIENT_CONFIG['renew'] = FALSE;

# on logout also logout from CAS-server
$CAS_CLIENT_CONFIG['auto_logout'] = TRUE;

# deny change password/mail
$CAS_CLIENT_CONFIG['deny_change_password'] = TRUE;
$CAS_CLIENT_CONFIG['deny_change_mail'] = TRUE;
$CAS_CLIENT_CONFIG['deny_change_profile_fields'] = array('profile_fullname', 'profile_description', 'profile_organization');

# identify CAS-users by uid
$CAS_CLIENT_CONFIG['uid_required'] = TRUE;
$CAS_CLIENT_CONFIG['authname_format'] = 'uid:$profile->uid@cas';
# identify users by name (use only if CAS-users have permanent names)
#$CAS_CLIENT_CONFIG['uid_required'] = FALSE;
#$CAS_CLIENT_CONFIG['authname_format'] = 'name:$profile->name@cas';

# mail format
$CAS_CLIENT_CONFIG['mail_format'] = '$profile->mail';
#$CAS_CLIENT_CONFIG['mail_format'] = '$profile->name@cas';

# map receivied fields to user's fields (on first login)
$CAS_CLIENT_CONFIG['profile_fields'] = array(
	'fullname'    => 'profile_fullname',
	'organization' => 'profile_organization',
	'description' => 'profile_description',
);

# which fields update on next logins
$CAS_CLIENT_CONFIG['profile_fields_update'] = array(
	'name',
	'mail',
	'fullname',
	'organization',
	'description'
);

# which roles auto-assigns to new users
$CAS_CLIENT_CONFIG['profile_roles'] = array(
	//'administrator' => 3,
	//'employee' => 4,
	//'student' => 5,
);

# which roles update on next logins
$CAS_CLIENT_CONFIG['profile_roles_update'] = array(
	//'administrator',
	//'employee',
	//'student',
);
