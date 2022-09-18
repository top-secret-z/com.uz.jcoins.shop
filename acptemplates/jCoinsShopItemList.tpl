{include file='header' pageTitle='wcf.acp.menu.link.shopJCoins.item.list'}

<script data-relocate="true">
    $(function() {
        new WCF.Action.Delete('wcf\\data\\jcoins\\shop\\item\\JCoinsShopItemAction', $('.jsItemRow'));
        new WCF.Action.Toggle('wcf\\data\\jcoins\\shop\\item\\JCoinsShopItemAction', $('.jsItemRow'));
    });
</script>

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.shopJCoins.item.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='JCoinsShopItemAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.menu.link.shopJCoins.item.add{/lang}</span></a></li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller="JCoinsShopItemList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table">
            <thead>
                <tr>
                    <th class="columnID columnItemID{if $sortField == 'shopItemID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=shopItemID&sortOrder={if $sortField == 'shopItemID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnText columnTitle{if $sortField == 'itemTitle'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=itemTitle&sortOrder={if $sortField == 'itemTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.title{/lang}</a></th>
                    <th class="columnText columnPrice{if $sortField == 'price'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=price&sortOrder={if $sortField == 'price' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.price{/lang}</a></th>
                    <th class="columnText columnType{if $sortField == 'typeDes'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=typeDes&sortOrder={if $sortField == 'typeDes' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.typeDes{/lang}</a></th>
                    <th class="columnText columnSold{if $sortField == 'sold'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=sold&sortOrder={if $sortField == 'sold' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.sales{/lang}</a></th>
                    <th class="columnText columnEarnings{if $sortField == 'earnings'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=earnings&sortOrder={if $sortField == 'earnings' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.earnings{/lang}</a></th>
                    <th class="columnText columnSortOrder{if $sortField == 'sortOrder'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopItemList'}pageNo={@$pageNo}&sortField=sortOrder&sortOrder={if $sortField == 'sortOrder' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.acp.jcoinsShop.item.sortOrder{/lang}</a></th>

                </tr>
            </thead>

            <tbody>
                {foreach from=$objects item=shopItem}
                    <tr class="jsItemRow">
                        <td class="columnIcon">
                            <span class="icon icon16 fa-{if !$shopItem->isDisabled}check-{/if}square-o jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $shopItem->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{@$shopItem->shopItemID}" data-disable-message="{lang}wcf.global.button.disable{/lang}" data-enable-message="{lang}wcf.global.button.enable{/lang}"></span>
                            <a href="{link controller='JCoinsShopItemEdit' object=$shopItem}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
                            <span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$shopItem->shopItemID}" data-confirm-message="{lang}wcf.acp.jcoinsShop.item.delete.sure{/lang}"></span>
                        </td>
                        <td class="columnID columnItemID">{@$shopItem->shopItemID}</td>
                        <td class="columnText columnTitle">{$shopItem->itemTitle}</td>
                        <td class="columnText columnPrice">{@$shopItem->price}</td>
                        <td class="columnText columnType">{lang}wcf.acp.jcoinsShop.item.{$shopItem->typeDes}{/lang}</td>
                        <td class="columnText columnSold">{@$shopItem->sold|shortUnit}</td>
                        <td class="columnText columnEarnings">{@$shopItem->earnings|shortUnit}</td>
                        <td class="columnText columnSortOrder">{@$shopItem->sortOrder}</td>

                    </tr>
                {/foreach}
            </tbody>
        </table>

    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        <nav class="contentFooterNavigation">
            <ul>
                <li><a href="{link controller='JCoinsShopItemAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.jcoinsShop.item.add{/lang}</span></a></li>

                {event name='contentFooterNavigation'}
            </ul>
        </nav>
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
