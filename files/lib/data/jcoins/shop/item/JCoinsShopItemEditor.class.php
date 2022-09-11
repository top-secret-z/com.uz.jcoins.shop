<?php
namespace wcf\data\jcoins\shop\item;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Shop Items.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsShopItem::class;
	
	/**
	 * Updates category ids.
	 */
	public function updateCategoryIDs(array $categoryIDs = []) {
		// remove old assigns
		$sql = "DELETE FROM	wcf".WCF_N."_jcoins_shop_item_to_category
				WHERE		shopItemID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->shopItemID]);
		
		// assign new categories
		if (!empty($categoryIDs)) {
			WCF::getDB()->beginTransaction();
			
			$sql = "INSERT INTO	wcf".WCF_N."_jcoins_shop_item_to_category
						(categoryID, shopItemID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($categoryIDs as $categoryID) {
				$statement->execute([
						$categoryID,
						$this->shopItemID
				]);
			}
			
			WCF::getDB()->commitTransaction();
		}
	}
	
	/**
	 * update buyer to item
	 */
	public function updateItemToBuyer() {
		$sql = "INSERT INTO	wcf".WCF_N."_jcoins_shop_item_buyer
							(shopItemID, userID, buyDate, price)
				VALUES		(?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->shopItemID, WCF::getUser()->userID, TIME_NOW, $this->getPrice()]);
	}
}
