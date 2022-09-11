<?php
namespace wcf\data\jcoins\shop\item;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins Shop Items.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemList extends DatabaseObjectList {
	/**
	 * enables/disables the loading of categories
	 */
	protected $categoryLoading = true;
	
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopItem::class;
	
	/**
	 * Enables/disables the loading of categories.
	 */
	public function enableCategoryLoading($enable = true) {
		$this->categoryLoading = $enable;
	}
}
