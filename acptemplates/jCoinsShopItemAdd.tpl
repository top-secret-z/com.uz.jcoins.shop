{include file='header' pageTitle='wcf.acp.jcoinsShop.item.'|concat:$action}

<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
		new UiUserSearchInput(elBySel('input[name="sender"]'));
	});
</script>

<script data-relocate="true">
	$(function() {
		$('input[type="radio"][name="expirationStatus"]').change(function(event) {
			var $selected = $('input[type="radio"][name="expirationStatus"]:checked');
			if ($selected.length > 0) {
				if ($selected.val() == 1) {
					$('#expirationDateDl').show();
				}
				else {
					$('#expirationDateDl').hide();
				}
			}
		}).trigger('change');
	});
</script>

<script data-relocate="true">
	$(function() {
		$('#isOffer').change(function() {
			if ($('#isOffer')[0].checked) {
				$('#offerPriceDl').show();
				$('#offerEndDl').show();
			}
			else {
				$('#offerPriceDl').hide();
				$('#offerEndDl').hide();
			}
		}).trigger('change');
	});
</script>

{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
	<script data-relocate="true">
		{include file='mediaJavaScript'}
		
		require(['WoltLabSuite/Core/Media/Manager/Select'], function(MediaManagerSelect) {
			new MediaManagerSelect({
				dialogTitle: '{lang}wcf.media.chooseImage{/lang}',
				imagesOnly: 1
			});
		});
	</script>
{/if}

{if $action == 'edit'}
	<script data-relocate="true">
		require(['Language', 'UZ/JCoins/Shop/Acp/Copy'], function(Language, UZJCoinsShopAcpCopy) {
			Language.addObject({
				'wcf.acp.jcoinsShop.item.copy.confirm': '{jslang}wcf.acp.jcoinsShop.item.copy.confirm{/jslang}'
			});
			new UZJCoinsShopAcpCopy();
		});
	</script>
{/if}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.jcoinsShop.item.{$action}{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			{if $action == 'edit'}
				<li><a class="jsButtonCopy button" data-object-id="{@$shopItemID}"><span class="icon icon16 fa-files-o"></span> <span>{lang}wcf.acp.jcoinsShop.item.copy{/lang}</span></a></li>
			{/if}
			<li><a href="{link controller='JCoinsShopItemList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.jcoinsShop.item.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{@$action}{/lang}</p>
{/if}

{if $action == 'edit' && $item->typeDes == 'membership' && $item->sold}
	<p class='error'>{lang}wcf.acp.jcoinsShop.item.membership.change{/lang}</p><br>
{/if}

<form id="formContainer" method="post" action="{if $action == 'add'}{link controller='JCoinsShopItemAdd'}{/link}{else}{link controller='JCoinsShopItemEdit' id=$item->shopItemID}{/link}{/if}" enctype="multipart/form-data">
	<div class="section tabMenuContainer">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('generalData')}">{lang}wcf.acp.jcoinsShop.item.general{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('typeData')}">{lang}wcf.acp.jcoinsShop.item.type{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('limitData')}">{lang}wcf.acp.jcoinsShop.item.limits{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('textData')}">{lang}wcf.acp.jcoinsShop.item.text{/lang}</a></li>
			</ul>
		</nav>
		
		<div id="generalData" class="tabMenuContent hidden">
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.item.price{/lang}</h2>
				
				<dl>
					<dt><label for="price">{lang}wcf.acp.jcoinsShop.item.price{/lang}</label></dt>
					<dd>
						<input type="number" name="price" id="price" value="{$price}" min="0" class="tiny" />
						<small>{lang}wcf.acp.jcoinsShop.item.price.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="isOffer" id="isOffer" value="1"{if $isOffer} checked{/if}> {lang}wcf.acp.jcoinsShop.item.isOffer{/lang}</label>
						<small>{lang}wcf.acp.jcoinsShop.item.isOffer.description{/lang}</small>
					</dd>
				</dl>
				
				<dl id="offerPriceDl">
					<dt><label for="offerPrice">{lang}wcf.acp.jcoinsShop.item.offerPrice{/lang}</label></dt>
					<dd>
						<input type="number" name="offerPrice" id="offerPrice" value="{$offerPrice}" min="0" class="tiny" />
					</dd>
				</dl>
				
				<dl id="offerEndDl"{if $errorField == 'offerEnd'} class="formError"{/if}{if $expirationStatus != 1} style="display: none"{/if}>
					<dt><label for="offerEnd">{lang}wcf.acp.jcoinsShop.item.offerEnd{/lang}</label></dt>
					<dd>
						<input type="datetime" id="offerEnd" name="offerEnd" value="{$offerEnd}" class="medium">
						{if $errorField == 'offerEnd'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.offerEnd.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			</section>
			
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.item.general{/lang}</h2>
				
				<dl{if $errorField == 'itemTitle'} class="formError"{/if}>
					<dt><label for="itemTitle">{lang}wcf.acp.jcoinsShop.item.title{/lang}</label></dt>
					<dd>
						<input type="text" id="itemTitle" name="itemTitle" value="{$itemTitle}" maxlength="80" class="long" />
						<small>{lang}wcf.acp.jcoinsShop.item.title.description{/lang}</small>
						
						{if $errorField == 'itemTitle'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsShop.item.title.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="isDisabled" value="1"{if $isDisabled} checked{/if}> {lang}wcf.acp.jcoinsShop.item.isDisabled{/lang}</label>
					</dd>
				</dl>
				
				<dl>
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="showStartPage" value="1"{if $showStartPage} checked{/if}> {lang}wcf.acp.jcoinsShop.item.showStartPage{/lang}</label>
						<small>{lang}wcf.acp.jcoinsShop.item.showStartPage.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="sortOrder">{lang}wcf.acp.jcoinsShop.item.sortOrder{/lang}</label></dt>
					<dd>
						<input type="number" name="sortOrder" id="sortOrder" value="{$sortOrder}" min="0" class="tiny" />
						<small>{lang}wcf.acp.jcoinsShop.item.sortOrder.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorField == 'sender'} class="sender"{/if}>
					<dt><label for="sender">{lang}wcf.acp.jcoinsShop.item.sender{/lang}</label></dt>
					<dd>
						<input type="text" id="sender" name="sender" value="{$sender}" class="small" maxlength="255">
						<small>{lang}wcf.acp.jcoinsShop.item.sender.description{/lang}</small>
						
						{if $errorField == 'sender'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.sender.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="leaveConversation" value="1"{if $leaveConversation} checked{/if}> {lang}wcf.acp.jcoinsShop.item.leaveConversation{/lang}</label>
					</dd>
				</dl>
			</section>
			
			<section class="section">
				<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.item.categories{/lang}</h2>
				
				{include file='jCoinsShopFlexibleCategoryList'}
				
				{if $errorField == 'categoryIDs'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else}
							{lang}wcf.acp.jcoinsShop.item.categories.error.{@$errorType}{/lang}
						{/if}
					</small>
				{/if}
			</section>
		</div>
		
		<div id="limitData" class="tabMenuContent hidden">
			<div class="section">
				<dl>
					<dt><label for="productLimit">{lang}wcf.acp.jcoinsShop.item.productLimit{/lang}</label></dt>
					<dd>
						<input type="number" name="productLimit" id="productLimit" value="{$productLimit}" min="0" class="tiny" />
						<small>{lang}wcf.acp.jcoinsShop.item.productLimit.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="buyLimit">{lang}wcf.acp.jcoinsShop.item.buyLimit{/lang}</label></dt>
					<dd>
						<input type="number" name="buyLimit" id="buyLimit" value="{$buyLimit}" min="0" class="tiny" />
						<small>{lang}wcf.acp.jcoinsShop.item.buyLimit.description{/lang}</small>
					</dd>
				</dl>
				
				<dl>
					<dt><label for="expirationStatus">{lang}wcf.acp.jcoinsShop.item.expirationStatus{/lang}</label></dt>
					<dd class="floated">
						<label><input type="radio" name="expirationStatus" value="0"{if $expirationStatus == 0} checked{/if}> {lang}wcf.acp.jcoinsShop.item.expirationStatus.no{/lang}</label>
						<label><input type="radio" name="expirationStatus" value="1"{if $expirationStatus == 1} checked{/if}> {lang}wcf.acp.jcoinsShop.item.expirationStatus.yes{/lang}</label>
					</dd>
				</dl>
				
				<dl id="expirationDateDl"{if $errorField == 'expirationDate'} class="formError"{/if}{if $expirationStatus != 1} style="display: none"{/if}>
					<dt><label for="expirationDate">{lang}wcf.acp.jcoinsShop.item.expirationDate{/lang}</label></dt>
					<dd>
						<input type="datetime" id="expirationDate" name="expirationDate" value="{$expirationDate}" class="medium">
						{if $errorField == 'expirationDate'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.expirationDate.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt><label for="autoDisable">{lang}wcf.acp.jcoinsShop.item.autoDisable.label{/lang}</label></dt>
					<dd>
						<label><input type="checkbox" name="autoDisable" id="autoDisable" value="1"{if $autoDisable} checked{/if}> {lang}wcf.acp.jcoinsShop.item.autoDisable{/lang}</label>
						<small>{lang}wcf.acp.jcoinsShop.item.autoDisable.description{/lang}</small>
					</dd>
				</dl>
			</div>
			<div class="section">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.item.conditions{/lang}</h2>
				</header>
				
				{include file='userConditions'}
			</div>
		</div>
		
		<div id="typeData" class="tabMenuContent hidden">
			<div class="section">
				<dl{if $errorField == 'typeID'} class="formError"{/if}>
					<dt><label for="typeID">{lang}wcf.acp.jcoinsShop.item.typeID{/lang}</label></dt>
					<dd>
						<select name="typeID" id="typeID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$availableTypes item=type}
								<option value="{@$type->typeID}"{if $type->typeID == $typeID} selected="selected"{/if}>{$type->getTitle()}</option>
							{/foreach}
						</select>
						
						{if $errorField == 'typeID'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsShop.item.typeID.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section membershipSetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.setting{/lang}</h2>
				</header>
				
				<dl{if $errorField == 'membershipID'} class="formError"{/if}>
					<dt>{lang}wcf.acp.jcoinsShop.item.membership.groupID{/lang}</dt>
					<dd>
						{htmlOptions name='membershipID' options=$availableGroups selected=$membershipID}
						
						{if $errorField == 'membershipID'}
							<small class="innerError">{lang}wcf.acp.jcoinsShop.item.membership.groupID.error.{$errorType}{/lang}</small>
						{/if}
					</dd>
				</dl>
				
				<dl{if $errorField == 'membershipDays'} class="formError"{/if}>
					<dt><label for="membershipDays">{lang}wcf.acp.jcoinsShop.item.membership.days{/lang}</label></dt>
					<dd>
						<input type="number" name="membershipDays" id="membershipDays" value="{$membershipDays}" min="1" max="6000" class="small" />
						<small>{lang}wcf.acp.jcoinsShop.item.membership.days.description{/lang}</small>
						
						{if $errorField == 'membershipDays'}
							<small class="innerError">{lang}wcf.acp.jcoinsShop.item.membership.days.error.{$errorType}{/lang}</small>
						{/if}
					</dd>
				</dl>
				
				<dl{if $errorField == 'membershipWarn'} class="formError"{/if}>
					<dt><label for="membershipWarn">{lang}wcf.acp.jcoinsShop.item.membership.warn{/lang}</label></dt>
					<dd>
						<input type="number" name="membershipWarn" id="membershipWarn" value="{$membershipWarn}" min="0" class="small" />
						<small>{lang}wcf.acp.jcoinsShop.item.membership.warn.description{/lang}</small>
						
						{if $errorField == 'membershipWarn'}
							<small class="innerError">{lang}wcf.acp.jcoinsShop.item.membership.warn.error.{$errorType}{/lang}</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section downloadSetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.setting{/lang}</h2>
					<p class="sectionDescription">{lang}wcf.acp.jcoinsShop.setting.download{/lang}</p>
				</header>
				
				<dl{if $errorField == 'filename'} class="formError"{/if}>
					<dt><label for="filename">{lang}wcf.acp.jcoinsShop.item.filename{/lang}</label></dt>
					<dd>
						<input type="text" id="filename" name="filename" value="{$filename}" maxlength="80" class="medium" />
						<small>{lang}wcf.acp.jcoinsShop.item.filename.description{/lang}</small>
						
						{if $errorField == 'filename'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsShop.item.filename.error.{@$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl{if $errorField == 'fileUpload'} class="formError"{/if}>
					<dt><label for="fileUpload">{lang}wcf.acp.jcoinsShop.item.fileUpload{/lang}</label></dt>
					<dd>
						{if $uploadedFilename}
							<input type="hidden" name="uploadedFilename" value="{$uploadedFilename}">
						{/if}
						<input type="file" id="fileUpload" name="fileUpload" value="">
						
						{if $errorField == 'fileUpload'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.fileUpload.error.{@$errorType}{/lang}
								{/if}
						</small>
						{/if}
						<small>{lang}wcf.acp.jcoinsShop.item.fileUpload.description{/lang}</small>
					</dd>
				</dl>
			</div>
			
			<div class="section handsonSetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.setting{/lang}</h2>
				</header>
				
				<dl{if $errorField == 'handsonNames'} class="formError"{/if}>
					<dt><label for="handsonNames">{lang}wcf.acp.jcoinsShop.item.handson.names{/lang}</label></dt>
					<dd>
						<input type="text" id="handsonNames" name="handsonNames" value="{$handsonNames}" class="long" />
						<small>{lang}wcf.acp.jcoinsShop.item.handson.names.description{/lang}</small>
						
						{if $errorField == 'handsonNames'}
							<small class="innerError">
								{lang}wcf.acp.jcoinsShop.item.handson.names.error.{$errorType}{/lang}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			<div class="section textItemSetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.setting{/lang}</h2>
				</header>
				
				<dl{if $errorField == 'textItem'} class="formError"{/if}>
					<dt><label for="textItem">{lang}wcf.acp.jcoinsShop.item.textItem{/lang}</label></dt>
					<dd>
						<textarea id="textItem" name="textItem" cols="40" rows="10">{$textItem}</textarea>
						<small>{lang}wcf.acp.jcoinsShop.item.textItem.description{/lang}</small>
						
						{if $errorField == 'textItem'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.textItem.error.{$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
				
				<dl>
					<dt></dt>
					<dd>
						<label><input type="checkbox" name="textItemAutoLimit" value="1"{if $textItemAutoLimit} checked{/if}> {lang}wcf.acp.jcoinsShop.item.textItemAutoLimit{/lang}</label>
						<small>{lang}wcf.acp.jcoinsShop.item.textItemAutoLimit.description{/lang}</small>
					</dd>
				</dl>
			</div>
			
			<div class="section trophySetting">
				<header class="sectionHeader">
					<h2 class="sectionTitle">{lang}wcf.acp.jcoinsShop.setting{/lang}</h2>
				</header>
				
				<dl{if $errorField == 'trophyID'} class="formError"{/if}>
					<dt><label for="trophy">{lang}wcf.acp.jcoinsShop.item.trophy{/lang}</label></dt>
					<dd>
						<select name="trophyID" id="trophyID">
							<option value="0">{lang}wcf.global.noSelection{/lang}</option>
							{foreach from=$availableTrophies item=trophy}
								<option value="{@$trophy->trophyID}"{if $trophy->trophyID == $trophyID} selected{/if}>{$trophy->trophyID} - {$trophy->getTitle()}</option>
							{/foreach}
						</select>
						<small>{lang}wcf.acp.jcoinsShop.item.trophy.description{/lang}</small>
						
						{if $errorField == 'trophyID'}
							<small class="innerError">
								{if $errorType == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.jcoinsShop.item.trophyID.error.{$errorType}{/lang}
								{/if}
							</small>
						{/if}
					</dd>
				</dl>
			</div>
			
			{event name='shopItemType'}
		</div>
		
		<div id="textData" class="tabMenuContent hidden">
			<div class="section" id="notifyTextContainer">
				<div class="section">
					{if !$isMultilingual}
						{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
							<dl{if $errorField == 'image'} class="formError"{/if}>
								<dt><label for="image">{lang}wcf.acp.jcoinsShop.item.image{/lang}</label></dt>
								<dd>
									<div id="imageDisplay" class="selectedImagePreview">
										{if $images[0]|isset && $images[0]->hasThumbnail('small')}
											{@$images[0]->getThumbnailTag('small')}
										{/if}
									</div>
									<p class="button jsMediaSelectButton" data-store="imageID0" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
									<small>{lang}wcf.acp.jcoinsShop.item.image.description{/lang}</small>
									<input type="hidden" name="imageID[0]" id="imageID0"{if $imageID[0]|isset} value="{@$imageID[0]}"{/if}>
									{if $errorField == 'image'}
										<small class="innerError">{lang}wcf.acp.jcoinsShop.item.image.error.{@$errorType}{/lang}</small>
									{/if}
								</dd>
							</dl>
						{elseif $action == 'edit' && $images[0]|isset && $images[0]->hasThumbnail('small')}
							<dl>
								<dt>{lang}wcf.acp.jcoinsShop.item.image{/lang}</dt>
								<dd>
									<div id="imageDisplay">{@$images[0]->getThumbnailTag('small')}</div>
								</dd>
							</dl>
						{/if}
						
						<!-- subject -->
						<dl{if $errorField == 'subject'} class="formError notifySubject"{else} class="notifySubject"{/if}>
							<dt><label for="subject0">{lang}wcf.acp.jcoinsShop.item.subject{/lang}</label></dt>
							<dd>
								<input type="text" id="subject0" name="subject[0]" value="{if !$subject[0]|empty}{$subject[0]}{/if}" class="long" maxlength="255">
								
								{if $errorField == 'subject'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsShop.item.subject.error.{$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
						
						<dl{if $errorField == 'teaser'} class="formError notifyTeaser"{else} class="notifyTeaser"{/if}>
							<dt><label for="teaser0">{lang}wcf.acp.jcoinsShop.item.teaser{/lang}</label></dt>
							<dd>
								<textarea name="teaser[0]" id="teaser0" rows="3">{if !$teaser[0]|empty}{$teaser[0]}{/if}</textarea>
								
								{if $errorField == 'teaser'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsShop.item.teaser.error.{$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
						
						<dl{if $errorField == 'content'} class="formError notifyContent"{else} class="notifyContent"{/if}>
							<dt><label for="content0">{lang}wcf.acp.jcoinsShop.item.content{/lang}</label></dt>
							<dd>
								<textarea name="content[0]" id="content0" class="wysiwygTextarea" data-disable-media="0">{if !$content[0]|empty}{$content[0]}{/if}</textarea>
								{include file='wysiwyg' wysiwygSelector='content0'}
								{if $errorField == 'content'}
									<small class="innerError">
										{if $errorType == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.acp.jcoinsShop.item.content.error.{@$errorType}{/lang}
										{/if}
									</small>
								{/if}
							</dd>
						</dl>
					</div>
				{else}
					<div class="section tabMenuContainer">
						<nav class="tabMenu">
							<ul>
								{foreach from=$availableLanguages item=availableLanguage}
									{assign var='containerID' value='language'|concat:$availableLanguage->languageID}
									<li><a href="{@$__wcf->getAnchor($containerID)}">{$availableLanguage->languageName}</a></li>
								{/foreach}
							</ul>
						</nav>
						
						{foreach from=$availableLanguages item=availableLanguage}
							<div id="language{@$availableLanguage->languageID}" class="tabMenuContent">
								<div class="section">
									{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
										<dl{if $errorField == 'image'|concat:$availableLanguage->languageID} class="formError"{/if}>
											<dt><label for="image{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsShop.item.image{/lang}</label></dt>
											<dd>
												<div id="imageDisplay{@$availableLanguage->languageID}">
													{if $images[$availableLanguage->languageID]|isset && $images[$availableLanguage->languageID]->hasThumbnail('small')}
														{@$images[$availableLanguage->languageID]->getThumbnailTag('small')}
													{/if}
												</div>
												<p class="button jsMediaSelectButton" data-store="imageID{@$availableLanguage->languageID}" data-display="imageDisplay{@$availableLanguage->languageID}">{lang}wcf.media.chooseImage{/lang}</p>
												<small>{lang}wcf.acp.jcoinsShop.item.image.description{/lang}</small>
												<input type="hidden" name="imageID[{@$availableLanguage->languageID}]" id="imageID{@$availableLanguage->languageID}"{if $imageID[$availableLanguage->languageID]|isset} value="{@$imageID[$availableLanguage->languageID]}"{/if}>
												{if $errorField == 'image'|concat:$availableLanguage->languageID}
													<small class="innerError">{lang}wcf.acp.jcoinsShop.item.image.error.{@$errorType}{/lang}</small>
												{/if}
											</dd>
										</dl>
									{elseif $action == 'edit' && $images[$availableLanguage->languageID]|isset && $images[$availableLanguage->languageID]->hasThumbnail('small')}
										<dl>
											<dt>{lang}wcf.acp.jcoinsShop.item.image{/lang}</dt>
											<dd>
												<div id="imageDisplay">{@$images[$availableLanguage->languageID]->getThumbnailTag('small')}</div>
											</dd>
										</dl>
									{/if}
									
									
									
									<dl{if $errorField == 'subject'|concat:$availableLanguage->languageID} class="formError notifySubject"{else} class="notifySubject"{/if}>
										<dt><label for="subject{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsShop.item.subject{/lang}</label></dt>
										<dd>
											<input type="text" id="subject{@$availableLanguage->languageID}" name="subject[{@$availableLanguage->languageID}]" value="{if !$subject[$availableLanguage->languageID]|empty}{$subject[$availableLanguage->languageID]}{/if}" class="long" maxlength="255">
											
											{if $errorField == 'subject'|concat:$availableLanguage->languageID}
												<small class="innerError">
													{if $errorType == 'empty'}
														{lang}wcf.global.form.error.empty{/lang}
													{else}
														{lang}wcf.acp.jcoinsShop.item.subject.error.{$errorType}{/lang}
													{/if}
												</small>
											{/if}
										</dd>
									</dl>
									
									<dl{if $errorField == 'teaser'|concat:$availableLanguage->languageID} class="formError notifyTeaser"{else} class="notifyTeaser"{/if}>
										<dt><label for="teaser{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsShop.item.teaser{/lang}</label></dt>
										<dd>
											<textarea name="teaser[{@$availableLanguage->languageID}]" id="teaser{@$availableLanguage->languageID}" rows="3">{if !$teaser[$availableLanguage->languageID]|empty}{$teaser[$availableLanguage->languageID]}{/if}</textarea>
											
											{if $errorField == 'teaser'|concat:$availableLanguage->languageID}
												<small class="innerError">
													{if $errorType == 'empty'}
														{lang}wcf.global.form.error.empty{/lang}
													{else}
														{lang}wcf.acp.jcoinsShop.item.teaser.error.{$errorType}{/lang}
													{/if}
												</small>
											{/if}
										</dd>
									</dl>
									
									<dl{if $errorField == 'content'|concat:$availableLanguage->languageID} class="formError notifyContent"{else} class="notifyContent"{/if}>
										<dt><label for="content{@$availableLanguage->languageID}">{lang}wcf.acp.jcoinsShop.item.content{/lang}</label></dt>
										<dd>
											<textarea name="content[{@$availableLanguage->languageID}]" id="content{@$availableLanguage->languageID}" class="wysiwygTextarea" data-disable-media="0">{if !$content[$availableLanguage->languageID]|empty}{$content[$availableLanguage->languageID]}{/if}</textarea>
											{include file='wysiwyg' wysiwygSelector='content'|concat:$availableLanguage->languageID}
											{if $errorField == 'content'|concat:$availableLanguage->languageID}
												<small class="innerError">
													{if $errorType == 'empty'}
														{lang}wcf.global.form.error.empty{/lang}
													{else}
														{lang}wcf.acp.jcoinsShop.item.content.error.{@$errorType}{/lang}
													{/if}
												</small>
											{/if}
										</dd>
									</dl>
								</div>
							</div>
						{/foreach}
					</div>
				{/if}
			</div>
		</div>
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		{csrfToken}
	</div>
</form>

<script data-relocate="true">
	$(function() {
		var $typeID = $('#typeID').change(function(event) {
			var value = $(event.currentTarget).val();
			
			$('.membershipSetting, .downloadSetting, .handsonSetting, .textItemSetting, .trophySetting').hide();
			
			if (value == 1) { $('.membershipSetting').show(); }
			if (value == 2) { $('.downloadSetting').show(); }
			if (value == 3) { $('.handsonSetting').show(); }
			if (value == 4) { $('.textItemSetting').show(); }
			if (value == 5) { $('.trophySetting').show(); }
			
			{event name='shopItemTypeJS'}
			
		});
		$typeID.trigger('change');
		
	});
</script>

<script data-relocate="true">
	$(function() {
		new WCF.Search.User('#handsonNames', undefined, false, [ ], true);
	});
</script>

{include file='footer'}
