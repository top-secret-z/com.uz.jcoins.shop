<?php
namespace wcf\data\jcoins\shop\item\type;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins shop item types
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemTypeList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopItemType::class;
	
	/**
	 * @inheritDoc
	 */
	public $sqlOrderBy = 'sortOrder ASC';
}
