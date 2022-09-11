<?php
namespace wcf\data\jcoins\shop\item\content;
use wcf\data\DatabaseObject;
use wcf\data\language\Language;
use wcf\data\media\ViewableMedia;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\language\LanguageFactory;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a JCoins shop item content.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsShopItemContent extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'jcoins_shop_item_content';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'contentID';
	
	/**
	 * @inheritDoc
	 */
	public function getFormattedContent() {
		$processor = new HtmlOutputProcessor();
		if ($this->hasEmbeddedObjects) {
			MessageEmbeddedObjectManager::getInstance()->loadObjects('com.uz.jcoins.shop.content', [$this->contentID]);
		}
		$processor->process($this->content, 'com.uz.jcoins.shop.content', $this->contentID);
		
		return $processor->getHtml();
	}
	
	/**
	 * Returns the language of this item content or `null` if no language has been specified.
	 */
	public function getLanguage() {
		if ($this->languageID) {
			return LanguageFactory::getInstance()->getLanguage($this->languageID);
		}
		
		return null;
	}
	
	/**
	 * Returns the item's image if the active user can access it or `null`.
	 */
	public function getImage() {
		if ($this->image === null) {
			if ($this->imageID) {
				$this->image = ViewableMedia::getMedia($this->imageID);
			}
		}
		
		if ($this->image === null || !$this->image->isAccessible()) {
			return null;
		}
		
		return $this->image;
	}
	
	/**
	 * Sets the item's image.
	 */
	public function setImage(ViewableMedia $image) {
		$this->image = $image;
	}
	
	/**
	 * Returns a certain item content or `null` if it does not exist.
	 */
	public static function getItemContent($shopItemID, $languageID) {
		if ($languageID !== null) {
			$sql = "SELECT	*
					FROM	wcf".WCF_N."_jcoins_shop_item_content
					WHERE	shopItemID = ? AND languageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$shopItemID, $languageID]);
		}
		else {
			$sql = "SELECT	*
					FROM	wcf".WCF_N."_jcoins_shop_item_content
					WHERE	shopItemID = ? AND languageID IS NULL";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$shopItemID]);
		}
		
		if (($row = $statement->fetchSingleRow()) !== false) {
			return new JCoinsShopItemContent(null, $row);
		}
		
		return null;
	}
	
	/**
	 * Returns the item's subject.
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	/**
	 * Returns the item's unformatted teaser.
	 */
	public function getTeaser() {
		return $this->teaser;
	}
	
	/**
	 * Returns the item's formatted teaser.
	 */
	public function getFormattedTeaser() {
		if ($this->teaser) {
			return StringUtil::encodeHTML($this->teaser);
		}
		else {
			return StringUtil::truncateHTML(StringUtil::stripHTML($this->getFormattedContent()));
		}
	}
}
