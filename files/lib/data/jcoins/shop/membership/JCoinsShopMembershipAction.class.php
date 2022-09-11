<?php
namespace wcf\data\jcoins\shop\membership;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\conversation\Conversation;
use wcf\data\conversation\ConversationAction;
use wcf\data\conversation\ConversationEditor;
use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\group\UserGroup;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\log\modification\ConversationModificationLogHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Executes JCoins Shop Item actions.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopMembershipAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = JCoinsShopMembershipEditor::class;
	
	/**
	 * Revokes an existing membership.
	 */
	public function revoke() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach ($this->getObjects() as $membership) {
			$count = 0;
			
			// set inactive anyway
			$membership->update(['isActive' => 0]);
			
			// no removal from admin group
			$userGroup = new UserGroup($membership->groupID);
			if (!$userGroup->groupID) continue;
			if ($userGroup->isAdminGroup()) continue;
			
			// remove from group only if no other longer membership in this group
			$sql = "SELECT	COUNT(*)
					FROM	wcf".WCF_N."_jcoins_shop_membership
					WHERE	groupID = ? AND userID = ? AND endDate > ? AND isActive = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$membership->groupID, $membership->userID, $membership->endDate, 1]);
			$count = $statement->fetchColumn();
			
			if (!$count) {
				$action = new UserAction([$membership->userID], 'removeFromGroups', [
						'groups' => [$membership->groupID]
				]);
				$action->executeAction();
			}
		}
	}
	
	/**
	 * warn prior expiration of membership.
	 */
	public function warn() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		if (count($this->getObjects())) {
			// conversation data
			$data = [];
			$data['time'] = TIME_NOW;
			
			foreach ($this->getObjects() as $membership) {
				$membership->update(['isWarned' => 1]);
				
				$shopItem = new JCoinsShopItem($membership->shopItemID);
				if (!$shopItem->shopItemID) continue;
				
				$sender = User::getUserByUsername($shopItem->sender);
				if (!$sender->userID) continue;
				
				$receiver = new User($membership->userID);
				if (!$receiver->userID) continue;
				$language = $receiver->getLanguage();
				
				$group = UserGroup::getGroupByID($shopItem->membershipID);
				if (!$group->groupID) continue;
				
				$data['userID'] = $sender->userID;
				$data['username'] = $sender->username;
				$data['time'] = TIME_NOW;
				$participants[] = $receiver->userID;
				
				$data['subject'] = $language->getDynamicVariable('wcf.jcoins.shop.conversation.membership.warning.subject');
				
				$message = $language->getDynamicVariable('wcf.jcoins.shop.conversation.hallo', ['username' => $receiver->username]);
				$message .= '<br>';
				$message .= $language->getDynamicVariable('wcf.jcoins.shop.conversation.membership.warning.expires', [
						'shopItem' => $shopItem,
						'endDate' => $membership->endDate
				]);
				
				$htmlInputProcessor = new HtmlInputProcessor();
				$htmlInputProcessor->process($message, 'com.woltlab.wcf.conversation.message');
				$conversationAction = new ConversationAction([], 'create', [
						'data' => $data,
						'messageData' => [],
						'participants' => $participants,
						'htmlInputProcessor' => $htmlInputProcessor
				]);
				$returnValues = $conversationAction->executeAction();
				
				// leave conversation if configured
				if ($shopItem->leaveConversation) {
					$conversation = $returnValues['returnValues'];
					$conversationEditor = new ConversationEditor($conversation);
					
					// change user for log
					$oldUser = WCF::getUser();
					WCF::getSession()->changeUser($sender, true);
					
					$conversationEditor->removeParticipant($sender->userID);
					$conversationEditor->updateParticipantSummary();
					UserStorageHandler::getInstance()->reset([$sender->userID], 'unreadConversationCount');
					
					ConversationModificationLogHandler::getInstance()->add($conversation, 'removeParticipant', [
							'userID' => $sender->userID,
							'username' => $sender->username
					]);
					
					// Reset to old user
					WCF::getSession()->changeUser($oldUser, true);
				}
			}
		}
	}
}
