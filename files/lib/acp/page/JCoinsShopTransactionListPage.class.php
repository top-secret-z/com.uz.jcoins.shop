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

use wcf\data\jcoins\shop\transaction\JCoinsShopTransactionList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Shop Item list page.
 */
class JCoinsShopTransactionListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.transaction.list';

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
    public $defaultSortField = 'time';

    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['transactionID', 'time', 'username', 'price', 'typeDes', 'detail', 'itemTitle'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = JCoinsShopTransactionList::class;

    /**
     * filter
     */
    public $username = '';

    public $itemTitle = '';

    public $typeDes = '';

    public $availableTypes = [];

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
        if (!empty($_REQUEST['typeDes'])) {
            $this->typeDes = $_REQUEST['typeDes'];
        }
    }

    /**
     * @inheritdoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->username) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.username LIKE ?', ['%' . $this->username . '%']);
        }
        if ($this->itemTitle) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.itemTitle LIKE ?', ['%' . $this->itemTitle . '%']);
        }

        if ($this->typeDes) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_transaction.typeDes LIKE ?', ['%' . $this->typeDes . '%']);
        }

        // available types
        $this->availableTypes = [];
        $sql = "SELECT    DISTINCT    typeDes
                FROM                wcf" . WCF_N . "_jcoins_shop_transaction";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            if ($row['typeDes']) {
                $this->availableTypes[$row['typeDes']] = WCF::getLanguage()->get('wcf.acp.jcoinsShop.item.' . $row['typeDes']);
            }
        }
        \ksort($this->availableTypes);
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
            'typeDes' => $this->typeDes,
            'availableTypes' => $this->availableTypes,
        ]);
    }
}
