<?php 
namespace wcf\acp\page;
use wcf\data\jcoins\shop\item\JCoinsShopItemList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * Shows the JCoins Shop Item list page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.item.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.shopJCoins.canManage'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_JCOINS_SHOP'];
	
	/**
	 * number of items shown per page
	 */
	public $itemsPerPage = 20;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'shopItemID';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['shopItemID', 'isDisabled', 'sortOrder', 'itemTitle', 'typeDes', 'price', 'sold', 'earnings'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = JCoinsShopItemList::class;
}
