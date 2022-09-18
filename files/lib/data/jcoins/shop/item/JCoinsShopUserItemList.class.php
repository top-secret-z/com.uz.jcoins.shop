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

use wcf\system\WCF;

/**
 * Represents a list of bought JCoins Shop Items.
 */
class JCoinsShopUserItemList extends JCoinsShopItemList
{
    /**
     * Creates a new item list object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (SELECT shopItemID FROM wcf" . WCF_N . "_jcoins_shop_item_buyer WHERE userID = ?)", [WCF::getUser()->userID]);
    }

    /**
     * @inheritDoc
     */
    public function readObjects()
    {
        parent::readObjects();

        // get endDate manually
        foreach ($this->objects as $item) {
            if ($item->typeDes == 'membership') {
                $sql = "SELECT    endDate
                        FROM    wcf" . WCF_N . "_jcoins_shop_membership
                        WHERE    userID = ? AND shopItemID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([WCF::getUser()->userID, $item->shopItemID]);
                $item->endDate = $statement->fetchColumn();
            }
        }
    }
}
