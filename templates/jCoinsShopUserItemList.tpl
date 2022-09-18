{include file='userMenuSidebar'}

{include file='header'}

{if $userItems|count}
    <section class="section sectionContainerList">
        <ul class="containerList">
            {foreach from=$userItems item=userItem}
                <li>
                    <div class="box32">

                        {if $userItem->getImage() && $userItem->getImage()->hasThumbnail('tiny')}
                            <span class="jcoinsShopBoxImage64">{@$userItem->getImage()->getThumbnailTag('tiny')}</span>
                        {/if}

                        <div class="containerHeadline">
                            {if ($userItem->canSee())}
                                <h3><a href="{link controller='JCoinsShop' shopItemID=$userItem->shopItemID}{/link}">{$userItem->getSubject()}</a></h3>
                            {else}
                                <h3>{$userItem->getSubject()}</h3>
                            {/if}
                            <p>{@$userItem->getFormattedTeaser()}</p>

                            {if $userItem->typeDes == 'membership' && $userItem->endDate}
                                <div class="containerContent">
                                    {lang}wcf.jcoins.shop.product.user.{if $userItem->endDate < TIME_NOW}expired{else}expires{/if}{/lang} {@$userItem->endDate|time}

                                    {if $userItem->canBuy() && $userItem->shopItemID|in_array:$allowedIDs}
                                        <span style="margin-left:15px;"><button class="small  jsOnly jsJcoinsShopBuy" data-shop-item="{@$userItem->shopItemID}">{if $userItem->isBuyer() && $userItem->isMember()}{lang}wcf.jcoins.shop.button.extend{/lang}{else}{lang}wcf.jcoins.shop.button.buy{/lang}{/if}</button></span>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
    </section>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

<script data-relocate="true">
    require(['Language', 'UZ/JCoins/Shop/Buy'], function(Language, UZJCoinsShopBuy) {
        Language.addObject({
            'wcf.jcoins.shop.dialog.buy': '{jslang}wcf.jcoins.shop.dialog.buy{/jslang}',
            'wcf.jcoins.shop.success': '{jslang}wcf.jcoins.shop.success{/jslang}'
        });
        new UZJCoinsShopBuy({JCOINS_SHOP_TERMS_ENABLE});
    });
</script>

{include file='footer'}
