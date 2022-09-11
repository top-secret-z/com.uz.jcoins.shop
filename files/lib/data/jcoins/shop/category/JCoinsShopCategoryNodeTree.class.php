<?php
namespace wcf\data\jcoins\shop\category;
use wcf\data\category\CategoryNode;
use wcf\data\category\CategoryNodeTree;

/**
 * Represents a list of  JCoins Shop category nodes.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryNodeTree extends CategoryNodeTree {
	/**
	 * @inheritDoc
	 */
	protected $nodeClassName = JCoinsShopCategoryNode::class;
	
	/**
	 * @inheritDoc
	 */
	public function isIncluded(CategoryNode $categoryNode) {
		return parent::isIncluded($categoryNode) && $categoryNode->isAccessible();
	}
}
