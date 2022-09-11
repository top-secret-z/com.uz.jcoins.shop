<?php 
namespace wcf\data\jcoins\shop\transaction;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a JCoins Shop transaction
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopTransaction extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_shop_transaction';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'transactionID';
}
