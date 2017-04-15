<?php
/*
 * @copyright  Copyright (C) 2016 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$input = $app->input;

$params = JComponentHelper::getParams('com_update');
$accessKey = $params->get('access_key', '');
$debugMode = $params->get('debug_mode', '0');

$logLevel = JLog::ALL;
if ($input->get('debug', '0') !== '1' && $debugMode !== '1') {
	$logLevel &= ~JLog::DEBUG;
}

JLog::addLogger(
	array(
		'text_file' => 'com_update.errors.php' // also used in logs_controller.php and view.html.php
	),
	$logLevel,
	array('com_update')
);

if (version_compare(PHP_VERSION, '5.6.0') === -1) {
	JLog::add('the component requires at least PHP 5.6.0');
	throw new Exception(JText::_('COM_UPDATE_INTERNAL_SERVER_ERROR'), 500);
}

$httpAuthorization = '';
if (isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] !== NULL) {
	$httpAuthorization = $_SERVER['HTTP_AUTHORIZATION'];
}
else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] !== NULL) {
	$httpAuthorization = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}
else {
	JLog::add('no HTTP_AUTHORIZATION header was provided', JLog::ERROR, 'com_update');
	throw new Exception(JText::_('COM_UPDATE_BAD_REQUEST'), 400);
}

$accessKeyRequest = preg_replace('/^Bearer /', '', $httpAuthorization);
if ($accessKeyRequest === NULL) {
	JLog::add('removal of Bearer failed, authorization value was: ' . $httpAuthorization, JLog::ERROR, 'com_update');
	throw new Exception(JText::_('COM_UPDATE_BAD_REQUEST'), 400);
}


// TODO check if access key is safe enough

if (!$accessKey || !$accessKeyRequest || strlen($accessKey) < 16 || $accessKey !== $accessKeyRequest) {
	throw new Exception(JText::_('COM_UPDATE_UNAUTHORIZED'), 401);
}

$view = $input->getWord('view', '');
$task = $input->getCmd('task', '');

if ($view == '' || $task == '') {
	JLog::add('view or task param had no value', JLog::ERROR, 'com_update');
	throw new Exception(JText::_('COM_UPDATE_BAD_REQUEST'), 400);
}

switch($view) {
case 'logs':
	require_once(JPATH_COMPONENT . '/logs_controller.php');
	$controller = JControllerLegacy::getInstance('Logs');
	break;
default:
	require_once(JPATH_COMPONENT . '/updates_controller.php');
	$controller = JControllerLegacy::getInstance('Updates');
}

$controller->execute($task);
$controller->redirect();
?>
