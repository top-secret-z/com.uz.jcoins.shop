{include file='header' pageTitle='wcf.acp.menu.link.shopJCoins.transaction.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.shopJCoins.transaction.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{if $objects|count > 1}
	<form method="post" action="{link controller='JCoinsShopTransactionList'}{/link}">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
			
			<div class="row rowColGap formGrid">
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="username" name="username" value="{$username}" placeholder="{lang}wcf.user.username{/lang}" class="long">
					</dd>
				</dl>
				
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="itemTitle" name="itemTitle" value="{$itemTitle}" placeholder="{lang}wcf.acp.jcoinsShop.item.title{/lang}" class="long">
					</dd>
				</dl>
				
				{if $availableTypes|count > 1}
					<dl class="col-xs-12 col-md-4">
						<dt></dt>
						<dd>
							<select name="typeDes" id="typeDes">
								<option value="">{lang}wcf.acp.jcoinsShop.item.typeID{/lang}</option>
								{htmlOptions options=$availableTypes selected=$typeDes}
							</select>
						</dd>
					</dl>
				{/if}
			</div>
			
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				{csrfToken}
			</div>
		</section>
	</form>
{/if}

{hascontent}
	<div class="paginationTop">
		{content}
			{assign var='linkParameters' value=''}
			{if $itemTitle}{capture append=linkParameters}&itemTitle={@$itemTitle|rawurlencode}{/capture}{/if}
			{if $username}{capture append=linkParameters}&username={@$username|rawurlencode}{/capture}{/if}
			
			{pages print=true assign=pagesLinks controller="JCoinsShopTransactionList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnTransactionID{if $sortField == 'transactionID'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=transactionID&sortOrder={if $sortField == 'transactionID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnText columnTime{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.transaction.time{/lang}</a></th>
					<th class="columnText columnTitle{if $sortField == 'itemTitle'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=itemTitle&sortOrder={if $sortField == 'itemTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.item.title{/lang}</a></th>
					<th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.transaction.username{/lang}</a></th>
					<th class="columnText columnPrice{if $sortField == 'price'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=price&sortOrder={if $sortField == 'price' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.item.price{/lang}</a></th>
					<th class="columnText columnType{if $sortField == 'typeDes'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=typeDes&sortOrder={if $sortField == 'typeDes' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.item.typeDes{/lang}</a></th>
					<th class="columnText columnDetail{if $sortField == 'detail'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopTransactionList'}pageNo={@$pageNo}&sortField=detail&sortOrder={if $sortField == 'detail' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.item.detail{/lang}</a></th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=transaction}
					<tr class="jsItemRow">
						<td class="columnID columnTransactionID">{@$transaction->transactionID}</td>
						<td class="columnText columnTime">{@$transaction->time|time}</td>
						<td class="columnText columnTitle"><a href="{link controller='JCoinsShopItemEdit' id=$transaction->shopItemID}{/link}" title="{lang}wcf.global.button.edit{/lang}">{$transaction->itemTitle}</a></td>
						<td class="columnText columnUsername">{$transaction->username}</td>
						<td class="columnText columnPrice">{@$transaction->price}</td>
						<td class="columnText columnType">{lang}wcf.acp.jcoinsShop.item.{$transaction->typeDes}{/lang}</td>
						<td class="columnText columnDetail">{lang}{$transaction->detail}{/lang}</td>
						
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
				
				{event name='contentFooterNavigation'}
			</ul>
		</nav>
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
