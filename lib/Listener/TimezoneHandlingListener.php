<?php
/**
 * @copyright Copyright (c) 2023 Julien Veyssier <julien-nc@posteo.net>
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\UserOIDC\Listener;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\UserOIDC\AppInfo\Application;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\ISession;
use OCP\IUserSession;
use OCP\Util;

class TimezoneHandlingListener implements IEventListener {

	/**
	 * @var IUserSession
	 */
	private $userSession;
	/**
	 * @var ISession
	 */
	private $session;
	/**
	 * @var IConfig
	 */
	private $config;

	public function __construct(IUserSession $userSession,
								ISession     $session,
								IConfig      $config) {
		$this->userSession = $userSession;
		$this->session = $session;
		$this->config = $config;
	}

	public function handle(Event $event): void {
		if (!$event instanceof LoadAdditionalScriptsEvent) {
			return;
		}

		if (!$this->userSession->isLoggedIn()) {
			return;
		}

		$user = $this->userSession->getUser();
		$timezoneDB = $this->config->getUserValue($user->getUID(), 'core', 'timezone');

		if ($timezoneDB === '' || !$this->session->exists('timezone')) {
			Util::addScript(Application::APP_ID, Application::APP_ID . '-timezone');
		}
	}
}
