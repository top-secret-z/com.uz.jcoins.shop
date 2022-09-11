<?php
namespace wcf\data\jcoins\shop\item;
use wcf\system\WCF;

/**
 * Represents a list of bought JCoins Shop Items.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopUserItemList extends JCoinsShopItemList {
	/**
	 * Creates a new item list object.
	 */
	public function __construct() {
		parent::__construct();
		
		$this->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (SELECT shopItemID FROM wcf".WCF_N."_jcoins_shop_item_buyer WHERE userID = ?)", [WCF::getUser()->userID]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readObjects() {
		parent::readObjects();
		
		// get endDate manually
		foreach ($this->objects as $item) {
			if ($item->typeDes == 'membership') {
				$sql = "SELECT	endDate
						FROM	wcf".WCF_N."_jcoins_shop_membership
						WHERE	userID = ? AND shopItemID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute([WCF::getUser()->userID, $item->shopItemID]);
				$item->endDate = $statement->fetchColumn();
			}
		}
	}
}
