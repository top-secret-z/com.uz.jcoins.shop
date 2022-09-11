<?php
namespace wcf\acp\form;
use wcf\acp\form\AbstractCategoryAddForm;

/**
 * Shows the JCoins Shop category add form.
 * 
 * @author		Udo Zaydowicz
 * @copyright	2017-2022 Zaydowicz.de
 * @license		Zaydowicz Commercial License <https://zaydowicz.de>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopCategoryAddForm extends AbstractCategoryAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.category.add';
	
	/**
	 * @inheritDoc
	 */
	public $objectTypeName = 'com.uz.jcoins.shop.category';
}
