<?php
namespace wcf\data\jcoins\shop\item\content;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes JCoins shop item content related actions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemContentAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsShopItemContentEditor::class;
}
