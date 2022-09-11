<?php
namespace wcf\page;
use wcf\data\jcoins\shop\item\JCoinsShopItem;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;
use wcf\util\FileReader;

/**
 * JCoins file download Page.
 * 
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.shop
 */
class JCoinsDownloadPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $useTemplate = false;
	
	/**
	 * item object
	*/
	public $shopItem;
	
	/**
	 * file reader object
	 */
	public $fileReader;
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $id = intval($_REQUEST['id']);
		$this->shopItem = new JCoinsShopItem($id);
		if (!$this->shopItem->shopItemID) throw new IllegalLinkException();
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkPermissions() {
		parent::checkPermissions();
		
		if (!$this->shopItem->canDownload()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		// init file reader
		$this->fileReader = new FileReader(WCF_DIR.'jCoinsFiles/'.$this->shopItem->filename, []);
		// add etag
		$this->fileReader->addHeader('ETag', '"'.$this->shopItem->shopItemID.'"');
	}

	/**
	 * @inheritDoc
	 */
	public function show() {
		parent::show();
		
		// send file to client
		$this->fileReader->send();
		
		exit;
	}
}
