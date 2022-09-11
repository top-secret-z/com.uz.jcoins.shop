<?php
namespace wcf\data\jcoins\shop\category;
use wcf\data\category\CategoryNode;

/**
 * Represents a JCoins Shop category node.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryNode extends CategoryNode {
	/**
	 * number of items in the category
	 */
	protected $items;
	
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = JCoinsShopCategory::class;
	
	/**
	 * Returns number of items in the category.
	 */
	public function getItems() {
		if ($this->items === null) {
			$this->items = JCoinsShopCategoryCache::getInstance()->getItems($this->categoryID);
		}
		
		return $this->items;
	}
}
