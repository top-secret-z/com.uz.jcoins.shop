<?php
namespace wcf\data\jcoins\shop\item\type;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit JCoins shop item types.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemTypeEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsShopItemType::class;
}
