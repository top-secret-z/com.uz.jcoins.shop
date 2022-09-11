<?php
namespace wcf\data\jcoins\shop\item;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\condition\ConditionList;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\ConversationAction;
use wcf\data\conversation\ConversationEditor;
use wcf\data\IToggleAction;
use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\data\jcoins\shop\item\content\JCoinsShopItemContent;
use wcf\data\jcoins\shop\item\content\JCoinsShopItemContentEditor;
use wcf\data\jcoins\shop\item\content\JCoinsShopItemContentList;
use wcf\data\jcoins\shop\membership\JCoinsShopMembership;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipAction;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipEditor;
use wcf\data\jcoins\shop\transaction\JCoinsShopTransactionEditor;
use wcf\data\package\Package;
use wcf\data\trophy\Trophy;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserList;
use wcf\data\user\UserProfile;
use wcf\data\user\group\UserGroup;
use wcf\data\user\trophy\UserTrophyAction;
use wcf\system\cache\builder\ConditionCacheBuilder;
use wcf\system\condition\ConditionHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\log\modification\ConversationModificationLogHandler;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\request\LinkHandler;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Executes JCoins Shop Item actions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsShopItemEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.shopJCoins.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.shopJCoins.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['create', 'delete', 'toggle', 'update'];
	
	/**
	 * @inheritDoc
	 */
	protected $allowGuestAccess = ['getPreview'];
	
	/**
	 * data
	 */
	public $item = null;
	public $trophy = null;
	
	/**
	 * @inheritDoc
	 */
	public function create() {
		$item = parent::create();
		
		// save item content
		if (!empty($this->parameters['content'])) {
			foreach ($this->parameters['content'] as $languageID => $content) {
				if (!empty($content['htmlInputProcessor'])) {
					$content['content'] = $content['htmlInputProcessor']->getHtml();
				}
				
				$itemContent = JCoinsShopItemContentEditor::create([
						'shopItemID' => $item->shopItemID,
						'languageID' => $languageID ?: null,
						'subject' => $content['subject'],
						'teaser' => $content['teaser'],
						'content' => $content['content'],
					'imageID' => $content['imageID']
				]);
				$itemContentEditor = new JCoinsShopItemContentEditor($itemContent);
				
				// save embedded objects
				if (!empty($content['htmlInputProcessor'])) {
					$content['htmlInputProcessor']->setObjectID($itemContent->contentID);
					if (MessageEmbeddedObjectManager::getInstance()->registerObjects($content['htmlInputProcessor'])) {
						$itemContentEditor->update(['hasEmbeddedObjects' => 1]);
					}
				}
			}
		}
		
		// save categories
		$itemEditor = new JCoinsShopItemEditor($item);
		$itemEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$itemEditor->setCategoryIDs($this->parameters['categoryIDs']);
		
		return $item;
	}
	
	/**
	 * @inheritDoc
	 */
	public function delete() {
		// delete any conditions
		ConditionHandler::getInstance()->deleteConditions('com.uz.jcoins.shop.condition', $this->objectIDs);
		
		// remove activity event
		UserActivityEventHandler::getInstance()->removeEvents('com.uz.jcoins.shop.recentActivityEvent.purchase', $this->objectIDs);
		
		return parent::delete();
	}
	
	/**
	 * @inheritDoc
	 */
	public function update() {
		parent::update();
		
		foreach ($this->getObjects() as $item) {
			// handle categories
			if (isset($this->parameters['categoryIDs'])) {
				$item->updateCategoryIDs($this->parameters['categoryIDs']);
			}
			
			// update item content
			if (!empty($this->parameters['content'])) {
				foreach ($this->parameters['content'] as $languageID => $content) {
					if (!empty($content['htmlInputProcessor'])) {
						$content['content'] = $content['htmlInputProcessor']->getHtml();
					}
					
					$itemContent = JCoinsShopItemContent::getItemContent($item->shopItemID, ($languageID ?: null));
					$itemContentEditor = null;
					if ($itemContent !== null) {
						// update
						$itemContentEditor = new JCoinsShopItemContentEditor($itemContent);
						$itemContentEditor->update([
								'content' => $content['content'],
								'subject' => $content['subject'],
								'teaser' => $content['teaser'],
								'imageID' => $content['imageID']
						]);
					}
					else {
						$itemContent = JCoinsShopItemContentEditor::create([
								'shopItemID' => $item->shopItemID,
								'languageID' => $languageID ?: null,
								'content' => $content['content'],
								'subject' => $content['subject'],
								'teaser' => $content['teaser'],
								'imageID' => $content['imageID']
						]);
						$itemContentEditor = new JCoinsShopItemContentEditor($itemContent);
					}
					
					// save embedded objects
					if (!empty($content['htmlInputProcessor'])) {
						$content['htmlInputProcessor']->setObjectID($itemContent->contentID);
						if ($itemContent->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($content['htmlInputProcessor'])) {
							$itemContentEditor->update(['hasEmbeddedObjects' => $itemContent->hasEmbeddedObjects ? 0 : 1]);
						}
					}
				}
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
	
	/**
	 * @inheritDoc
	 */
	public function toggle() {
		foreach ($this->objects as $item) {
			$item->update([
					'isDisabled' => $item->isDisabled ? 0 : 1
			]);
		}
	}
	
	/**
	 * Validates the copy action.
	 */
	public function validateCopy() {
		$this->item = new JCoinsShopItem($this->parameters['objectID']);
		if (!$this->item->shopItemID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Executes the copy action.
	 */
	public function copy() {
		$data = $this->item->getData();
		$oldShopItemID = $data['shopItemID'];
		unset($data['shopItemID']);
		
		// copy item, set to disable, set time
		$data['isDisabled'] = 1;
		$data['time'] = TIME_NOW;
		$data['changeTime'] = TIME_NOW;
		$data['sold'] = 0;
		$data['earnings'] = 0;
		$data['itemTitle'] = substr($data['itemTitle'], 0, 250) . ' (2)';
		$this->parameters['data'] = $data;
		
		// copy categories
		$categoryIDs = [];
		$temp = $this->item->getCategories();
		if (count($temp)) {
			foreach ($temp as $category) {
				$categoryIDs[] = $category->categoryID;
			}
		}
		$this->parameters['categoryIDs'] = $categoryIDs;
		
		$item = $this->create();
		
		// copy conditions
		$definitionIDs = [];
		$sql = "SELECT		definitionID
				FROM		wcf".WCF_N."_object_type_definition
				WHERE		definitionName LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(['com.uz.jcoins.shop.condition%']);
		while ($row = $statement->fetchArray()) {
			$definitionIDs[] = $row['definitionID'];
		}
		
		foreach($definitionIDs as $definitionID) {
			$objectTypeIDs = [];
			$sql = "SELECT		objectTypeID
					FROM		wcf".WCF_N."_object_type
					WHERE		definitionID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$definitionID]);
			while ($row = $statement->fetchArray()) {
				$objectTypeIDs[] = $row['objectTypeID'];
			}
			
			$conditionList = new ConditionList();
			$conditionList->getConditionBuilder()->add('objectTypeID IN (?)', [$objectTypeIDs]);
			$conditionList->getConditionBuilder()->add('objectID = ?', [$oldShopItemID]);
			$conditionList->readObjects();
			$conditions = $conditionList->getObjects();
				
			if (count($conditions)) {
				WCF::getDB()->beginTransaction();
				$sql = "INSERT INTO wcf".WCF_N."_condition
								(objectID, objectTypeID, conditionData)
						VALUES	(?, ?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				
				foreach($conditions as $condition) {
					$statement->execute([$item->shopItemID, $condition->objectTypeID, serialize($condition->conditionData)]);
				}
				WCF::getDB()->commitTransaction();
			}
		}
		
		ConditionCacheBuilder::getInstance()->reset();
		
		// copy content
		$contentList = new JCoinsShopItemContentList();
		$contentList->getConditionBuilder()->add('shopItemID = ?', [$oldShopItemID]);
		$contentList->readObjects();
		$contents = $contentList->getObjects();
		
		WCF::getDB()->beginTransaction();
		$sql = "INSERT INTO wcf".WCF_N."_jcoins_shop_item_content
							(shopItemID, languageID, content, subject, teaser, imageID)
				VALUES	(?, ?, ?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		foreach($contents as $content) {
			$statement->execute([$item->shopItemID, $content->languageID, $content->content, $content->subject, $content->teaser, $content->imageID]);
		}
		WCF::getDB()->commitTransaction();
		
		return [
				'redirectURL' => LinkHandler::getInstance()->getLink('JCoinsShopItemEdit', [
						'id' => $item->shopItemID
				])
		];
	}
	
	/**
	 * Validates the get getPreview dialog action.
	 */
	public function validateGetPreview() {
		if (!isset($this->parameters['shopItem'])) throw new IllegalLinkException();
		$this->item = new JCoinsShopItem($this->parameters['shopItem']);
		if (!$this->item->shopItemID) throw new IllegalLinkException();
		if (!$this->item->canSee()) throw new PermissionDeniedException();
	}
	
	/**
	 * Executes the get preview dialog action.
	 */
	public function getPreview() {
		WCF::getTPL()->assign([
				'item' => $this->item
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsShopPreviewDialog')
		];
	}
	
	/**
	 * Validates the get buy dialog action.
	 */
	public function validateGetBuyDialog() {
		if (!isset($this->parameters['shopItem'])) throw new IllegalLinkException();
		$this->item = new JCoinsShopItem($this->parameters['shopItem']);
		if (!$this->item->shopItemID) {
			throw new IllegalLinkException();
		}
		
		if (!$this->item->canBuy()) throw new PermissionDeniedException();
		if (!$this->item->isAllowed()) throw new PermissionDeniedException();
		
		// membership ID
		if ($this->item->typeDes == 'membership' && !$this->item->membershipID) {
			// disable product
			$editor = new JCoinsShopItemEditor($this->item);
			$editor->update(['isDisabled' => 1]);
			
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Executes the get buy dialog action.
	 */
	public function getBuyDialog() {
		$notice = '';
		$language = WCF::getUser()->getLanguage();
		
		if ($this->item->isBuyer()) {
			$notice .= $language->getDynamicVariable('wcf.jcoins.shop.product.bought');
		}
		
		if ($this->item->typeDes == 'membership' && $this->item->isMember()) {
			if (!empty($notice)) $notice .= '<br>';
			$notice .= $language->getDynamicVariable('wcf.jcoins.shop.product.membership.extension');
		}
		
		WCF::getTPL()->assign([
				'item' => $this->item,
				'notice' => $notice
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('jCoinsShopBuyDialog')
		];
	}
	
	/**
	 * Validates the buy action.
	 */
	public function validateBuy() {
		if (!isset($this->parameters['shopItem'])) throw new IllegalLinkException();
		$this->item = new JCoinsShopItem($this->parameters['shopItem']);
		if (!$this->item->shopItemID) throw new IllegalLinkException();
		if (!$this->item->canBuy()) throw new PermissionDeniedException();
		if (!$this->item->isAllowed()) throw new PermissionDeniedException();
		
		// prevent membership in admin group
		if ($this->item->typeDes == 'membership') {
			$group = UserGroup::getGroupByID($this->item->membershipID);
			if ($group->isAdminGroup()) {
				throw new PermissionDeniedException();
			}
		}
		
		// trophy
		if ($this->item->typeDes == 'trophy') {
			$this->trophy = new Trophy($this->item->trophyID);
			if (!$this->trophy->trophyID || $this->trophy->isDisabled) {
				throw new NamedUserException(WCF::getUser()->getLanguage()->get('wcf.jcoins.shop.buy.error.trophy'));
			}
		}
	}
	
	/**
	 * Executes the buy action.
	 */
	public function buy() {
		$shopItem = $this->item;	// too many ;-)
		
		// conversation and other data
		$user = WCF::getUser();
		$language = $user->getLanguage();
		
		$data = [];
		$sender = User::getUserByUsername($shopItem->sender);
		if (!$sender->userID) {
			throw new NamedUserException($language->get('wcf.jcoins.shop.buy.error.sender'));
		}
		
		$data['subject'] = $language->getDynamicVariable('wcf.jcoins.shop.conversation.subject');
		$data['userID'] = $sender->userID;
		$data['username'] = $sender->username;
		$data['time'] = TIME_NOW;
		$participants[] = $user->userID;
		$message = $language->getDynamicVariable('wcf.jcoins.shop.conversation.hallo', ['username' => $user->username]);
		$message .= '<br>';
		$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.item', ['shopItem' => $shopItem]);
		
		$detail = '';
		
		// membership
		if ($shopItem->typeDes == 'membership') {
			// group must exist
			$group = UserGroup::getGroupByID($shopItem->membershipID);
			if ($group->groupID) {
				$endDate = $warnDate = $extension = 0;
				
				// check whether user already possesses membership; create or extend
				$membershipID = $shopItem->isMember();
				if ($membershipID) {
					$membership = new JCoinsShopMembership($membershipID);
					$endDate = $membership->endDate + $shopItem->membershipDays * 86400;
					if ($endDate > 2147483645) $endDate = 2147483645;
					$warnDate = $shopItem->membershipWarn > 0 ? $endDate - $shopItem->membershipWarn * 86400 : 0;
					
					$membershipEditor = new JCoinsShopMembershipEditor($membership);
					$membershipEditor->update([
							'endDate' => $endDate,
							'warnDate' => $warnDate
					]);
					
					$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.membership.extended', ['endDate' => $endDate]);
				}
				else {
					// check for existing, but inactive membership
					$membershipID = $shopItem->isInactiveMember();
					if ($membershipID) {
						$membership = new JCoinsShopMembership($membershipID);
						$membershipEditor = new JCoinsShopMembershipEditor($membership);
						$endDate = TIME_NOW + $shopItem->membershipDays * 86400;
						if ($endDate > 2147483645) $endDate = 2147483645;
						$warnDate = $shopItem->membershipWarn > 0 ? $endDate - $shopItem->membershipWarn * 86400 : 0;
						
						$membershipEditor->update([
								'isActive' => 1,
								'endDate' => $endDate,
								'startDate' => TIME_NOW,
								'warnDate' => $warnDate
						]);
					}
					else {
						$endDate = TIME_NOW + $shopItem->membershipDays * 86400;
						if ($endDate > 2147483645) $endDate = 2147483645;
						
						$objectAction = new JCoinsShopMembershipAction([], 'create', [
								'data' => [
										'shopItemID' => $shopItem->shopItemID,
										'groupID' => $shopItem->membershipID,
										'userID' => $user->userID,
										'endDate' => $endDate,
										'isActive' => 1,
										'startDate' => TIME_NOW,
										'warnDate' => $shopItem->membershipWarn > 0 ? $endDate - $shopItem->membershipWarn * 86400 : 0
								]
						]);
						$objectAction->executeAction();
					}
					
					$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.membership.end', ['endDate' => $endDate]);
				}
				
				// add to group in any case
				$action = new UserAction([$user->userID], 'addToGroups', [
						'groups' => [$shopItem->membershipID],
						'deleteOldGroups' => false,
						'addDefaultGroups' => false
				]);
				$action->executeAction();
				
				$detail = $group->groupName;
			}
		}
		
		// download product - just send a conversation
		if ($shopItem->typeDes == 'download') {
			$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.download');
			
			$detail = $shopItem->filename;
		}
		
		// handson product - just send a conversation
		if ($shopItem->typeDes == 'handson') {
			// set conversation participants
			$names = UserProfile::getUserProfilesByUsername(ArrayUtil::trim(explode(',', $shopItem->handsonNames)));
			$userList = new UserList();
			$userList->getConditionBuilder()->add('user_table.username IN (?)', [$names]);
			$userList->readObjects();
			$temp = $userList->getObjects();
			if (count($temp)) {
				foreach ($temp as $participant) {
					$participants[] = $participant->userID;
				}
			}
			
			$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.handson');
		}
		
		// text item product 
		if ($shopItem->typeDes == 'textItem') {
			$textItem = '';
			$count = 0;
			$lines = ArrayUtil::trim(explode("\n", $shopItem->textItem));
			
			foreach ($lines as $key=>$line) {
				preg_match('/^(\d+):(.+)/', $line, $matches);
				if (!$matches[1]) continue;
				
				$count = $matches[1];
				$textItem = $matches[2];
				break;
			}
			
			if (!empty($textItem)) {
				// adjust lines
				$count --;
				$lines[$key] = $count . ':' . $textItem;
				$editor = new JCoinsShopItemEditor($shopItem);
				$editor->update([
						'textItem' => implode("\n", $lines)
				]);
				
				// sell
				$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.textItem.text', [
						'text' => $textItem
				]);
				
				$detail = $textItem;
			}
			else {
				// no text left
				throw new NamedUserException($language->get('wcf.jcoins.shop.buy.error.textItem'));
			}
			
		}
		
		// trophy
		if ($shopItem->typeDes == 'trophy') {
			$objectAction = new UserTrophyAction([], 'create', [
					'data' => [
							'trophyID' => $this->trophy->trophyID,
							'userID' => WCF::getUser()->userID,
							'description' => '',
							'time' => TIME_NOW,
							'useCustomDescription' => 0,
							// not in 3.1, but default 0
							//'trophyUseHtml' => 0
					]
			]);
			$objectAction->executeAction();
		}
		
		$parameters = [
				'message' => $message,
				'shopItem' => $shopItem
		];
		EventHandler::getInstance()->fireAction($this, 'buy', $parameters);
		$message = $parameters['message'];
		
		// log transaction
		JCoinsShopTransactionEditor::create([
				'shopItem' => $shopItem,
				'detail' => $detail
		]);
		
		//  JCoins
		UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.shop.statement.product', null, [
				'amount' => -1 * $shopItem->getPrice(),
				'userID' => $user->userID,
				'title' => $shopItem->getSubject()
		]);
		
		// save item stats
		$editor = new JCoinsShopItemEditor($shopItem);
		$editor->updateCounters([
				'sold' => 1,
				'earnings' => $shopItem->getPrice()
		]);
		
		// update user to item
		$editor->updateItemToBuyer();
		
		// create activity event
		if (JCOINS_SHOP_ACTIVITY) {
			UserActivityEventHandler::getInstance()->fireEvent('com.uz.jcoins.shop.recentActivityEvent.purchase', $shopItem->shopItemID);
		}
		
		// create conversation
		$htmlInputProcessor = new HtmlInputProcessor();
		$htmlInputProcessor->process($message, 'com.woltlab.wcf.conversation.message');
		$conversationAction = new ConversationAction([], 'create', [
				'data' => $data,
				'messageData' => [],
				'participants' => $participants,
				'htmlInputProcessor' => $htmlInputProcessor
		]);
		$returnValues = $conversationAction->executeAction();
		
		if ($shopItem->leaveConversation) {
			// get conversation
			$conversation = $returnValues['returnValues'];
			$conversationEditor = new ConversationEditor($conversation);
			
			// change user for log
			$oldUser = WCF::getUser();
			WCF::getSession()->changeUser($sender, true);
			
			$sql = "UPDATE	wcf".WCF_N."_conversation_to_user
					SET	hideConversation = ?
					WHERE	conversationID = ? AND participantID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([Conversation::STATE_LEFT, $conversation->conversationID, $sender->userID]);
			
			UserStorageHandler::getInstance()->reset([$sender->userID], 'conversationCount');
			UserStorageHandler::getInstance()->reset([$sender->userID], 'unreadConversationCount');
			
			ConversationModificationLogHandler::getInstance()->leave($conversation);
			
			ConversationEditor::updateParticipantCounts([$conversation->conversationID]);
			ConversationEditor::updateParticipantSummaries([$conversation->conversationID]);
			// no need to delete conversation, since there must be a participant at this time
			
			// Reset to old user
			WCF::getSession()->changeUser($oldUser, true);
		}
	}
}
