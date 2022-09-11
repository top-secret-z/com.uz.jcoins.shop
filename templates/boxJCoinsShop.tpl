<ul class="sidebarItemList">
	{foreach from=$shopItems item=shopItem}
		<li class="box24">
			{if $shopItem->getImage() && $shopItem->getImage()->hasThumbnail('tiny')}
				<span class="jcoinsShopBoxImage">{@$shopItem->getImage()->getThumbnailTag('tiny')}</span>
			{/if}
			
			<div class="sidebarItemTitle">
				{if ($shopItem->canSee())}
					<h3><a href="{link controller='JCoinsShop' shopItemID=$shopItem->shopItemID}{/link}">{$shopItem->getSubject()|truncate:75}</a></h3>
				{else}
					<h3>{$shopItem->getSubject()}</h3>
				{/if}
				<ul class="inlineList dotSeparated">
					<li><small>{@$shopItem->changeTime|date}</small></li>
					<li><small>{lang}wcf.jcoins.shop.product.sold{/lang}</small></li>
				</ul>
			</div>
		</li>
	{/foreach}
</ul>
