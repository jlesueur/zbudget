<?php

function setLoggedInUser($user)
{
	global $sGlobals;
	$sGlobals->userId = $user->getId();
}

function getLoggedInUser()
{
	requireLoggedIn();
	global $sGlobals;
	$user = new user($sGlobals->userId);
	return $user;
}

function isLoggedIn()
{
	global $sGlobals;
	return isset($sGlobals->userId);
}

function requireLoggedIn()
{
	RequireCondition(isLoggedIn());
}

function initLoggedInUser()
{
	$user = getLoggedInUser();
	if(!$user->isInitDone())
	{
		BaseRedirect(zone_register::makePath() . '/initAccounts');
	}
}
