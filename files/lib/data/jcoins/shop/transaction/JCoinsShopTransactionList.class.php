<?php 
namespace wcf\data\jcoins\shop\transaction;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins Shop transactions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopTransactionList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopTransaction::class;
}
