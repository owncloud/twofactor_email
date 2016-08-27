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

use Base32\Base32;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\IConfig;
use OCP\ISession;
use OCP\IUser;
use OCP\Template;
use Otp\GoogleAuthenticator;
use Otp\Otp;

class TwoFactorEmailProvider implements IProvider {

	/** @var ISession */
	private $session;
	
	/** @var IConfig */
	private $config;
	
	/**
	 * @param ISession $session
	 * @param IConfig $config
	 */
	public function __construct(ISession $session, IConfig $config) {
		$this->session = $session;
		$this->config = $config;
	}

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
		return 'Email';
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Authenticate with E-mail';
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user) {
		$otp = new Otp();
		$secret = GoogleAuthenticator::generateRandom();
		$this->session->set('twofactor_email_secret', $secret);
		$totp = (string)$otp->totp(Base32::decode($secret));
		$email = $user->getEMailAddress();
		try {
			$mailer = \OC::$server->getMailer();
			$message = $mailer->createMessage();
			$message->setSubject('Two-step verification code');
			$message->setTo([$email => $user->getDisplayName()]);
			$message->setPlainBody($totp);
			$mailer->send($message);
		} catch (Exception $e) {
			$tmpl = new Template('twofactor_email', 'error');
			return $tmpl;
		}
		
		return new Template('twofactor_email', 'challenge');
	}

	/**
	 * Verify the given challenge
	 *
	 * @param IUser $user
	 * @param string $challenge
	 */
	public function verifyChallenge(IUser $user, $challenge) {
		$otp = new Otp();
		$secret = $this->session->get('twofactor_email_secret');
		return $otp->checkTotp(Base32::decode($secret), $challenge);
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
