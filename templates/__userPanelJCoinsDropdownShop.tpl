{if MODULE_JCOINS_SHOP}
    {if $__wcf->session->getPermission('user.jcoins.canShop') || $__wcf->session->getPermission('user.jcoins.canSeeShop')}
        $panel._createNewLink('panelJCoinsShopLink', '{link controller='JCoinsShop' encode=false}{/link}', '{capture assign=JCoinsShopTitle}{lang}wcf.jcoins.shop.shop{/lang}{/capture}{@$JCoinsShopTitle|encodeJS}', 'fa-shopping-basket');
    {/if}
{/if}
