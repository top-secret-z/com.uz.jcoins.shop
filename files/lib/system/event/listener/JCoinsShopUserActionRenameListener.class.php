<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\AbstractUserActionRenameListener;

/**
 * Updates the stored username on user rename.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopUserActionRenameListener extends AbstractUserActionRenameListener {
	/**
	 * @inheritDoc
	 */
	protected $databaseTables = ['wcf{WCF_N}_jcoins_shop_transaction'];
}
