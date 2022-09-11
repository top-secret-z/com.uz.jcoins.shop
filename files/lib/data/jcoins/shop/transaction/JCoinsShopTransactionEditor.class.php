<?php 
namespace wcf\data\jcoins\shop\transaction;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Shop transactions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopTransactionEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = JCoinsShopTransaction::class;
	
	/**
	 * @Log transaction
	 */
	public static function create(array $data = []) {
		$user = WCF::getUser();
		$shopItem = $data['shopItem'];
		
		$parameters = [
				'time' => TIME_NOW,
				'shopItemID' => $shopItem->shopItemID,
				'itemTitle' => $shopItem->itemTitle,
				'typeDes' => $shopItem->typeDes,
				'price' => $shopItem->getPrice(),
				'userID' => $user->userID,
				'username' => $user->username,
				'detail' => $data['detail']
		];
		
		parent::create($parameters);
	}
}
