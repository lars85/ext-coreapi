<?php
namespace Etobi\CoreAPI\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel <helmut.hummel@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * API Command Controller
 */
class BackendApiCommandController extends CommandController {

	/**
	 * @var \TYPO3\CMS\Core\Log\LogManager $logManager
	 */
	protected $logManager;

	/**
	 * @var \TYPO3\CMS\Core\Log\Logger $logger
	 */
	protected $logger;

	/**
	 * @param \TYPO3\CMS\Core\Log\LogManager $logManager
	 *
	 * @return void
	 */
	public function injectLogManager(\TYPO3\CMS\Core\Log\LogManager $logManager) {
		$this->logManager = $logManager;
	}

	/**
	 * Initialize the object
	 */
	public function initializeObject() {
		$this->logger = $this->objectManager->get('\TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
	}

	/**
	 * Locks backend access for all users by writing a lock file that is checked when the backend is accessed.
	 *
	 * @param string $redirectUrl URL to redirect to when the backend is accessed
	 */
	public function lockCommand($redirectUrl = NULL) {
		if (@is_file((PATH_typo3conf . 'LOCK_BACKEND'))) {
			$message = 'A lockfile already exists. Overwriting it...';
			$this->outputLine($message);
			$this->logger->info($message);
		}

		\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile(PATH_typo3conf . 'LOCK_BACKEND', (string)$redirectUrl);

		if ($redirectUrl === NULL) {
			$message = 'Wrote lock file to \'typo3conf/LOCK_BACKEND\'';
			$this->outputLine($message);
			$this->logger->info($message);
		} else {
			$message = 'Wrote lock file to \'typo3conf/LOCK_BACKEND\' with instruction to redirect to: \'' . $redirectUrl . '\'';
			$this->outputLine($message);
			$this->logger->info($message);
		}
	}

	/**
	 * Unlocks the backend access by deleting the lock file
	 */
	public function unlockCommand() {
		if (@is_file((PATH_typo3conf . 'LOCK_BACKEND'))) {
			unlink(PATH_typo3conf . 'LOCK_BACKEND');
			if (@is_file((PATH_typo3conf . 'LOCK_BACKEND'))) {
				$message = 'ERROR: Could not remove lock file \'typo3conf/LOCK_BACKEND\'!';
				$this->outputLine($message);
				$this->logger->error($message);
				$this->quit(1);
			} else {
				$message = 'Removed lock file \'typo3conf/LOCK_BACKEND\'';
				$this->outputLine($message);
				$this->logger->info($message);
			}
		} else {
			$message = 'No lock file \'typo3conf/LOCK_BACKEND\' was found, hence no lock could be removed.';
			$this->outputLine($message);
			$this->logger->info($message);
			$this->quit(1);
		}
	}
}