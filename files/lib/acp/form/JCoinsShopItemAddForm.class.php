<?php
namespace wcf\acp\form;
use wcf\data\jcoins\shop\category\JCoinsShopCategory;
use wcf\data\jcoins\shop\category\JCoinsShopCategoryNodeTree;
use wcf\data\jcoins\shop\item\JCoinsShopItemAction;
use wcf\data\jcoins\shop\item\type\JCoinsShopItemType;
use wcf\data\jcoins\shop\item\type\JCoinsShopItemTypeList;
use wcf\data\media\ViewableMediaList;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\trophy\Trophy;
use wcf\data\trophy\TrophyList;
use wcf\data\user\User;
use wcf\data\user\group\UserGroup;
use wcf\data\user\UserProfile;
use wcf\form\AbstractForm;
use wcf\system\category\CategoryHandler;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * Shows the JCoins Shop Item add form.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.shopJCoins.item.add';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.shopJCoins.canManage'];
	
	/**
	 * general data
	 */
	public $availableGroups = [];
	public $availableLanguages = [];
	public $availableTrophies = [];
	public $availableTypes = [];
	public $htmlInputProcessors = [];
	
	/**
	 * shop item data
	 */
	public $fileUpload = [];
	public $filename = '';
	public $uploadedFilename = '';
	
	public $shopItemID = 0;
	public $itemTitle = '';
	public $sender = '';
	public $leaveConversation = 0;
	public $isDisabled = 0;
	public $showStartPage = 1;
	public $type = null;
	public $typeID = 0;
	
	public $content = [];
	public $subject = [];
	public $teaser = [];
	
	public $handsonNames = '';
	public $membershipDays = 30;
	public $membershipID = 0;
	public $membershipWarn = 1;
	public $textItem = '';
	public $textItemAutoLimit = 1;
	public $textItemCount = 0;
	public $trophyID = null;
	
	public $isOffer = 0;
	public $offerEnd = '';
	public $offerEndObj;
	public $offerPrice = 1;
	
	public $price = 1;
	public $buyLimit = 0;
	public $productLimit = 0;
	public $sortOrder = 0;
	
	public $expirationStatus = 0;
	public $expirationDate = '';
	public $expirationDateObj;
	
	public $autoDisable = 0;
	
	public $conditions = [];
	
	public $imageID = [];
	public $images = [];
	
	public $categoryIDs = [];
	public $categoryList;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		$this->availableTypes = new JCoinsShopItemTypeList();
		$this->availableTypes->readObjects();
		
		// trophies
		$this->availableTrophies = new TrophyList;
		$this->availableTrophies->getConditionBuilder()->add('isDisabled = ?', [0]);
		$this->availableTrophies->getConditionBuilder()->add('awardAutomatically = ?', [0]);
		$this->availableTrophies->readObjects();
		
		// read categories
		$excludedCategoryIDs = array_diff(JCoinsShopCategory::getAccessibleCategoryIDs(), JCoinsShopCategory::getAccessibleCategoryIDs(['canUseCategory']));
		$categoryTree = new JCoinsShopCategoryNodeTree('com.uz.jcoins.shop.category', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();
		$this->categoryList->setMaxDepth(0);
		
		// check pre-selected categories and add parent categories
		foreach ($this->categoryIDs as $categoryID) {
			$category = JCoinsShopCategory::getCategory($categoryID);
			if ($category) {
				$this->categoryIDs[] = $category->categoryID;
				
				if ($category->parentCategoryID) {
					$this->categoryIDs[] = $category->parentCategoryID;
				}
			}
		}
		$this->categoryIDs = array_unique($this->categoryIDs);
		
		// get accessible groups, exclude admin/owner group (no OWNER in 3.1)
		$this->availableGroups = UserGroup::getAccessibleGroups(array(), array(UserGroup::GUESTS, UserGroup::EVERYONE, UserGroup::USERS));
		foreach ($this->availableGroups as $key => $group) {
			if ($group->isAdminGroup()) {
				unset($this->availableGroups[$key]);
			}
		}
		
		// conditions
		$objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.uz.jcoins.shop.condition');
		foreach ($objectTypes as $objectType) {
			if (!$objectType->conditiongroup) continue;
			
			if (!isset($groupedObjectTypes[$objectType->conditiongroup])) {
				$groupedObjectTypes[$objectType->conditiongroup] = [];
			}
			
			$groupedObjectTypes[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
		}
		$this->conditions = $groupedObjectTypes;
		
		parent::readData();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
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
		
		// languages
		$this->isMultilingual = 0;
		$this->availableLanguages = LanguageFactory::getInstance()->getLanguages();
		if (count($this->availableLanguages) > 1) $this->isMultilingual = 1;
		
		// categories
		if (isset($_REQUEST['categoryIDs'])) $this->categoryIDs = ArrayUtil::toIntegerArray($_REQUEST['categoryIDs']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'action' => 'add',
				
				'availableGroups' => $this->availableGroups,
				'availableLanguages' => $this->availableLanguages,
				'availableTrophies' => $this->availableTrophies,
				'availableTypes' => $this->availableTypes,
				
				'itemTitle' => $this->itemTitle,
				'sender' => $this->sender,
				'leaveConversation' => $this->leaveConversation,
				'isDisabled' => $this->isDisabled,
				'showStartPage' => $this->showStartPage,
				
				'isMultilingual' => $this->isMultilingual,
				'content' => $this->content,
				'subject' => $this->subject,
				'teaser' => $this->teaser,
				'imageID' => $this->imageID,
				'images' => $this->images,
				
				'typeID' => $this->typeID,
				
				'filename' => $this->filename,
				'uploadedFilename' => $this->uploadedFilename,
				'handsonNames' => $this->handsonNames,
				'membershipDays' => $this->membershipDays,
				'membershipID' => $this->membershipID,
				'membershipWarn' => $this->membershipWarn,
				'textItem' => $this->textItem,
				'textItemAutoLimit' => $this->textItemAutoLimit,
				'trophyID' => $this->trophyID,
				
				'isOffer' => $this->isOffer,
				'offerEnd' => $this->offerEnd,
				'offerPrice' => $this->offerPrice,
				
				'price' => $this->price,
				'buyLimit' => $this->buyLimit,
				'productLimit' => $this->productLimit,
				'sortOrder' => $this->sortOrder,
				
				'expirationStatus' => $this->expirationStatus,
				'expirationDate' => $this->expirationDate,
				
				'autoDisable' => $this->autoDisable,
				
				'groupedObjectTypes' => $this->conditions,
				
				'categoryIDs' => $this->categoryIDs,
				'categoryList' => $this->categoryList
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// general
		$this->leaveConversation = $this->isDisabled = $this->showStartPage = $this->isOffer = 0;
		if (isset($_POST['itemTitle'])) $this->itemTitle = StringUtil::trim($_POST['itemTitle']);
		if (isset($_POST['sender'])) $this->sender = StringUtil::trim($_POST['sender']);
		if (isset($_POST['leaveConversation'])) $this->leaveConversation = 1;
		if (isset($_POST['isDisabled'])) $this->isDisabled = 1;
		if (isset($_POST['showStartPage'])) $this->showStartPage = 1;
		
		if (isset($_POST['typeID'])) $this->typeID = intval($_POST['typeID']);
		
		$this->textItemAutoLimit = 0;
		if (isset($_POST['handsonNames'])) $this->handsonNames = StringUtil::trim($_POST['handsonNames']);
		if (isset($_POST['membershipDays'])) $this->membershipDays = intval($_POST['membershipDays']);
		if (isset($_POST['membershipID'])) $this->membershipID = intval($_POST['membershipID']);
		if (isset($_POST['membershipWarn'])) $this->membershipWarn = intval($_POST['membershipWarn']);
		if (isset($_POST['textItem'])) $this->textItem = StringUtil::trim($_POST['textItem']);
		if (isset($_POST['textItemAutoLimit'])) $this->textItemAutoLimit = 1;
		if (isset($_POST['trophyID'])) $this->trophyID = intval($_POST['trophyID']);
		if (isset($_POST['price'])) $this->price = intval($_POST['price']);
		if (isset($_POST['buyLimit'])) $this->buyLimit = intval($_POST['buyLimit']);
		if (isset($_POST['productLimit'])) $this->productLimit = intval($_POST['productLimit']);
		if (isset($_POST['sortOrder'])) $this->sortOrder = intval($_POST['sortOrder']);
		
		$this->isOffer = 0;
		if (isset($_POST['isOffer'])) $this->isOffer = 1;
		if ($this->isOffer == 1 && isset($_POST['offerEnd'])) {
			$this->offerEnd = $_POST['offerEnd'];
			$this->offerEndObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->offerEnd);
		}
		if (isset($_POST['offerPrice'])) $this->offerPrice = intval($_POST['offerPrice']);
		
		if (isset($_POST['content']) && is_array($_POST['content'])) $this->content = ArrayUtil::trim($_POST['content']);
		if (isset($_POST['subject']) && is_array($_POST['subject'])) $this->subject = ArrayUtil::trim($_POST['subject']);
		if (isset($_POST['teaser']) && is_array($_POST['teaser'])) $this->teaser = ArrayUtil::trim($_POST['teaser']);
		
		if (WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
			if (isset($_POST['imageID']) && is_array($_POST['imageID'])) $this->imageID = ArrayUtil::toIntegerArray($_POST['imageID']);
			
			$this->readImages();
		}
		
		$this->expirationStatus = 0;
		if (isset($_POST['expirationStatus'])) $this->expirationStatus = intval($_POST['expirationStatus']);
		if ($this->expirationStatus == 1 && isset($_POST['expirationDate'])) {
			$this->expirationDate = $_POST['expirationDate'];
			$this->expirationDateObj = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->expirationDate);
		}
		
		$this->autoDisable = 0;
		if (isset($_POST['autoDisable'])) $this->autoDisable = intval($_POST['autoDisable']);
		
		if (isset($_POST['filename'])) $this->filename = StringUtil::trim($_POST['filename']);
		if (isset($_POST['uploadedFilename'])) $this->uploadedFilename = StringUtil::trim($_POST['uploadedFilename']);
		if (isset($_FILES['fileUpload'])) $this->fileUpload = $_FILES['fileUpload'];
		
		// conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->readFormParameters();
			}
		}
	}
	
	/**
	 * Reads the box images.
	 */
	protected function readImages() {
		if (!empty($this->imageID)) {
			$mediaList = new ViewableMediaList();
			$mediaList->setObjectIDs($this->imageID);
			$mediaList->readObjects();
				
			foreach ($this->imageID as $languageID => $imageID) {
				$image = $mediaList->search($imageID);
				if ($image !== null && $image->isImage) {
					$this->images[$languageID] = $image;
				}
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
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
			$this->productLimit = $this->textItemCount;
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
		
		// save
		$objectAction = new JCoinsShopItemAction([], 'create', [
				'data' => array_merge($this->additionalFields, $data),
				'content' => $content,
				'categoryIDs' => $this->categoryIDs
		]);
		$objectAction->executeAction();
		
		$returnValues = $objectAction->getReturnValues();
		$this->shopItemID = $returnValues['returnValues']->shopItemID;
		
		// transform conditions array into one-dimensional array
		$conditions = [];
		foreach ($this->conditions as $groupedObjectTypes) {
			$conditions = array_merge($conditions, $groupedObjectTypes);
		}
		ConditionHandler::getInstance()->createConditions($this->shopItemID, $conditions);
		
		// Reset values
		$this->itemTitle = '';
		$this->sender = '';
		$this->leaveConversation = 0;
		$this->isDisabled = 0;
		$this->showStartPage = 1;
		
		$this->content = [];
		$this->subject = [];
		$this->teaser = [];
		$this->images = [];
		$this->imageID = [];
		
		$this->type = null;
		$this->typeID = 0;
		
		$this->filename = '';
		$this->uploadedFilename = '';
		$this->handsonNames = '';
		$this->membershipDays = 30;
		$this->membershipID = 0;
		$this->membershipWarn = 1;
		$this->textItem = '';
		$this->textItemAutoLimit = 1;
		$this->trophyID = null;
		
		$this->isOffer = 0;
		$this->offerEnd = '';
		$this->offerPrice = 1;
		
		$this->price = 1;
		$this->buyLimit = 0;
		$this->productLimit = 0;
		$this->sortOrder = 0;
		
		$this->expirationDate = '';
		$this->expirationStatus = 0;
		
		$this->autoDisable = 0;
		
		// reset conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->reset();
			}
		}
		
		$this->saved();
		
		// Show success message
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		// General
		// item title required, max 80 chars
		if (empty($this->itemTitle)) throw new UserInputException('itemTitle', 'required');
		if (mb_strlen($this->itemTitle) > 80) throw new UserInputException('itemTitle', 'tooLong');
		
		if ($this->isOffer == 1) {
			if (empty($this->offerEnd)) {
				throw new UserInputException('offerEnd');
			}
		
			if (!$this->offerEndObj || $this->offerEndObj->getTimestamp() < TIME_NOW) {
				throw new UserInputException('offerEnd', 'invalid');
			}
		}
		
		// sender required, max 255 chars
		if (empty($this->sender)) throw new UserInputException('sender', 'required');
		if (mb_strlen($this->sender) > 255) throw new UserInputException('sender', 'tooLong');
		$user = User::getUserByUsername($this->sender);
		if (!$user->userID) {
			throw new UserInputException('sender', 'invalid');
		}
		
		// validate category ids
		if (empty($this->categoryIDs)) {
			throw new UserInputException('categoryIDs');
		}
		$categories = [];
		foreach ($this->categoryIDs as $categoryID) {
			$category = CategoryHandler::getInstance()->getCategory($categoryID);
			if ($category === null) throw new UserInputException('categoryIDs');
				
			$category = new JCoinsShopCategory($category);
			if (!$category->isAccessible() || !$category->getPermission('canUseCategory')) throw new UserInputException('categoryIDs');
			$categories[] = $category;
		}
		
		// type must exist
		if (!$this->typeID) throw new UserInputException('typeID', 'missing');
		$this->type = JCoinsShopItemType::getTypeByID($this->typeID);
		
		// expiration status
		if ($this->expirationStatus != 0 && $this->expirationStatus != 1) {
			throw new UserInputException('expirationStatus');
		}
		if ($this->expirationStatus == 1) {
			if (empty($this->expirationDate)) {
				throw new UserInputException('expirationDate');
			}
				
			if (!$this->expirationDateObj || $this->expirationDateObj->getTimestamp() < TIME_NOW) {
				throw new UserInputException('expirationDate', 'invalid');
			}
		}
		
		// membership
		if ($this->type->typeTitle == 'membership') {
			// check group
			if (!isset($this->availableGroups[$this->membershipID])) {
				throw new UserInputException('membershipID', 'invalidSelection');
			}
			
			// check warning
			if ($this->membershipWarn > $this->membershipDays) {
				throw new UserInputException('membershipWarn', 'moreThanDays');
			}
			
			// check max. duration
			if ($this->membershipDays > 6000) {
				throw new UserInputException('membershipDays', 'invalid');
			}
		}
		
		// paid download
		if ($this->type->typeTitle == 'download') {
			// filename used?
			
			if (!empty($this->filename)) {
				if (!file_exists(WCF_DIR.'jCoinsFiles/'.$this->filename)) {
					throw new UserInputException('filename', 'notExists');
				}
			}
			else {
				// uploaded?
				if (empty($this->fileUpload['name'])) {
					throw new UserInputException('fileUpload');
				}
				
				// exists?
				if (file_exists(WCF_DIR.'jCoinsFiles/'.$this->fileUpload['name'])) {
					throw new UserInputException('fileUpload', 'exists');
				}
				
				// move
				if (!@move_uploaded_file($this->fileUpload['tmp_name'], WCF_DIR.'jCoinsFiles/'.$this->fileUpload['name'])) {
					throw new UserInputException('fileUpload', 'uploadFailed');
				}
				
				// set filename
				$this->filename = $this->fileUpload['name'];
			}
		}
		
		// paid hands-on
		if ($this->type->typeTitle == 'handson') {
			if (empty($this->handsonNames)) {
				throw new UserInputException('handsonNames', 'notConfigured');
			}
			
			// check names
			$names = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $this->handsonNames)));
			if (!count($names)) throw new UserInputException('handsonNames', 'notConfigured');
			if (count($names) > 20) throw new UserInputException('handsonNames', 'tooMany');
			foreach ($names as $name => $user) {
				if ($user === null) {
					WCF::getTPL()->assign('name', $name);
					throw new UserInputException('handsonNames', 'invalid');
				}
			}
		}
		
		// paid text item
		if ($this->type->typeTitle == 'textItem') {
			if (empty($this->textItem)) {
				throw new UserInputException('textItem');
			}
			
			$lines = ArrayUtil::trim(explode("\n", $this->textItem));
			$this->textItem = implode("\n", $lines);
			$re = '/(\d+):(.+)/';
			
			$this->textItemCount = 0;
			
			foreach ($lines as $line) {
				preg_match('/^(\d+):(.+)/', $line, $matches);
				if (empty($matches)) {
					throw new UserInputException('textItem', 'invalid');
				}
				if (mb_strlen($matches[2]) > 255) {
					throw new UserInputException('textItem', 'tooLong');
				}
				
				$this->textItemCount += intval($matches[1]);
			}
		}
		
		// trophy
		if ($this->type->typeTitle == 'trophy') {
			$trophy = new Trophy($this->trophyID);
			if (!$trophy->trophyID) {
				throw new UserInputException('trophyID', 'invalid');
			}
		}
		
		// conditions
		foreach ($this->conditions as $conditions) {
			foreach ($conditions as $condition) {
				$condition->getProcessor()->validate();
			}
		}
		
		// texts must exist
		if ($this->isMultilingual) {
			foreach ($this->availableLanguages as $language) {
				if (empty($this->subject[$language->languageID])) throw new UserInputException('subject'.$language->languageID);
				if (empty($this->teaser[$language->languageID])) throw new UserInputException('teaser'.$language->languageID);
				if (empty($this->content[$language->languageID])) throw new UserInputException('content'.$language->languageID);
				
				$this->htmlInputProcessors[$language->languageID] = new HtmlInputProcessor();
				$this->htmlInputProcessors[$language->languageID]->process($this->content[$language->languageID], 'com.uz.jcoins.shop.content', 0);
			}
		}
		else {
			if (empty($this->subject[0])) throw new UserInputException('subject');
			if (empty($this->teaser[0])) throw new UserInputException('teaser');
			if (empty($this->content[0])) throw new UserInputException('content');
			
			$this->htmlInputProcessors[0] = new HtmlInputProcessor();
			$this->htmlInputProcessors[0]->process($this->content[0], 'com.uz.jcoins.shop.content', 0);
		}
	}
}
