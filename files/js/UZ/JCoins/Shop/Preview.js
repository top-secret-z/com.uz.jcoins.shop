/**
 * Provides the dialog to view item description.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
define(['Ajax', 'Language', 'Ui/Dialog'], function(Ajax, Language, UiDialog) {
	"use strict";
	
	function UZJCoinsShopPreview() { this.init(); }
	
	UZJCoinsShopPreview.prototype = {
		init: function() {
			var buttons = elBySelAll('.jsJcoinsShopPreview');
			for (var i = 0, length = buttons.length; i < length; i++) {
				buttons[i].addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
			}
			
			this._shopItem = '';
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					actionName:	'getPreview',
					className:	'wcf\\data\\jcoins\\shop\\item\\JCoinsShopItemAction'
				}
			};
		},
		
		_ajaxSuccess: function(data) {
			UiDialog.open(this, data.returnValues.template);
		},
		
		_dialogSetup: function() {
			return {
				id: 'getPreview',
				options: {
					title: Language.get('wcf.jcoins.shop.dialog.preview')
				},
				source: null
			};
		},
		
		_click: function(event) {
			this._shopItem = elData(event.currentTarget, 'shop-item');
			
			Ajax.api(this, {
				parameters: {
					shopItem: this._shopItem
				}
			});
		}
	};
	return UZJCoinsShopPreview;
});
