<?php 
namespace wcf\acp\page;
use wcf\data\jcoins\shop\transaction\JCoinsShopTransactionList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Shop Item list page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopTransactionListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.transaction.list';
	
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
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['transactionID', 'time', 'username', 'price', 'typeDes', 'detail', 'itemTitle'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = JCoinsShopTransactionList::class;
	
	/**
	 * filter
	 */
	public $username = '';
	public $itemTitle = '';
	public $typeDes = '';
	public $availableTypes = [];
	
	/**
	 * @inheritdoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
		if (isset($_REQUEST['itemTitle'])) $this->itemTitle = StringUtil::trim($_REQUEST['itemTitle']);
		if (!empty($_REQUEST['typeDes'])) $this->typeDes = $_REQUEST['typeDes'];
	}
	
	/**
	 * @inheritdoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if ($this->username) {
			$this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.username LIKE ?', ['%' . $this->username . '%']);
		}
		if ($this->itemTitle) {
			$this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.itemTitle LIKE ?', ['%' . $this->itemTitle . '%']);
		}
		
		if ($this->typeDes) {
			$this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.typeDes LIKE ?', ['%' . $this->typeDes . '%']);
		}
		
		// available types
		$this->availableTypes = [];
		$sql = "SELECT	DISTINCT	typeDes
				FROM				wcf".WCF_N."_jcoins_shop_transaction";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			if ($row['typeDes']) $this->availableTypes[$row['typeDes']] = WCF::getLanguage()->get('wcf.acp.jcoinsShop.item.' . $row['typeDes']);
		}
		ksort($this->availableTypes);
	}
	
	/**
	 * @inheritdoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'username' => $this->username,
				'itemTitle' => $this->itemTitle,
				'typeDes' => $this->typeDes,
				'availableTypes' => $this->availableTypes
		]);
	}
}
