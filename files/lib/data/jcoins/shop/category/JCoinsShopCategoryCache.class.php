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
namespace wcf\data\jcoins\shop\category;

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Manages the JCoins Shop category cache.
 */
class JCoinsShopCategoryCache extends SingletonFactory
{
    /**
     * number of total items
     */
    protected $items;

    /**
     * Calculates the number of items.
     */
    protected function initItems()
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('jcoins_shop_item.isDisabled = ?', [0]);

        $sql = "SELECT        COUNT(*) AS count, jcoins_shop_item_to_category.categoryID
                FROM        wcf" . WCF_N . "_jcoins_shop_item jcoins_shop_item
                LEFT JOIN    wcf" . WCF_N . "_jcoins_shop_item_to_category jcoins_shop_item_to_category
                ON            (jcoins_shop_item_to_category.shopItemID = jcoins_shop_item.shopItemID)
                " . $conditionBuilder . "
                GROUP BY    jcoins_shop_item_to_category.categoryID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditionBuilder->getParameters());
        $this->items = $statement->fetchMap('categoryID', 'count');
    }

    /**
     * Return the number of items in the category with the given id.
     */
    public function getItems($categoryID)
    {
        if ($this->items === null) {
            $this->initItems();
        }

        if (isset($this->items[$categoryID])) {
            return $this->items[$categoryID];
        }

        return 0;
    }
}
