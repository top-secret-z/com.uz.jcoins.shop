<?php
namespace wcf\system\cronjob;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipEditor;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipList;
use wcf\data\cronjob\Cronjob;
use wcf\data\user\User;

/**
 * Disables JCoins Shop membership item when group is left.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipGroupCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// get active memberships
		$membershipList = new JCoinsShopMembershipList();
		$membershipList->getConditionBuilder()->add('isActive = ?', [1]);
		$membershipList->getConditionBuilder()->add('startDate < ? AND endDate > ?', [TIME_NOW, TIME_NOW]);
		$membershipList->readObjects();
		$memberships = $membershipList->getObjects();
		
		if (!count($memberships)) return;
		
		// check whether users are still in membership groups, 200 users
		$count = 0;
		foreach ($memberships as $membership) {
			$count ++;
			
			$user = new User($membership->userID);
			if (!$user->userID) continue;
			
			$groups = $user->getGroupIDs();
			if (!in_array($membership->groupID, $groups)) {
				$editor = new JCoinsShopMembershipEditor($membership);
				$editor->update([
						'isActive' => 0,
						'endDate' => TIME_NOW
				]);
			}
			
			if ($count > 200) break;
		}
	}
}
