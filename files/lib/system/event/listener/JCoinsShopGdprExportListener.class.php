<?php
namespace wcf\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Exports user data iwa Gdpr.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopGdprExportListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// include transactions
		$eventObj->data['com.uz.jcoins.shop'] = [
				'jCoinsShopTransactions' => $this->dumpTable('wcf' . WCF_N . '_jcoins_shop_transaction', 'userID', $eventObj->user->userID),
		];
	}
	
	/**
	 * dump table copied from action and modified
	 */
	protected function dumpTable($tableName, $userIDColumn, $userID) {
		$sql = "SELECT	*
				FROM	${tableName}
				WHERE	${userIDColumn} = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$userID]);
		
		$data = [];
		while ($row = $statement->fetchArray()) {
			$data[] = $row;
		}
		
		return $data;
	}
}
