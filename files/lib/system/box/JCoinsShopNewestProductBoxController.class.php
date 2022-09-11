<?php
namespace wcf\system\box;
use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\box\AbstractBoxController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows newest shop items.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopNewestProductBoxController extends AbstractBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('JCoinsShop', [], '&sortField=changeTime&sortOrder=DESC&pageNo=0');
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		// module and permission
		if (!MODULE_JCOINS || !MODULE_JCOINS_SHOP) return;
		if (!WCF::getSession()->getPermission('user.jcoins.canShop') && !WCF::getSession()->getPermission('user.jcoins.canSeeShop')) return;
		
		$itemList = new ViewableJCoinsShopItemList();
		$itemList->sqlOrderBy = 'changeTime DESC';
		$itemList->sqlLimit = JCOINS_SHOP_ITEMS_PER_BOX;
		$itemList->readObjects();
		
		if (count($itemList)) {
			WCF::getTPL()->assign([
					'shopItems' => $itemList
			]);
			
			$this->content = WCF::getTPL()->fetch('boxJCoinsShop');
		}
	}
}
