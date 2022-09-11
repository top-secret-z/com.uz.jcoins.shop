<?php
namespace wcf\data\jcoins\shop\category;
use wcf\data\category\AbstractDecoratedCategory;
use wcf\data\user\User;
use wcf\data\ITitledLinkObject;
use wcf\system\category\CategoryHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a JCoins Shop category
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategory extends AbstractDecoratedCategory implements ITitledLinkObject  {
	/**
	 * object type name of the categories
	 */
	const OBJECT_TYPE_NAME = 'com.uz.jcoins.shop.category';
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('CategoryJCoinsShopItemList', [
			'application' => 'wcf',
			'forceFrontend' => true,
			'object' => $this->getDecoratedObject()
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getDecoratedObject()->getTitle();
	}
	
	/**
	 * Returns true if the category is accessible for the given user. If no
	 * user is given, the active user is checked.
	 */
	public function isAccessible(User $user = null) {
		if ($this->getObjectType()->objectType != self::OBJECT_TYPE_NAME) return false;
		
		// check permissions
		return $this->getPermission('canViewCategory', $user);
	}
	
	/**
	 * Returns a list with ids of accessible categories.
	 */
	public static function getAccessibleCategoryIDs(array $permissions = ['canViewCategory']) {
		$categoryIDs = [];
		foreach (CategoryHandler::getInstance()->getCategories(self::OBJECT_TYPE_NAME) as $category) {
			$result = true;
			$category = new JCoinsShopCategory($category);
			
			// hide disabled categories
			if ($category->isDisabled) {
				continue;
			}
			
			foreach ($permissions as $permission) {
				$result = $result && $category->getPermission($permission);
			}
			
			if ($result) {
				$categoryIDs[] = $category->categoryID;
			}
		}
		
		return $categoryIDs;
	}
}
