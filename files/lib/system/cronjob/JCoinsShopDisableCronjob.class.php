<?php
namespace wcf\system\cronjob;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\data\jcoins\shop\item\JCoinsShopItemList;
use wcf\data\cronjob\Cronjob;

/**
 * Disables JCoins Shop items if sold out / expired.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopDisableCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		// get affected products
		$itemList = new JCoinsShopItemList();
		$itemList->getConditionBuilder()->add('isDisabled = ?', [0]);
		$itemList->getConditionBuilder()->add('autoDisable = ?', [1]);
		$itemList->getConditionBuilder()->add('(productLimit > ? AND productLimit >= sold) OR (expirationStatus = ? AND expirationDate < ?)', [0, 1, TIME_NOW]);
		$itemList->readObjects();
		$items = $itemList->getObjects();
		
		if (!count($items)) return;
		
		// toggle
		$action = new JCoinsShopItemAction($items, 'toggle');
		$action->executeAction();
	}
}
