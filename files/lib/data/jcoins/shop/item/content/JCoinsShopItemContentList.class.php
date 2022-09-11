<?php
namespace wcf\data\jcoins\shop\item\content;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins shop item  contents.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemContentList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopItemContent::class;
}
