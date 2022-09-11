<?php
namespace wcf\acp\page;
use wcf\acp\page\AbstractCategoryListPage;

/**
 * Shows the JCoins Shop category list page
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryListPage extends AbstractCategoryListPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.category.list';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'com.uz.jcoins.shop.category';
}
