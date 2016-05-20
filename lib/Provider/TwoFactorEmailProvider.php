<?php

/**
 * @author Christoph Wurst <christoph@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactor_Email\Provider;

use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\IUser;
use OCP\Template;

class TwoFactorEmailProvider implements IProvider {

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId() {
		return 'email';
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName() {
		// TODO: L10N
		return 'Email';
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription() {
		// TODO: L10N
		return 'Get a token via e-mail';
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user) {
		return new Template('twofactor_email', 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge) {
		if ($challenge === 'passme') {
			return true;
		}
		return false;
	}

	/**
	 * Decides whether 2FA is enabled for the given user
	 *
	 * @param IUser $user
	 * @return boolean
	 */
	public function isTwoFactorAuthEnabledForUser(IUser $user) {
		// 2FA is enforced for all users
		return true;
	}

}
