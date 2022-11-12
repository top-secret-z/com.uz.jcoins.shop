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
namespace wcf\acp\page;

use wcf\data\jcoins\shop\membership\JCoinsShopAdminMembershipList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Shop membership list page.
 */
class JCoinsShopMembershipListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.membership.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.shopJCoins.canManage'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_JCOINS_SHOP'];

    /**
     * number of items shown per page
     */
    public $itemsPerPage = 20;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'membershipID';

    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['membershipID', 'username', 'itemTitle', 'startDate', 'endDate', 'groupName', 'isActive'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = JCoinsShopAdminMembershipList::class;

    /**
     * filter
     */
    public $username = '';

    public $itemTitle = '';

    /**
     * @inheritdoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['username'])) {
            $this->username = StringUtil::trim($_REQUEST['username']);
        }
        if (isset($_REQUEST['itemTitle'])) {
            $this->itemTitle = StringUtil::trim($_REQUEST['itemTitle']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->username) {
            $this->objectList->getConditionBuilder()->add('user_table.username LIKE ?', ['%' . $this->username . '%']);
        }
        if ($this->itemTitle) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_item.itemTitle LIKE ?', ['%' . $this->itemTitle . '%']);
        }
    }

    /**
     * @inheritdoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'username' => $this->username,
            'itemTitle' => $this->itemTitle,
        ]);
    }
}
