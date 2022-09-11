<?php 
namespace wcf\data\jcoins\shop\transaction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes JCoins Shop transaction actions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopTransactionAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsShopTransactionEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.shopJCoins.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.shopJCoins.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update'];
	
}
