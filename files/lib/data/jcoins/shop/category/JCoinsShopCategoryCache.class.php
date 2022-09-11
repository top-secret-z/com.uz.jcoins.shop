<?php
namespace wcf\data\jcoins\shop\category;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the JCoins Shop category cache.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryCache extends SingletonFactory {
	/**
	 * number of total items
	 */
	protected $items;
	
	/**
	 * Calculates the number of items.
	 */
	protected function initItems() {
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('jcoins_shop_item.isDisabled = ?', [0]);
		
		$sql = "SELECT		COUNT(*) AS count, jcoins_shop_item_to_category.categoryID
				FROM		wcf".WCF_N."_jcoins_shop_item jcoins_shop_item
				LEFT JOIN	wcf".WCF_N."_jcoins_shop_item_to_category jcoins_shop_item_to_category
				ON			(jcoins_shop_item_to_category.shopItemID = jcoins_shop_item.shopItemID)
				".$conditionBuilder."
				GROUP BY	jcoins_shop_item_to_category.categoryID";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		$this->items = $statement->fetchMap('categoryID', 'count');
	}
	
	/**
	 * Return the number of items in the category with the given id.
	 */
	public function getItems($categoryID) {
		if ($this->items === null) {
			$this->initItems();
		}
		
		if (isset($this->items[$categoryID])) return $this->items[$categoryID];
		return 0;
	}
}
