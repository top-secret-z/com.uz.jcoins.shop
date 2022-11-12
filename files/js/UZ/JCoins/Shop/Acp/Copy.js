/**
 * Copies a JCoins Shop Item.
 * 
 * @author        2017-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.jcoins.shop
 */
define(['Ajax', 'Language', 'Ui/Confirmation', 'Ui/Notification'], function(Ajax, Language, UiConfirmation, UiNotification) {
    "use strict";

    function UZJCoinsShopAcpCopy() { this.init(); }

    UZJCoinsShopAcpCopy.prototype = {
        init: function() {
            var button = elBySel('.jsButtonCopy');

            button.addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
        },

        _click: function(event) {
            event.preventDefault();
            var objectID = ~~elData(event.currentTarget, 'object-id');

            UiConfirmation.show({
                confirm: function() {
                    Ajax.apiOnce({
                        data: {
                            actionName: 'copy',
                            className: 'wcf\\data\\jcoins\\shop\\item\\JCoinsShopItemAction',
                            parameters: {
                                objectID: objectID
                            }
                        },
                        success: function(data) {
                            UiNotification.show();

                            window.location = data.returnValues.redirectURL;
                        }
                    });
                },
                message: Language.get('wcf.acp.jcoinsShop.item.copy.confirm')
            });    
        }
    };
    return UZJCoinsShopAcpCopy;
});
