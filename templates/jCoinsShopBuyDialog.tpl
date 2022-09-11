<section class="section">
	<h2 class="sectionTitle">{$item->getSubject()}</h2>
	
	{@$item->getFormattedTeaser()}
</section>

<section class="section">
	<h2 class="sectionTitle">{lang}wcf.jcoins.shop.product.buyFor{/lang}</h2>
	
	<span class="jcoinsShopDialogItemPrice">{@$item->getPrice()} {JCOINS_NAME}</span>
</section>

{if !$notice|empty}
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.jcoins.shop.product.notice{/lang}</h2>
		
		<p>{@$notice}</p>
	</section>
{/if}

{if JCOINS_SHOP_TERMS_ENABLE}
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.jcoins.shop.terms{/lang}</h2>
		
		<label><input type="checkbox" id="termsConfirmed" name="termsConfirmed" value="1"> {lang}wcf.jcoins.shop.terms.confirm{/lang}</label>
	</section>
{/if}

<div class="formSubmit">
	<button class="jsSubmitBuy buttonPrimary" accesskey="s">{lang}wcf.jcoins.shop.button.buy{/lang}</button>
	<button class="jsCancelBuy" accesskey="s">{lang}wcf.global.button.cancel{/lang}</button>
</div>
