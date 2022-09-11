<?php
namespace wcf\data\jcoins\shop\membership;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of JCoins Shop Memberships.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopMembership::class;
}
