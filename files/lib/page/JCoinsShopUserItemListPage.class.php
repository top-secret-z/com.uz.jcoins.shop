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
namespace wcf\page;

use wcf\data\jcoins\shop\item\JCoinsShopUserItemList;
use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * Shows a list of the items bought by user.
 */
class JCoinsShopUserItemListPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_JCOINS', 'MODULE_JCOINS_SHOP'];

    /**
     * list of user items
     */
    public $userItemList = [];

    public $allowedIDs = [];

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // get user items
        $list = new JCoinsShopUserItemList();
        $list->readObjects();
        $this->userItemList = $list->getObjects();

        // viewable item list
        $list = new ViewableJCoinsShopItemList();
        $list->getConditionBuilder()->add("jcoins_shop_item.typeDes LIKE ?", ['membership']);
        $list->readObjectIDs();
        $this->allowedIDs = $list->getObjectIDs();
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'userItems' => $this->userItemList,
            'allowedIDs' => $this->allowedIDs,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        // set active tab
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.settings.jCoinsShop');

        parent::show();
    }
}
