{if $category}
    {capture assign='pageTitle'}{lang}wcf.jcoins.shop.shop.categorized{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
    {capture assign='contentTitle'}{lang}wcf.jcoins.shop.shop.categorized{/lang}{/capture}
    {capture assign='contentDescription'}{$category->getDescription()}{/capture}
{elseif $shopItem}
    {capture assign='pageTitle'}{lang}wcf.jcoins.shop.shop{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
    {capture assign='contentTitle'}{lang}wcf.jcoins.shop.shop.product{/lang}{/capture}
{else}
    {capture assign='pageTitle'}{lang}wcf.jcoins.shop.shop{/lang}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}
    {capture assign='contentTitle'}{lang}wcf.jcoins.shop.shop{/lang}{/capture}
    {capture assign='contentDescription'}{lang}{@JCOINS_SHOP_DESCRIPTION}{/lang}{/capture}
{/if}

{capture assign='contentHeaderNavigation'}

{/capture}

{capture assign='sidebarRight'}
    <section class="box">
        <form method="post" action="{link controller='JCoinsShop'}{/link}">
            <h2 class="boxTitle">{lang}wcf.jcoins.shop.categories{/lang}</h2>

            <div class="boxContent">
                <ol class="boxMenu">
                    {foreach from=$categoryList item=categoryItem}
                        <li{if $category && $category->categoryID == $categoryItem->categoryID} class="active"{/if} data-category-id="{@$categoryItem->categoryID}">
                            <a href="{link controller='JCoinsShop'}categoryID={@$categoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
                                <span class="boxMenuLinkTitle">{$categoryItem->getTitle()}</span>
                                <span class="badge">{#$categoryItem->getItems()}</span>
                            </a>

                            {if $category && ($category->categoryID == $categoryItem->categoryID || $category->isParentCategory($categoryItem->getDecoratedObject())) && $categoryItem->hasChildren()}
                                <ol class="boxMenuDepth1">
                                    {foreach from=$categoryItem item=subCategoryItem}
                                        <li{if $category->categoryID == $subCategoryItem->categoryID} class="active"{/if} data-category-id="{@$subCategoryItem->categoryID}">
                                            <a href="{link controller='JCoinsShop'}categoryID={@$subCategoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
                                                <span class="boxMenuLinkTitle">{$subCategoryItem->getTitle()}</span>
                                                <span class="badge">{#$subCategoryItem->getItems()}</span>
                                            </a>

                                            {if $category && ($category->categoryID == $subCategoryItem->categoryID || $category->parentCategoryID == $subCategoryItem->categoryID) && $subCategoryItem->hasChildren()}
                                                <ol class="boxMenuDepth2">
                                                    {foreach from=$subCategoryItem item=subSubCategoryItem}
                                                        <li{if $category && $category->categoryID == $subSubCategoryItem->categoryID} class="active"{/if} data-category-id="{@$subSubCategoryItem->categoryID}">
                                                            <a href="{link controller='JCoinsShop'}categoryID={@$subSubCategoryItem->categoryID}&sortField={@$sortField}&sortOrder={@$sortOrder}{/link}" class="boxMenuLink">
                                                                <span class="boxMenuLinkTitle">{$subSubCategoryItem->getTitle()}</span>
                                                                <span class="badge">{#$subSubCategoryItem->getItems()}</span>
                                                            </a>
                                                        </li>
                                                    {/foreach}
                                                </ol>
                                            {/if}
                                        </li>
                                    {/foreach}
                                </ol>
                            {/if}
                        </li>
                    {/foreach}
                </ol>
            </div>

            {if $objects|count > 0}
                <h2 class="boxTitle">{lang}wcf.jcoins.shop.displayOptions{/lang}</h2>

                <div class="boxContent">
                    <dl>
                        <dt><label for="sortField">{lang}wcf.jcoins.shop.sortBy{/lang}</label></dt>
                        <dd>
                            <select id="sortField" name="sortField">
                                <option value="sortOrder"{if $sortField == 'sortOrder'} selected{/if}>{lang}wcf.jcoins.shop.sort.sortOrder{/lang}</option>
                                <option value="changeTime"{if $sortField == 'changeTime'} selected{/if}>{lang}wcf.jcoins.shop.sort.changeTime{/lang}</option>
                                <option value="price"{if $sortField == 'price'} selected{/if}>{lang}wcf.jcoins.shop.sort.price{/lang}</option>
                                <option value="sold"{if $sortField == 'sold'} selected{/if}>{lang}wcf.jcoins.shop.sort.sold{/lang}</option>

                                {event name='sortFields'}
                            </select>
                            <select name="sortOrder">
                                <option value="ASC"{if $sortOrder == 'ASC'} selected{/if}>{lang}wcf.global.sortOrder.ascending{/lang}</option>
                                <option value="DESC"{if $sortOrder == 'DESC'} selected{/if}>{lang}wcf.global.sortOrder.descending{/lang}</option>
                            </select>
                        </dd>
                    </dl>
                </div>

                <div class="formSubmit">
                    <input type="hidden" name="categoryID" value="{@$categoryID}">
                    <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
                </div>
            {/if}
        </form>
    </section>
{/capture}

{assign var='linkParameters' value=''}
{if $category}{capture append='linkParameters'}&categoryID={@$category->categoryID}{/capture}{/if}

{if WCF_VERSION|substr:0:3 >= '5.5'}
    {capture assign='contentInteractionPagination'}
        {pages print=true assign=pagesLinks controller='JCoinsShop' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
    {/capture}

    {include file='header'}
{else}
    {include file='header'}

    {hascontent}
        <div class="paginationTop">
            {content}
                {pages print=true assign=pagesLinks controller='JCoinsShop' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
            {/content}
        </div>
    {/hascontent}
{/if}

{if $items}
    <div class="section">
        <ol class="jcoinsShopItemList">
            {include file='jCoinsShopItems'}
        </ol>
    </div>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
    {hascontent}
        <div class="paginationBottom">
            {content}
                {@$pagesLinks}
            {/content}
        </div>
    {/hascontent}

    {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}{event name='contentFooterNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</footer>

{include file="__jCoinsBranding"}

<script data-relocate="true">
    require(['Language', 'UZ/JCoins/Shop/Preview'], function(Language, UZJCoinsShopPreview) {
        Language.addObject({
            'wcf.jcoins.shop.dialog.preview': '{jslang}wcf.jcoins.shop.dialog.preview{/jslang}'
        });
        new UZJCoinsShopPreview();
    });
</script>

<script data-relocate="true">
    require(['Language', 'UZ/JCoins/Shop/Buy'], function(Language, UZJCoinsShopBuy) {
        Language.addObject({
            'wcf.jcoins.shop.dialog.buy': '{jslang}wcf.jcoins.shop.dialog.buy{/jslang}',
            'wcf.jcoins.shop.success': '{jslang}wcf.jcoins.shop.success{/jslang}'
        });
        new UZJCoinsShopBuy({JCOINS_SHOP_TERMS_ENABLE});
    });
</script>

{event name='additionalJS'}

{include file='footer'}
