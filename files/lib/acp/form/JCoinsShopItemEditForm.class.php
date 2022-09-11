<?php
namespace wcf\acp\form;
use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\DateUtil;

/**
 * Shows the JCoins Shop Item edit form.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemEditForm extends JCoinsShopItemAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.item.list';
	
	// Shop Item data
	public $shopItemID = 0;
	public $item = null;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		if (!empty($_POST) && !WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
			foreach ($this->item->getItemContents() as $languageID => $content) {
				$this->imageID[$languageID] = $content->imageID;
			}
			
			$this->readImages();
		}
		
		parent::readData();
		
		if (empty($_POST)) {
			$this->itemTitle = $this->item->itemTitle;
			$this->sender = $this->item->sender;
			$this->leaveConversation = $this->item->leaveConversation;
			$this->isDisabled = $this->item->isDisabled;
			$this->showStartPage = $this->item->showStartPage;
			
			$this->typeID = $this->item->typeID;
			
			$this->filename = $this->item->filename;
			$this->handsonNames = $this->item->handsonNames;
			$this->membershipDays = $this->item->membershipDays;
			$this->membershipID = $this->item->membershipID;
			$this->membershipWarn = $this->item->membershipWarn;
			$this->textItem = $this->item->textItem;
			$this->textItemAutoLimit = $this->item->textItemAutoLimit;
			$this->trophyID = $this->item->trophyID;
			
			$this->isOffer = $this->item->isOffer;
			if ($this->item->isOffer) {
				$dateTime = DateUtil::getDateTimeByTimestamp($this->item->offerEnd);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->offerEnd = $dateTime->format('c');
			}
			$this->offerPrice = $this->item->offerPrice;
			
			$this->price = $this->item->price;
			$this->buyLimit = $this->item->buyLimit;
			$this->productLimit = $this->item->productLimit;
			$this->sortOrder = $this->item->sortOrder;
			
			$this->expirationStatus = $this->item->expirationStatus;
			if ($this->item->expirationDate) {
				$dateTime = DateUtil::getDateTimeByTimestamp($this->item->expirationDate);
				$dateTime->setTimezone(WCF::getUser()->getTimeZone());
				$this->expirationDate = $dateTime->format('c');
			}
			
			$this->autoDisable = $this->item->autoDisable;
			
			foreach ($this->item->getCategories() as $category) {
				$this->categoryIDs[] = $category->categoryID;
			}
			
			foreach ($this->item->getItemContents() as $languageID => $content) {
				$this->content[$languageID] = $content->content;
				$this->subject[$languageID] = $content->subject;
				$this->teaser[$languageID] = $content->teaser;
				$this->imageID[$languageID] = $content->imageID;
			}
			
			$this->readImages();
			
			// conditions
			$conditions = $this->item->getConditions();
			foreach ($conditions as $condition) {
				$this->conditions[$condition->getObjectType()->conditiongroup][$condition->objectTypeID]->getProcessor()->setData($condition);
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['categoryIDs']) && is_array($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
		
		if (isset($_REQUEST['id'])) $this->shopItemID = intval($_REQUEST['id']);
		$this->item = new JCoinsShopItem($this->shopItemID);
		if (!$this->item->shopItemID) {
			throw new IllegalLinkException();
		}
		
		if ($this->item->isMultilingual) $this->isMultilingual = 1;
		
		if (!WCF::getSession()->getPermission('admin.shopJCoins.canManage')) {
			throw new PermissionDeniedException();
		}
		
		// need admin.user.accessibleGroups with groups > id 3
		$groups = explode(',', WCF::getSession()->getPermission('admin.user.accessibleGroups'));
		if (empty($groups[0])) {
			throw new PermissionDeniedException();
		}
		$ok = 0;
		foreach ($groups as $id) {
			if ($id > 3) {
				$ok = 1;
				break;
			}
		}
		if (!$ok) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'action' => 'edit',
				'item' => $this->item,
				'shopItemID' => $this->item->shopItemID
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();
		
		// texts
		$content = [];
		if ($this->isMultilingual) {
			foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
				$content[$language->languageID] = [
						'content' => !empty($this->content[$language->languageID]) ? $this->content[$language->languageID] : '',
						'subject' => !empty($this->subject[$language->languageID]) ? $this->subject[$language->languageID] : '',
						'teaser' => !empty($this->teaser[$language->languageID]) ? $this->teaser[$language->languageID] : '',
						'htmlInputProcessor' => isset($this->htmlInputProcessors[$language->languageID]) ? $this->htmlInputProcessors[$language->languageID] : null,
						'imageID' => !empty($this->imageID[$language->languageID]) ? $this->imageID[$language->languageID] : null
				];
			}
		}
		else {
			$content[0] = [
					'content' => !empty($this->content[0]) ? $this->content[0] : '',
					'subject' => !empty($this->subject[0]) ? $this->subject[0] : '',
					'teaser' => !empty($this->teaser[0]) ? $this->teaser[0] : '',
					'htmlInputProcessor' => isset($this->htmlInputProcessors[0]) ? $this->htmlInputProcessors[0] : null,
					'imageID' => !empty($this->imageID[0]) ? $this->imageID[0] : null
			];
		}
		
		// data
		// special treatment of product limit
		if ($this->type->typeTitle == 'textItem' && $this->textItemAutoLimit) {
			$this->productLimit = $this->textItemCount + $this->item->sold;
		}
		
		$data = [
				'itemTitle' => $this->itemTitle,
				'sender' => $this->sender,
				'leaveConversation' => $this->leaveConversation,
				'isDisabled' => $this->isDisabled,
				'showStartPage' => $this->showStartPage,
				'isMultilingual' => $this->isMultilingual,
				'time' => TIME_NOW,
				'changeTime' => TIME_NOW,
				
				'typeID' => $this->typeID,
				'typeDes' => $this->typeID ? $this->type->typeTitle : '',
				
				'filename' => $this->filename,
				'handsonNames' => $this->handsonNames,
				'membershipDays' => $this->membershipDays,
				'membershipID' => $this->membershipID,
				'membershipWarn' => $this->membershipWarn,
				'textItem' => $this->textItem,
				'textItemAutoLimit' => $this->textItemAutoLimit,
				'trophyID' => $this->trophyID ? $this->trophyID : null,
				
				'isOffer' => $this->isOffer,
				'offerEnd' => $this->isOffer == 1 ? $this->offerEndObj->getTimestamp() : 0,
				'offerPrice' => $this->offerPrice,
				
				'price' => $this->price,
				'buyLimit' => $this->buyLimit,
				'productLimit' => $this->productLimit,
				'sortOrder' => $this->sortOrder,
				
				'expirationStatus' => $this->expirationStatus,
				'expirationDate' => $this->expirationStatus == 1 ? $this->expirationDateObj->getTimestamp() : 0,
				
				'autoDisable' => $this->autoDisable
		];
		
		$this->objectAction = new JCoinsShopItemAction([$this->item], 'update', [
				'data' => array_merge($this->additionalFields, $data), 
				'content' => $content,
				'categoryIDs' => $this->categoryIDs
		]);
		$this->objectAction->executeAction();
		
		// transform conditions array into one-dimensional array
		$conditions = [];
		foreach ($this->conditions as $groupedObjectTypes) {
			$conditions = array_merge($conditions, $groupedObjectTypes);
		}
		ConditionHandler::getInstance()->updateConditions($this->item->shopItemID, $this->item->getConditions(), $conditions);
		
		$this->saved();
		
		// show success
		WCF::getTPL()->assign([
				'success' => true
		]);
	}
}
