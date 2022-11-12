/**
 * Dialog to buy a JCoins shop item
 * 
 * @author        2017-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.jcoins.shop
 */
define(['Ajax', 'Language', 'Ui/Dialog', 'Ui/Notification', 'Ui/Confirmation'], function(Ajax, Language, UiDialog, UiNotification, UiConfirmation) {
    "use strict";

    function UZJCoinsShopBuy(mustConfirm) { this.init(mustConfirm); }

    UZJCoinsShopBuy.prototype = {
        init: function(mustConfirm) {
            this._mustConfirm = parseInt(mustConfirm);
            this._item = 0;

            var buttons = elBySelAll('.jsJcoinsShopBuy');
            for (var i = 0, length = buttons.length; i < length; i++) {
                buttons[i].addEventListener(WCF_CLICK_EVENT, this._showDialog.bind(this));
            }
        },

        /**
         * Submits the purchase.
         */
        _submit: function() {
            var button = elBySel('.jsSubmitBuy');
            button.classList.add('disabled');

            Ajax.api(this, {
                actionName:    'buy',
                parameters:    {
                    shopItem: this._item
                }
            });
        },

        /**
         * terms click to enable / hide buy button
         */
        _terms: function() {
            var button = elBySel('.jsSubmitBuy');

            if (document.getElementById("termsConfirmed").checked) {
                button.classList.remove('disabled');
            }
            else {
                button.classList.add('disabled');
            }
        },

        /**
         * cancel just closes the dialog
         */
        _cancel: function() {
            UiDialog.close(this);
        },

        /**
         * Initializes the buy dialog.
         */
        _showDialog: function(event) {
            event.preventDefault();

            this._item = ~~elData(event.currentTarget, 'shop-item');

            Ajax.api(this, {
                actionName:    'getBuyDialog',
                parameters:    {
                    shopItem:    this._item
                }
            });
        },

        _ajaxSuccess: function(data) {
            switch (data.actionName) {
                case 'getBuyDialog':
                    this._render(data);
                    break;
                case 'buy':
                    UiNotification.show(Language.get('wcf.jcoins.shop.success'));
                    UiDialog.close(this);
                    window.location.reload();
                    break;
            }
        },

        /**
         * Opens the buy dialog.
         */
        _render: function(data) {
            UiDialog.open(this, data.returnValues.template);

            var submitButton = elBySel('.jsSubmitBuy');
            submitButton.addEventListener(WCF_CLICK_EVENT, this._submit.bind(this));

            var cancelButton = elBySel('.jsCancelBuy');
            cancelButton.addEventListener(WCF_CLICK_EVENT, this._cancel.bind(this));

            if (this._mustConfirm) {
                var termsCheck = elById('termsConfirmed');
                termsCheck.addEventListener('change', this._terms.bind(this));

                submitButton.classList.add('disabled');
            }
        },

        _ajaxSetup: function() {
            return {
                data: {
                    className: 'wcf\\data\\jcoins\\shop\\item\\JCoinsShopItemAction',
                }
            };
        },

        _dialogSetup: function() {
            return {
                id:         'buyDialog',
                options:     { title: Language.get('wcf.jcoins.shop.dialog.buy') },
                source:     null
            };
        }
    };

    return UZJCoinsShopBuy;
});
