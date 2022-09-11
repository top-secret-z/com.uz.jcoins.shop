<?php
namespace wcf\data\jcoins\shop\membership;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Shop memberships.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsShopMembership::class;
}
