<?php
namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryEditForm;

/**
 * Shows the JCoins Shop category add form.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryEditForm extends AbstractCategoryEditForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.jcoins';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'com.uz.jcoins.shop.category';
}
