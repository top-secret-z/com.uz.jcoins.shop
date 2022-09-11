<?php
namespace wcf\data\jcoins\shop\item\content;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit JCoins shop item content.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemContentEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = JCoinsShopItemContent::class;
}
