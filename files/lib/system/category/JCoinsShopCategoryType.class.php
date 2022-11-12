<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\category;

use wcf\data\category\CategoryEditor;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\data\jcoins\shop\item\JCoinsShopItemList;
use wcf\system\WCF;

/**
 * Category type implementation for JCoins Shop categories.
 */
class JCoinsShopCategoryType extends AbstractCategoryType
{
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
    public function afterDeletion(CategoryEditor $categoryEditor)
    {
        // delete items with no categories
        $itemList = new JCoinsShopItemList();
        $itemList->enableCategoryLoading(false);
        $itemList->sqlJoins = "LEFT JOIN wcf" . WCF_N . "_jcoins_shop_item_to_category jcoins_shop_item_to_category ON (jcoins_shop_item_to_category.shopItemID = jcoins_shop_item.shopItemID)";
        $itemList->getConditionBuilder()->add("jcoins_shop_item_to_category.categoryID IS NULL");
        $itemList->readObjects();

        if (\count($itemList)) {
            $action = new JCoinsShopItemAction($itemList->getObjects(), 'delete');
            $action->executeAction();
        }

        parent::afterDeletion($categoryEditor);
    }

    /**
     * @inheritDoc
     */
    public function canAddCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canDeleteCategory()
    {
        return $this->canEditCategory();
    }

    /**
     * @inheritDoc
     */
    public function canEditCategory()
    {
        return WCF::getSession()->getPermission('admin.shopJCoins.canManage');
    }

    /**
     * @inheritDoc
     */
    public function changedParentCategories(array $categoryData)
    {
        // if category is moved to a new parent category, the items in
        // the moved category need to be also assigned to this new parent
        // category
        $sql = "INSERT IGNORE INTO    wcf" . WCF_N . "_jcoins_shop_item_to_category
                        (categoryID, shopItemID)
                SELECT            ?, shopItemID
                FROM            wcf" . WCF_N . "_jcoins_shop_item_to_category
                WHERE            categoryID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($categoryData as $categoryID => $parentCategoryData) {
            if ($parentCategoryData['newParentCategoryID']) {
                $statement->execute([
                    $parentCategoryData['newParentCategoryID'],
                    $categoryID,
                ]);
            }
        }
        WCF::getDB()->commitTransaction();
    }
}
