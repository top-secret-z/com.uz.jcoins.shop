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
namespace wcf\data\jcoins\shop\item;

use wcf\data\jcoins\shop\category\JCoinsShopCategory;
use wcf\system\WCF;

/**
 * Represents a list of viewable JCoins Shop Items.
 */
class ViewableJCoinsShopItemList extends JCoinsShopItemList
{
    public function __construct()
    {
        // all items
        $itemList = new JCoinsShopItemList();
        $itemList->readObjects();
        $items = $itemList->getObjects();

        // item group conditions
        $groupItemIDs = [];
        if (\count($items)) {
            foreach ($items as $item) {
                $conditions = $item->getConditions();
                foreach ($conditions as $condition) {
                    if (!$condition->getObjectType()->getProcessor()->showContent($condition)) {
                        continue 2;
                    }
                }

                $groupItemIDs[] = $item->shopItemID;
            }
        }

        // categories
        $accessibleCategoryIDs = JCoinsShopCategory::getAccessibleCategoryIDs();

        parent::__construct();

        if (!WCF::getSession()->getPermission('admin.shopJCoins.canManage')) {
            $this->getConditionBuilder()->add("jcoins_shop_item.isDisabled = ?", [0]);
        }

        // item groups
        if (\count($groupItemIDs)) {
            $this->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (?)", [$groupItemIDs]);
        } else {
            $this->getConditionBuilder()->add('1=0');
        }

        // categories
        if (empty($accessibleCategoryIDs)) {
            $this->getConditionBuilder()->add('1=0');
        } else {
            $this->getConditionBuilder()->add('jcoins_shop_item.shopItemID IN (SELECT shopItemID FROM wcf' . WCF_N . '_jcoins_shop_item_to_category WHERE categoryID IN (?))', [$accessibleCategoryIDs]);
        }
    }
}
