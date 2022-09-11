{foreach from=$objects item=shopItem}
	<li>
		<div class="jcoinsShopItemDiv">
			
			<div class="box128">
				{if $shopItem->getImage() && $shopItem->getImage()->hasThumbnail('tiny')}
					<div class="jcoinsShopItemImage">{@$shopItem->getImage()->getThumbnailTag('tiny')}</div>
				{else}
					{event name='image'}
				{/if}
				
				<div {if $shopItem->isDisabled}class="jcoinsShopItemDisabled"{/if}>
					<div class="containerHeadline">
						<h3 class="jcoinsShopItemSubject">
							<span>{$shopItem->getSubject()}</span>
							
							{if $shopItem->expirationStatus}
								{assign var='timeLeft' value=$shopItem->expirationDate - TIME_NOW}
								
								{if $timeLeft > 10 * 86400}
									<span class="label badge green pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.expires{/lang}">{$shopItem->expirationDate|dateDiff}</span>
								{elseif $timeLeft > 5 * 86400}
									<span class="label badge orange pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.expires{/lang}">{$shopItem->expirationDate|dateDiff}</span>
								{elseif $timeLeft > 0}
									<span class="label badge red pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.expires{/lang}">{$shopItem->expirationDate|dateDiff}</span>
								{else}
									<span class="label badge brown pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.expires{/lang}">{lang}wcf.jcoins.shop.product.expired{/lang}</span>
								{/if}
							{/if}
							
							{if $shopItem->productLimit}
								{assign var='left' value=$shopItem->productLimit - $shopItem->sold}
								
								{if $left > 10}
									<span class="label badge green pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.limit{/lang}">{lang}wcf.jcoins.shop.product.limit.detail{/lang}</span>
								{elseif $left > 5}
									<span class="label badge orange pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.limit{/lang}">{lang}wcf.jcoins.shop.product.limit.detail{/lang}</span>
								{elseif $left > 0}
									<span class="label badge red pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.limit{/lang}">{lang}wcf.jcoins.shop.product.limit.detail{/lang}</span>
								{else}
									<span class="label badge brown pointer jsTooltip" title="{lang}wcf.jcoins.shop.product.limit{/lang}">{lang}wcf.jcoins.shop.product.limit.soldOut{/lang}</span>
								{/if}
							{/if}
						</h3>
						<ul class="inlineList dotSeparated jcoinsShopItemInfo">
							{event name='user'}
							
							<li>{@$shopItem->changeTime|time}</li>
						</ul>
					</div>
					
					<div class="containerContent jcoinsShopItemTeaser">
						{@$shopItem->getFormattedTeaser()}
					</div>
					
					<div class="containerContent jcoinsShopItemPriceInfo">
						{if $shopItem->isBuyer()}
							<span class="icon icon16 fa-check" title="{lang}wcf.jcoins.shop.product.bought{/lang}"></span>
						{/if}
						
						<span class="jcoinsShopItemPrice{if $shopItem->isOffer()} jcoinsShopItemPriceOffer jsTooltip" title="{lang}wcf.jcoins.shop.product.offer.expires{/lang}"{else}"{/if}>{@$shopItem->price} {JCOINS_NAME}</span>
						{if $shopItem->isOffer()}
							&nbsp;  <span class="jcoinsShopItemPrice">{@$shopItem->offerPrice} {JCOINS_NAME}</span>
						{/if}
						<br>
					</div>
					
					<div class="containerContent jcoinsShopItemTeaser">
						{if $shopItem->canSee()}
							<button class="small jsOnly jsJcoinsShopPreview" data-shop-item="{@$shopItem->shopItemID}">{lang}wcf.jcoins.shop.button.preview{/lang}</button>
						{else}
							<button class="small jsOnly jsJcoinsShopPreview disabled" data-shop-item="{@$shopItem->shopItemID}">{lang}wcf.jcoins.shop.button.preview{/lang}</button>
						{/if}
						
						{if $shopItem->canBuy()}
							<button class="small buttonPrimary jsOnly jsJcoinsShopBuy" data-shop-item="{@$shopItem->shopItemID}">{if $shopItem->isBuyer() && $shopItem->isMember()}{lang}wcf.jcoins.shop.button.extend{/lang}{else}{lang}wcf.jcoins.shop.button.buy{/lang}{/if}</button>
						{else}
							<button class="small buttonPrimary jsOnly disabled">{lang}wcf.jcoins.shop.button.buy{/lang}</button>
						{/if}
						
						{if $shopItem->canDownload()}
							<a href="{link controller='JCoinsDownload' id=$shopItem->shopItemID}{/link}" class="small button" style="float:right;">
								{lang}wcf.jcoins.shop.button.download{/lang}
							</a>
						{/if}
						
						{event name='buttons'}
						
						{if $shopItem->typeDes == 'membership' && $shopItem->membershipID|in_array:$__wcf->user->getGroupIDs()}
							<span style="float:right;">{lang}wcf.acp.jcoinsShop.item.membership.already{/lang}</span>
							
						{/if}
					</div>
				</div>
			</div>
			
			<div class="jcoinsShopItemFooter">
				<ul class="inlineList jcoinsShopItemSales">
					<li>
						<span class="icon icon16 fa-shopping-basket"></span>
						{lang}wcf.jcoins.shop.product.sold{/lang}
					</li>
				</ul>
			</div>
		</div>
	</li>
{/foreach}