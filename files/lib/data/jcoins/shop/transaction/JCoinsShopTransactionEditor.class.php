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
namespace wcf\data\jcoins\shop\transaction;

use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit JCoins Shop transactions.
 */
class JCoinsShopTransactionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    public static $baseClass = JCoinsShopTransaction::class;

    /**
     * @Log transaction
     */
    public static function create(array $data = [])
    {
        $user = WCF::getUser();
        $shopItem = $data['shopItem'];

        $parameters = [
            'time' => TIME_NOW,
            'shopItemID' => $shopItem->shopItemID,
            'itemTitle' => $shopItem->itemTitle,
            'typeDes' => $shopItem->typeDes,
            'price' => $shopItem->getPrice(),
            'userID' => $user->userID,
            'username' => $user->username,
            'detail' => $data['detail'],
        ];

        parent::create($parameters);
    }
}
