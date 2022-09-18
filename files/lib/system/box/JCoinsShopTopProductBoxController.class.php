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
namespace wcf\system\box;

use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows top purchased shop items.
 */
class JCoinsShopTopProductBoxController extends AbstractBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return LinkHandler::getInstance()->getLink('JCoinsShop', [], '&sortField=sold&sortOrder=DESC&pageNo=0');
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        // module and permission
        if (!MODULE_JCOINS || !MODULE_JCOINS_SHOP) {
            return;
        }
        if (!WCF::getSession()->getPermission('user.jcoins.canShop') && !WCF::getSession()->getPermission('user.jcoins.canSeeShop')) {
            return;
        }

        $itemList = new ViewableJCoinsShopItemList();
        $itemList->getConditionBuilder()->add('jcoins_shop_item.sold > ?', [0]);
        $itemList->sqlOrderBy = 'sold DESC';
        $itemList->sqlLimit = JCOINS_SHOP_ITEMS_PER_BOX;
        $itemList->readObjects();

        if (\count($itemList)) {
            WCF::getTPL()->assign([
                'shopItems' => $itemList,
            ]);

            $this->content = WCF::getTPL()->fetch('boxJCoinsShop');
        }
    }
}
