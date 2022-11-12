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

use wcf\data\DatabaseObject;
use wcf\data\jcoins\shop\category\JCoinsShopCategory;
use wcf\data\jcoins\shop\item\content\JCoinsShopItemContent;
use wcf\data\TMultiCategoryObject;
use wcf\system\condition\ConditionHandler;
use wcf\system\event\EventHandler;
use wcf\system\request\IRouteController;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Represents a JCoins Shop Item
 */
class JCoinsShopItem extends DatabaseObject implements IRouteController
{
    use TMultiCategoryObject;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'jcoins_shop_item';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'shopItemID';

    /**
     * item content grouped by language id
     */
    public $itemContents;

    /**
     * list of bought items
     */
    protected static $buyerCache;

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return WCF::getLanguage()->get($this->itemTitle);
    }

    /**
     * Returns the active content version.
     */
    public function getItemContent()
    {
        $this->getItemContents();

        if ($this->isMultilingual) {
            if (isset($this->itemContents[WCF::getLanguage()->languageID])) {
                return $this->itemContents[WCF::getLanguage()->languageID];
            }
        } else {
            if (!empty($this->itemContents[0])) {
                return $this->itemContents[0];
            }
        }

        return null;
    }

    /**
     * Returns the item's contents.
     */
    public function getItemContents()
    {
        if ($this->itemContents === null) {
            $this->itemContents = [];

            $sql = "SELECT    *
                    FROM    wcf" . WCF_N . "_jcoins_shop_item_content
                    WHERE    shopItemID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->shopItemID]);
            while ($row = $statement->fetchArray()) {
                $this->itemContents[$row['languageID'] ?: 0] = new JCoinsShopItemContent(null, $row);
            }
        }

        return $this->itemContents;
    }

    /**
     * Returns the item's subject.
     */
    public function getSubject()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getSubject();
        }

        return '';
    }

    /**
     * Returns the item's unformatted teaser.
     */
    public function getTeaser()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getTeaser();
        }

        return '';
    }

    /**
     * Returns the item's formatted teaser.
     */
    public function getFormattedTeaser()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getFormattedTeaser();
        }

        return '';
    }

    /**
     * Returns the item's formatted content.
     */
    public function getFormattedContent()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getFormattedContent();
        }

        return '';
    }

    /**
     * Returns the item's image.
     */
    public function getImage()
    {
        if ($this->getItemContent() !== null) {
            return $this->getItemContent()->getImage();
        }

        return null;
    }

    /**
     * Returns count if the active user is a buyer of this item.
     */
    public function isBuyer()
    {
        if (self::$buyerCache === null) {
            self::loadBuyerCache();
        }

        return self::$buyerCache[$this->shopItemID] ?? 0;
    }

    /**
     * Loads the list of bought items incl. count.
     */
    protected static function loadBuyerCache()
    {
        self::$buyerCache = [];
        if (!WCF::getUser()->userID) {
            return;
        }

        $sql = "SELECT    shopItemID
                FROM    wcf" . WCF_N . "_jcoins_shop_item_buyer
                WHERE    userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID]);
        while ($shopItemID = $statement->fetchColumn()) {
            if (!isset(self::$buyerCache[$shopItemID])) {
                self::$buyerCache[$shopItemID] = 1;
            } else {
                self::$buyerCache[$shopItemID]++;
            }
        }
    }

    /**
     * Check whether the user is still member
     */
    public function isMember()
    {
        $sql = "SELECT    membershipID
                FROM    wcf" . WCF_N . "_jcoins_shop_membership
                WHERE    userID = ? AND shopItemID = ? AND isActive = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, $this->shopItemID, 1]);

        return $statement->fetchColumn();
    }

    /**
     * Check whether the user has inactive membership
     */
    public function isInactiveMember()
    {
        $sql = "SELECT    membershipID
                FROM    wcf" . WCF_N . "_jcoins_shop_membership
                WHERE    userID = ? AND shopItemID = ? AND isActive = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID, $this->shopItemID, 0]);

        return $statement->fetchColumn();
    }

    /**
     * Returns true if the active user can see this item.
     */
    public function canSee()
    {
        if (WCF::getSession()->getPermission('admin.shopJCoins.canManage')) {
            return true;
        }

        // check viewable
        $itemList = new ViewableJCoinsShopItemList();
        $itemList->getConditionBuilder()->add('shopItemID = ?', [$this->shopItemID]);
        $itemList->readObjects();
        if (!\count($itemList->getObjects())) {
            return false;
        }

        if (WCF::getSession()->getPermission('user.jcoins.canShop')) {
            return true;
        }
        if (WCF::getSession()->getPermission('user.jcoins.canSeeShop')) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the active user can buy this item.
     */
    public function canBuy()
    {
        // external check
        $canBuy = 1;
        $parameters = [
            'canBuy' => $canBuy,
            'shopItem' => $this,
        ];
        EventHandler::getInstance()->fireAction($this, 'canBuy', $parameters);
        $canBuy = $parameters['canBuy'];
        if (!$canBuy) {
            return false;
        }

        // internal
        if ($this->isDisabled) {
            return false;
        }

        //text item
        if ($this->typeDes == 'textItem') {
            if ($this->soldOutText()) {
                return false;
            }
        }

        if (!WCF::getSession()->getPermission('user.jcoins.canShop')) {
            return false;
        }

        if (!JCOINS_ALLOW_NEGATIVE) {
            if (WCF::getUser()->jCoinsAmount < $this->getPrice()) {
                return false;
            }
        }

        if ($this->buyLimit && $this->isBuyer() >= $this->buyLimit) {
            return false;
        }
        if ($this->productLimit && $this->sold >= $this->productLimit) {
            return false;
        }

        if ($this->expirationStatus && $this->expirationDate < TIME_NOW) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if a text item is sold out.
     */
    public function soldOutText()
    {
        if ($this->typeDes == 'textItem') {
            $found = 0;
            $lines = ArrayUtil::trim(\explode("\n", $this->textItem));
            foreach ($lines as $line) {
                \preg_match('/^(\d+):(.+)/', $line, $matches);
                if (!empty($matches) && !empty($matches[1])) {
                    $found = 1;
                    break;
                }
            }
            if (!$found) {
                return true;
            }

            return false;
        }
    }

    /**
     * Returns true if the active user can download this item.
     */
    public function canDownload()
    {
        if ($this->isDisabled) {
            return false;
        }
        if ($this->typeDes != 'download') {
            return false;
        }
        if (!WCF::getSession()->getPermission('user.jcoins.canShop')) {
            return false;
        }
        if (!$this->isBuyer()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the conditions for the item.
     */
    public function getConditions()
    {
        return ConditionHandler::getInstance()->getConditions('com.uz.jcoins.shop.condition', $this->shopItemID);
    }

    /**
     * @inheritDoc
     */
    public static function getCategoryClassName()
    {
        return JCoinsShopCategory::class;
    }

    /**
     * @inheritDoc
     */
    public static function getCategoryMappingDatabaseTableName()
    {
        return 'wcf' . WCF_N . '_jcoins_shop_item_to_category';
    }

    /**
     * Returns the price of the item.
     */
    public function getPrice()
    {
        if ($this->isOffer && $this->offerEnd > TIME_NOW) {
            return $this->offerPrice;
        }

        return $this->price;
    }

    /**
     * Returns true if the item is a offer.
     */
    public function isOffer()
    {
        if ($this->isOffer && $this->offerEnd > TIME_NOW) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if user is allowed
     */
    public function isAllowed()
    {
        $itemList = new ViewableJCoinsShopItemList();
        $itemList->readObjectIDs();
        $ids = $itemList->getObjectIDs();
        if (\in_array($this->shopItemID, $ids)) {
            return true;
        }

        return false;
    }
}
