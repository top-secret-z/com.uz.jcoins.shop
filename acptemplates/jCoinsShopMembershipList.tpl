{include file='header' pageTitle='wcf.acp.menu.link.shopJCoins.membership.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.shopJCoins.membership.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{if $objects|count > 1}
	<form method="post" action="{link controller='JCoinsShopMembershipList'}{/link}">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
			
			<div class="row rowColGap formGrid">
				<dl class="col-xs-12 col-md-6">
					<dt></dt>
					<dd>
						<input type="text" id="username" name="username" value="{$username}" placeholder="{lang}wcf.user.username{/lang}" class="long">
					</dd>
				</dl>
				
				<dl class="col-xs-12 col-md-6">
					<dt></dt>
					<dd>
						<input type="text" id="itemTitle" name="itemTitle" value="{$itemTitle}" placeholder="{lang}wcf.acp.jcoinsShop.item.title{/lang}" class="long">
					</dd>
				</dl>
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
			
			{pages print=true assign=pagesLinks controller="JCoinsShopMembershipList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnMembershipID{if $sortField == 'membershipID'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=membershipID&sortOrder={if $sortField == 'membershipID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnText columnIsActive{if $sortField == 'isActive'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=isActive&sortOrder={if $sortField == 'isActive' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.isActive{/lang}</a></th>
					<th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.username{/lang}</a></th>
					<th class="columnText columnGroupName{if $sortField == 'groupName'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=groupName&sortOrder={if $sortField == 'groupName' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.groupName{/lang}</a></th>
					<th class="columnText columnStartDate{if $sortField == 'startDate'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=startDate&sortOrder={if $sortField == 'startDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.startDate{/lang}</a></th>
					<th class="columnText columnEndDate{if $sortField == 'endDate'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=endDate&sortOrder={if $sortField == 'endDate' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.endDate{/lang}</a></th>
					<th class="columnText columnTitle{if $sortField == 'itemTitle'} active {@$sortOrder}{/if}"><a href="{link controller='JCoinsShopMembershipList'}pageNo={@$pageNo}&sortField=itemTitle&sortOrder={if $sortField == 'itemTitle' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.jcoinsShop.membership.title{/lang}</a></th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=membership}
					<tr class="jsItemRow">
						<td class="columnID columnMembershipID">{@$membership->membershipID}</td>
						<td class="columnText columnIsActive">
							{if $membership->isActive}
								<span class="badge green">{lang}wcf.acp.jcoinsShop.membership.isActive.yes{/lang}</span>
							{else}
								<span class="badge red">{lang}wcf.acp.jcoinsShop.membership.isActive.no{/lang}</span>
							{/if}
						</td>
						<td class="columnText columnUsername"><a href="{link controller='UserEdit' id=$membership->userID}{/link}" title="{lang}wcf.acp.user.edit{/lang}">{$membership->username}</a></td>
						<td class="columnText columnGroupName">{lang}{$membership->groupName}{/lang}</td>
						<td class="columnText columnStartDate">{@$membership->startDate|time}</td>
						<td class="columnText columnEndDate">{@$membership->endDate|time}</td>
						<td class="columnText columnTitle">{$membership->itemTitle}</td>
						
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
