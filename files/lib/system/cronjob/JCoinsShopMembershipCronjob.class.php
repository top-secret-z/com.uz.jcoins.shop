<?php
namespace wcf\system\cronjob;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipAction;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipList;
use wcf\data\cronjob\Cronjob;

/**
 * Disables JCoins Shop membership when expired.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// revoke expired memberships
		$membershipList = new JCoinsShopMembershipList();
		$membershipList->getConditionBuilder()->add('isActive = ?', [1]);
		$membershipList->getConditionBuilder()->add('endDate > ? AND endDate < ?', [0, TIME_NOW]);
		$membershipList->readObjects();
		
		if (count($membershipList->getObjects())) {
			$action = new JCoinsShopMembershipAction($membershipList->getObjects(), 'revoke');
			$action->executeAction();
		}
		
		// warn before expiration
		$membershipList = new JCoinsShopMembershipList();
		$membershipList->getConditionBuilder()->add('isActive = ?', [1]);
		$membershipList->getConditionBuilder()->add('isWarned = ?', [0]);
		$membershipList->getConditionBuilder()->add('warnDate > ? AND warnDate < ?', [0, TIME_NOW]);
		$membershipList->readObjects();
 		
		if (count($membershipList->getObjects())) {
			$action = new JCoinsShopMembershipAction($membershipList->getObjects(), 'warn');
			$action->executeAction();
		}
	}
}
