<?php 
namespace wcf\data\jcoins\shop\item\type;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a JCoins shop item type.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemType extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_shop_item_type';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'id';
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return WCF::getLanguage()->get('wcf.acp.jcoinsShop.item.' . $this->typeTitle);
	}
	
	/**
	 * return type with given typeID
	 */
	public static function getTypeByID($typeID) {
		$sql = "SELECT	*
				FROM 	wcf".WCF_N."_jcoins_shop_item_type
				WHERE	typeID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$typeID]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new JCoinsShopItemType(null, $row);
	}
}
