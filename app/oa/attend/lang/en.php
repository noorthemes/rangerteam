<?php
if(!isset($lang->attend)) $lang->attend = new stdclass();
$lang->attend->common     = 'Attend';
$lang->attend->personal   = 'My Attend';
$lang->attend->department = 'Department attend';
$lang->attend->company    = 'Company attend';
$lang->attend->settings   = 'Setting';

$lang->attend->id       = 'ID';
$lang->attend->signIn   = 'Sign in';
$lang->attend->signOut  = 'Sign out';
$lang->attend->date     = 'Date';
$lang->attend->status   = 'Status';
$lang->attend->account  = 'User';
$lang->attend->extra    = 'Other info';
$lang->attend->dayName  = 'Day name';
$lang->attend->report   = 'Report';

$lang->attend->signInSuccess  = 'Sign in success';
$lang->attend->signOutSuccess = 'Sign out success';
$lang->attend->signInFail     = 'Sign in fail';
$lang->attend->signOutFail    = 'Sign out fail';

$lang->attend->latestSignInTime    = 'Latest time of sign in';
$lang->attend->earliestSignOutTime = 'Earlies time of sign out';
$lang->attend->workingDaysPerWeek  = 'Working days per week';
$lang->attend->forcedSignOut       = 'Must sign out';

$lang->attend->workingDaysPerWeekList['5'] = "Monday ~ Friday";
$lang->attend->workingDaysPerWeekList['6'] = "Monday ~ Saturday";

$lang->attend->forcedSignOutList['yes'] = 'need';
$lang->attend->forcedSignOutList['no']  = 'not need';

$lang->attend->weeks = array('First week', 'Second week', 'Third week', 'Fourth week', 'Fifth week');