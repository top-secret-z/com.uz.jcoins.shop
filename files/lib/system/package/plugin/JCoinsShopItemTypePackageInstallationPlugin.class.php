<?php
namespace wcf\system\package\plugin;
use wcf\data\jcoins\shop\item\type\JCoinsShopItemTypeEditor;
use wcf\system\WCF;

/**
 * Installs, updates and deletes additional JCoins Shop item types.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemTypePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * @inheritDoc
	 */
	public $className = JCoinsShopItemTypeEditor::class;
	
	/**
	 * @inheritDoc
	 */
	public $tableName = 'jcoins_shop_item_type';
	
	/**
	 * @inheritDoc
	 */
	public $tagName = 'jCoinsShopItem';
	
	/**
	 * @inheritDoc
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM	wcf".WCF_N."_".$this->tableName."
				WHERE		typeTitle = ?
							AND packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute([$item['attributes']['name'], $this->installation->getPackageID()
			]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	protected function prepareImport(array $data) {
		return [
				'typeTitle' => $data['attributes']['name'],
				'typeID' => $data['elements']['typeID'],
				'sortOrder' => $data['elements']['sortOrder']
		];
	}
	
	/**
	 * @inheritDoc
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
				FROM	wcf".WCF_N."_".$this->tableName."
				WHERE	typeTitle = ?
						AND packageID = ?";
		$parameters = [
				$data['typeTitle'],
				$this->installation->getPackageID()
		];
		
		return [
				'sql' => $sql,
				'parameters' => $parameters
		];
	}
}
