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

use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\system\exception\PermissionDeniedException;
use wcf\util\FileReader;

/**
 * JCoins file download Page.
 */
class JCoinsDownloadPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $useTemplate = false;

    /**
     * item object
    */
    public $shopItem;

    /**
     * file reader object
     */
    public $fileReader;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $id = \intval($_REQUEST['id']);
        }
        $this->shopItem = new JCoinsShopItem($id);
        if (!$this->shopItem->shopItemID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        parent::checkPermissions();

        if (!$this->shopItem->canDownload()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // init file reader
        $this->fileReader = new FileReader(WCF_DIR . 'jCoinsFiles/' . $this->shopItem->filename, []);
        // add etag
        $this->fileReader->addHeader('ETag', '"' . $this->shopItem->shopItemID . '"');
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        parent::show();

        // send file to client
        $this->fileReader->send();

        exit;
    }
}
