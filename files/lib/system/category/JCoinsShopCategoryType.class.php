<?php
namespace wcf\system\category;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\data\jcoins\shop\item\JCoinsShopItemList;
use wcf\data\category\CategoryEditor;
use wcf\system\category\AbstractCategoryType;
use wcf\system\WCF;

/**
 * Category type implementation for JCoins Shop categories.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryType extends AbstractCategoryType {
	/**
	 * @inheritDoc
	 */
	protected $langVarPrefix = 'jcoins.shop.category';
	
	/**
	 * @inheritDoc
	 */
	protected $forceDescription = false;
	
	/**
	 * @inheritDoc
	 */
	protected $maximumNestingLevel = 2;
	
	/**
	 * @inheritDoc
	 */
	protected $objectTypes = ['com.woltlab.wcf.acl' => 'com.uz.jcoins.shop.category'];
	
	/**
	 * @inheritDoc
	 */
	public function afterDeletion(CategoryEditor $categoryEditor) {
		// delete items with no categories
		$itemList = new JCoinsShopItemList();
		$itemList->enableCategoryLoading(false);
		$itemList->sqlJoins = "LEFT JOIN wcf".WCF_N."_jcoins_shop_item_to_category jcoins_shop_item_to_category ON (jcoins_shop_item_to_category.shopItemID = jcoins_shop_item.shopItemID)";
		$itemList->getConditionBuilder()->add("jcoins_shop_item_to_category.categoryID IS NULL");
		$itemList->readObjects();
		
		if (count($itemList)) {
			$action = new JCoinsShopItemAction($itemList->getObjects(), 'delete');
			$action->executeAction();
		}
		
		parent::afterDeletion($categoryEditor);
	}
	
	/**
	 * @inheritDoc
	 */
	public function canAddCategory() {
		return $this->canEditCategory();
	}
	
	/**
	 * @inheritDoc
	 */
	public function canDeleteCategory() {
		return $this->canEditCategory();
	}
	
	/**
	 * @inheritDoc
	 */
	public function canEditCategory() {
		return WCF::getSession()->getPermission('admin.shopJCoins.canManage');
	}
	
	/**
	 * @inheritDoc
	 */
	public function changedParentCategories(array $categoryData) {
		// if category is moved to a new parent category, the items in
		// the moved category need to be also assigned to this new parent
		// category
		$sql = "INSERT IGNORE INTO	wcf".WCF_N."_jcoins_shop_item_to_category
						(categoryID, shopItemID)
				SELECT			?, shopItemID
				FROM			wcf".WCF_N."_jcoins_shop_item_to_category
				WHERE			categoryID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($categoryData as $categoryID => $parentCategoryData) {
			if ($parentCategoryData['newParentCategoryID']) {
				$statement->execute([
					$parentCategoryData['newParentCategoryID'],
					$categoryID
				]);
			}
		}
		WCF::getDB()->commitTransaction();
	}
}
