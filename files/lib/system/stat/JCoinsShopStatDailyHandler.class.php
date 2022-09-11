<?php
namespace wcf\system\stat;
use wcf\system\WCF;

/**
 * Stat handler implementation for JCoins shop purchases.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	public function getData($date) {
		return [
				'counter' => $this->getCounter($date, 'wcf'.WCF_N.'_jcoins_shop_item_buyer', 'buyDate'),
				'total' => $this->getTotal($date, 'wcf'.WCF_N.'_jcoins_shop_item_buyer', 'buyDate')
		];
	}
}
