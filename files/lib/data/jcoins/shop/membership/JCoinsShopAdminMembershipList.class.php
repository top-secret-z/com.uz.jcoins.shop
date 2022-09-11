<?php
namespace wcf\data\jcoins\shop\membership;
use wcf\system\WCF;

/**
 * Represents a list of JCoins Shop Memberships for ACP.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopAdminMembershipList extends JCoinsShopMembershipList {
	/**
	 * Creates a new membership list object.
	 */
	public function __construct() {
		parent::__construct();
		
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "user_table.username, jcoins_shop_item.itemTitle, user_group.groupID, user_group.groupName";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user user_table ON (user_table.userID = jcoins_shop_membership.userID)";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_jcoins_shop_item jcoins_shop_item ON (jcoins_shop_item.shopItemID = jcoins_shop_membership.shopItemID)";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_user_group user_group ON (user_group.groupID = jcoins_shop_membership.groupID)";
	}
	
	/**
	 * @inheritDoc
	 */
	public function countObjects() {
		$sql = "SELECT	COUNT(*)
				FROM	wcf".WCF_N."_jcoins_shop_membership
				".$this->sqlConditionJoins;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		return $statement->fetchSingleColumn();
	}
}
