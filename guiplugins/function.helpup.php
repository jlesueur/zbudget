<?php


function smarty_function_helpup($params, &$smarty)
{
	$params['delay'] = 500;
	$params['hauto'] = true;
	$params['vauto'] = true;
	$params['snapx'] = 15;
	$params['snapy'] = 15;
	return smarty_function_popup($params, $smarty);
}