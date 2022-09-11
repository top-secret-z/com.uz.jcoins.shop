<?php
namespace wcf\data\jcoins\shop\item;
use wcf\data\jcoins\shop\category\JCoinsShopCategory;
use wcf\system\WCF;

/**
 * Represents a list of viewable JCoins Shop Items.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class ViewableJCoinsShopItemList extends JCoinsShopItemList {
	public function __construct() {
		// all items
		$itemList = new JCoinsShopItemList();
		$itemList->readObjects();
		$items = $itemList->getObjects();
		
		// item group conditions
		$groupItemIDs = [];
		if (count($items)) {
			foreach ($items as $item) {
				$conditions = $item->getConditions();
				foreach ($conditions as $condition) {
					if (!$condition->getObjectType()->getProcessor()->showContent($condition)) {
						continue 2;
					}
				}
				
				$groupItemIDs[] = $item->shopItemID;
			}
		}
		
		// categories
		$accessibleCategoryIDs = JCoinsShopCategory::getAccessibleCategoryIDs();
		
		parent::__construct();
		
		if (!WCF::getSession()->getPermission('admin.shopJCoins.canManage')) {
			$this->getConditionBuilder()->add("jcoins_shop_item.isDisabled = ?", [0]);
		}
		
		// item groups
		if (count($groupItemIDs)) {
			$this->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (?)", [$groupItemIDs]);
		}
		else {
			$this->getConditionBuilder()->add('1=0');
		}
		
		// categories
		if (empty($accessibleCategoryIDs)) {
			$this->getConditionBuilder()->add('1=0');
		}
		else {
			$this->getConditionBuilder()->add('jcoins_shop_item.shopItemID IN (SELECT shopItemID FROM wcf'.WCF_N.'_jcoins_shop_item_to_category WHERE categoryID IN (?))', [$accessibleCategoryIDs]);
		}
	}
}
