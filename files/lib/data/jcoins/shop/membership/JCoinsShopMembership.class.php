<?php 
namespace wcf\data\jcoins\shop\membership;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a JCoins Shop membership
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembership extends DatabaseObject{
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_shop_membership';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'membershipID';

}
