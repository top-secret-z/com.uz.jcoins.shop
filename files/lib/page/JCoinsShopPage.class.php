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

use wcf\data\jcoins\shop\category\JCoinsShopCategory;
use wcf\data\jcoins\shop\category\JCoinsShopCategoryNodeTree;
use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\data\jcoins\shop\item\ViewableJCoinsShopItemList;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\page\PageLocationManager;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * JCoins Shop Page.
 */
class JCoinsShopPage extends SortablePage
{
    /**
     * @inheritdoc
     */
    public $loginRequired = false;

    /**
     * @inheritdoc
     */
    public $neededModules = ['MODULE_JCOINS', 'MODULE_JCOINS_SHOP'];

    /**
     * @inheritdoc
     */
    public $neededPermissions = ['user.jcoins.canShop', 'user.jcoins.canSeeShop'];

    /**
     * @inheritdoc
     */
    public $itemsPerPage = JCOINS_SHOP_ITEMS_PER_PAGE;

    /**
     * @inheritdoc
     */
    public $validSortFields = ['sortOrder', 'changeTime', 'price', 'sold'];

    /**
     * @inheritdoc
     */
    public $defaultSortField = JCOINS_SHOP_SORTFIELD;

    /**
     * @inheritdoc
     */
    public $defaultSortOrder = JCOINS_SHOP_SORTORDER;

    /**
     * @inheritdoc
     */
    public $objectListClassName = ViewableJCoinsShopItemList::class;

    /**
     * category list
     */
    public $categoryList;

    public $categoryID = 0;

    public $category;

    /**
     * shop item
     */
    public $shopItemID = 0;

    public $shopItem;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['categoryID'])) {
            $this->categoryID = \intval($_REQUEST['categoryID']);
            $this->category = JCoinsShopCategory::getCategory($this->categoryID);
            if ($this->category === null) {
                throw new IllegalLinkException();
            }
            if (!$this->category->isAccessible()) {
                throw new PermissionDeniedException();
            }
        }

        if (!empty($_REQUEST['shopItemID'])) {
            $this->shopItemID = \intval($_REQUEST['shopItemID']);
            $this->shopItem = new JCoinsShopItem($this->shopItemID);
            if ($this->shopItem === null) {
                throw new IllegalLinkException();
            }
            if (!$this->shopItem->canSee()) {
                throw new PermissionDeniedException();
            }
        }

        $linkParameters = 'sortField=' . $this->sortField . '&sortOrder=' . $this->sortOrder . '&pageNo=' . $this->pageNo;
        if ($this->category) {
            $linkParameters .= '&categoryID=' . $this->category->categoryID;
        }

        if ($this->shopItem) {
            $linkParameters .= '&shopItemID=' . $this->shopItemID;
        }

        $this->setCanonicalURL($linkParameters);
    }

    /**
     * Sets/enforces the canonical url of the page.
     */
    protected function setCanonicalURL($linkParameters)
    {
        if (empty($_POST)) {
            $this->canonicalURL = LinkHandler::getInstance()->getLink('JCoinsShop', [
                'application' => 'wcf',
            ], ($this->pageNo ? 'pageNo=' . $this->pageNo : ''));
        } else {
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('JCoinsShop', [
                'application' => 'wcf',
            ], $linkParameters));

            exit;
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        if ($this->category) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_item.shopItemID IN (SELECT shopItemID FROM wcf' . WCF_N . '_jcoins_shop_item_to_category WHERE categoryID = ?)', [$this->category->categoryID]);
        } elseif ($this->shopItem) {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_item.shopItemID = ?', [$this->shopItemID]);
        } else {
            $this->objectList->getConditionBuilder()->add('jcoins_shop_item.showStartPage = ?', [1]);
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // get categories
        $categoryTree = new JCoinsShopCategoryNodeTree('com.uz.jcoins.shop.category');
        $this->categoryList = $categoryTree->getIterator();
        $this->categoryList->setMaxDepth(0);

        if ($this->category || $this->shopItem) {
            $this->setLocation();
        }
    }

    /**
     * Sets the page location data.
     */
    protected function setLocation()
    {
        if ($this->category || $this->shopItem) {
            // `-1` = pseudo object id to have (second) page with identifier `com.uz.jcoins.JCoinsShop`
            PageLocationManager::getInstance()->addParentLocation('com.uz.jcoins.JCoinsShop', -1);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'controllerObject' => null,
            'controllerName' => 'JCoinsShop',
            'categoryList' => $this->categoryList,
            'category' => $this->category,
            'categoryID' => $this->categoryID,
            'shopItem' => $this->shopItem,
        ]);
    }
}
