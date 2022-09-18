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
namespace wcf\system\user\activity\event;

use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for a shop item purchase.
 */
class JCoinsShopUserActivityEvent extends SingletonFactory implements IUserActivityEvent
{
    /**
     * @inheritDoc
     */
    public function prepare(array $events)
    {
        $objectIDs = [];
        foreach ($events as $event) {
            $objectIDs[] = $event->objectID;
        }

        // fetch items
        $itemList = new ViewableJCoinsShopItemList();
        $itemList->getConditionBuilder()->add("jcoins_shop_item.shopItemID IN (?)", [$objectIDs]);
        $itemList->readObjects();
        $items = $itemList->getObjects();

        // set message
        foreach ($events as $event) {
            if (isset($items[$event->objectID])) {
                $item = $items[$event->objectID];

                // check permissions
                if (!$item->canSee()) {
                    continue;
                }
                $event->setIsAccessible();

                // title and description
                $text = WCF::getLanguage()->getDynamicVariable('wcf.jcoins.shop.recentActivity.purchase', ['user' => WCF::getUser()]);
                $event->setTitle($text);
                $event->setDescription('');
            } else {
                $event->setIsOrphaned();
            }
        }
    }
}
