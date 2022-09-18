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
namespace wcf\data\jcoins\shop\membership;

use wcf\system\WCF;

/**
 * Represents a list of JCoins Shop Memberships for ACP.
 */
class JCoinsShopAdminMembershipList extends JCoinsShopMembershipList
{
    /**
     * Creates a new membership list object.
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects .= "user_table.username, jcoins_shop_item.itemTitle, user_group.groupID, user_group.groupName";
        $this->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user user_table ON (user_table.userID = jcoins_shop_membership.userID)";
        $this->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_jcoins_shop_item jcoins_shop_item ON (jcoins_shop_item.shopItemID = jcoins_shop_membership.shopItemID)";
        $this->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user_group user_group ON (user_group.groupID = jcoins_shop_membership.groupID)";
    }

    /**
     * @inheritDoc
     */
    public function countObjects()
    {
        $sql = "SELECT    COUNT(*)
                FROM    wcf" . WCF_N . "_jcoins_shop_membership
                " . $this->sqlConditionJoins;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();

        return $statement->fetchSingleColumn();
    }
}
