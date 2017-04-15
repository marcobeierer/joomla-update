<?php
/*
 * @copyright  Copyright (C) 2016 - 2017 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

function UpdateParseRoute($segments) {
	$vars = array();
	$count = count($segments);
	$method = $_SERVER['REQUEST_METHOD'];

	$view = $segments[0];
	$filename = '';

	if ($view == 'updates') {
		$vars['view'] = $view;

		switch($method) {
		case 'GET':
			$vars['task'] = 'getUpdates';
			break;
		default:
			unset($vars['view']);
		}
	}

	$vars['format'] = 'raw';
	return $vars;
}
