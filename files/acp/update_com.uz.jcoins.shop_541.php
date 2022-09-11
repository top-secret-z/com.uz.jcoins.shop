<?php
use wcf\data\page\PageCache;
use wcf\data\page\PageEditor;

/**
 * Adds permission to shop page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */

$page = PageCache::getInstance()->getPageByIdentifier('com.uz.jcoins.JCoinsShop');
if ($page) {
	$editor = new PageEditor($page);
	$editor->update(['permissions' => 'user.jcoins.canShop,user.jcoins.canSeeShop']);
}
