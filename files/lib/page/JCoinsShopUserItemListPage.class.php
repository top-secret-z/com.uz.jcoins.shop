<?php
namespace wcf\page;
use wcf\data\jcoins\shop\item\JCoinsShopUserItemList;
use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * Shows a list of the items bought by user.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopUserItemListPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_JCOINS','MODULE_JCOINS_SHOP'];
	
	/**
	 * list of user items
	 */
	public $userItemList = [];
	public $allowedIDs = [];
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		// get user items
		$list = new JCoinsShopUserItemList();
		$list->readObjects();
		$this->userItemList = $list->getObjects();
		
		// viewable item list
		$list = new ViewableJCoinsShopItemList();
		$list->getConditionBuilder()->add("jcoins_shop_item.typeDes LIKE ?", ['membership']);
		$list->readObjectIDs();
		$this->allowedIDs = $list->getObjectIDs();
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'userItems' => $this->userItemList,
			'allowedIDs' => $this->allowedIDs
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function show() {
		// set active tab
		UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.settings.jCoinsShop');
		
		parent::show();
	}
}
