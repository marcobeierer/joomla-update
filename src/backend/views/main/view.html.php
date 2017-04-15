<?php
/*
 * @copyright  Copyright (C) 2016 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class UpdateViewMain extends JViewLegacy {
	function display($tmpl = null) {
		JToolbarHelper::title(JText::_('COM_UPDATE'));

		if (JFactory::getUser()->authorise('core.admin', 'com_update')) {
			JToolbarHelper::preferences('com_update');
		}

		$params = JComponentHelper::getParams('com_update');

		$this->accessKey = $params->get('access_key', '');
		$this->debugMode = $params->get('debug_mode', '0') === '1';

		$this->hasValidAccessKey = $this->accessKey && strlen($this->accessKey) >= 16;

		$this->phpVersionToOld = version_compare(PHP_VERSION, '5.6.0') === -1;

		$this->logData = $this->logData($this->debugMode);

		parent::display();
	}

	function logData($debugMode) {
		if (!$debugMode) {
			return false;
		}

		$config = JFactory::getConfig();

		$logPath = $config->get('log_path');
		$logsFilepath = $logPath . '/com_update.errors.php'; // also used in update.php and logs_controller.php

		if (!file_exists($logsFilepath)) {
			return false;
		}

		return file_get_contents($logsFilepath);
	}
}
