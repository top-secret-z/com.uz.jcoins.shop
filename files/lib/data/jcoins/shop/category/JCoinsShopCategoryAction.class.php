<?php
namespace wcf\data\jcoins\shop\category;
use wcf\data\category\Category;
use wcf\data\category\CategoryEditor;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes JCoins Shop-related actions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = CategoryEditor::class;
}
