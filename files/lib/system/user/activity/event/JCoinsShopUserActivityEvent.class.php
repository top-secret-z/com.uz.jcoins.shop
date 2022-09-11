<?php
namespace wcf\system\user\activity\event;
use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for a shop item purchase.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @inheritDoc
	 */
	public function prepare(array $events) {
		$objectIDs = [];
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// fetch items
		$itemList = new ViewableJCoinsShopItemList();
		$itemList->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (?)", [$objectIDs]);
		$itemList->readObjects();
		$items = $itemList->getObjects();
		
		// set message
		foreach ($events as $event) {
			if (isset($items[$event->objectID])) {
				$item = $items[$event->objectID];
				
				// check permissions
				if (!$item->canSee()) {
					continue;
				}
				$event->setIsAccessible();
				
				// title and description
				$text = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.shop.recentActivity.purchase', ['user' => WCF::getUser()]);
				$event->setTitle($text);
				$event->setDescription('');
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
