<?php 
namespace wcf\acp\page;
use wcf\data\jcoins\shop\membership\JCoinsShopAdminMembershipList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Shop membership list page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.membership.list';
	
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
	public $defaultSortField = 'membershipID';
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['membershipID', 'username', 'itemTitle', 'startDate', 'endDate', 'groupName', 'isActive'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = JCoinsShopAdminMembershipList::class;
	
	/**
	 * filter
	 */
	public $username = '';
	public $itemTitle = '';
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
		if (isset($_REQUEST['itemTitle'])) $this->itemTitle = StringUtil::trim($_REQUEST['itemTitle']);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if ($this->username) {
			$this->objectList->getConditionBuilder()->add('user_table.username LIKE ?', ['%' . $this->username . '%']);
		}
		if ($this->itemTitle) {
			$this->objectList->getConditionBuilder()->add('jcoins_shop_item.itemTitle LIKE ?', ['%' . $this->itemTitle . '%']);
		}
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'username' => $this->username,
				'itemTitle' => $this->itemTitle
		]);
	}
}
