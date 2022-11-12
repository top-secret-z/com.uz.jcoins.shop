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
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\data\jcoins\shop\item\JCoinsShopItemList;

/**
 * Disables JCoins Shop items if sold out / expired.
 */
class JCoinsShopDisableCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // get affected products
        $itemList = new JCoinsShopItemList();
        $itemList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $itemList->getConditionBuilder()->add('autoDisable = ?', [1]);
        $itemList->getConditionBuilder()->add('(productLimit > ? AND productLimit >= sold) OR (expirationStatus = ? AND expirationDate < ?)', [0, 1, TIME_NOW]);
        $itemList->readObjects();
        $items = $itemList->getObjects();

        if (!\count($items)) {
            return;
        }

        // toggle
        $action = new JCoinsShopItemAction($items, 'toggle');
        $action->executeAction();
    }
}
