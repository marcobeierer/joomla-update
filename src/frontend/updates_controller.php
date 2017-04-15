<?php
/*
 * @copyright  Copyright (C) 2016 - 2017 Marco Beierer. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

class UpdatesController extends JControllerLegacy {
	function __construct($properties = null) {
		parent::__construct($properties);
	}

	function __destruct() {
	}

	function getUpdates() {
		JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_installer/models', 'InstallerModel');
		$model = JModelLegacy::getInstance('Update', 'InstallerModel');

		$model->findUpdates();
		$items = $model->getItems();

		$updates = array();

		foreach ($items as $item) {
			$update = new stdClass;
			$update->ID = intval($item->update_id);
			$update->Title = $item->name; // TODO or title?
			$update->Description = $item->description;
			$update->Version = $item->version;
			$update->URL = $item->infourl;

			$updates[] = $update;
		}

		$jsonData = json_encode($updates); // TODO check for errors
		if ($jsonData === false) {
			JLog::add('could not encode as json: ' . $updates, JLog::ERROR, 'com_backup');
			throw new Exception(JText::_('COM_BACKUP_INTERNAL_SERVER_ERROR'), 500);
		}

		echo $jsonData;
		exit;
	}
}
