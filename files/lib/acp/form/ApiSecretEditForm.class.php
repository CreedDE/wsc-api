<?php
namespace wcf\acp\form;
use wcf\data\user\avatar\Gravatar;
use wcf\data\user\avatar\UserAvatar;
use wcf\data\user\avatar\UserAvatarAction;
use wcf\data\user\cover\photo\UserCoverPhoto;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfileAction;
use wcf\form\AbstractForm;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\data\ApiSecret;
use wcf\data\ApiSecretEditor;

/**
 * Shows the user edit form.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2018 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Acp\Form
 */
class ApiSecretEditForm extends ApiSecretAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.wscApi.secrets.edit';
    
	/**
	 * object id
	 * @var	integer
	 */
	public $secretID = 0;
	
	/**
	 * user editor object
	 * @var	ApiSecretEditor
	 */
	public $apiSecret;

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = [];
		
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		if (isset($_REQUEST['id'])) $this->secretID = intval($_REQUEST['id']);
		$apiSecret = new ApiSecret($this->secretID);
		if (!$apiSecret->secretID) {
			throw new IllegalLinkException();
		}
		
		$this->apiSecret = new ApiSecretEditor($apiSecret);
		/*if (!UserGroup::isAccessibleGroup($this->user->getGroupIDs())) {
			throw new PermissionDeniedException();
		}*/
		
		parent::readParameters();
    }
    
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		/*
		if (!WCF::getSession()->getPermission('admin.user.canEditPassword')) $this->password = $this->confirmPassword = '';
		if (!WCF::getSession()->getPermission('admin.user.canEditMailAddress')) $this->email = $this->confirmEmail = $this->user->email;
		
		if (!empty($_POST['banned'])) $this->banned = 1;
		if (isset($_POST['banReason'])) $this->banReason = StringUtil::trim($_POST['banReason']);
		if ($this->banned && !isset($_POST['banNeverExpires'])) {
			if (isset($_POST['banExpires'])) $this->banExpires = @strtotime(StringUtil::trim($_POST['banExpires']));
		}
		
		if (isset($_POST['avatarType'])) $this->avatarType = $_POST['avatarType'];
		
		if (WCF::getSession()->getPermission('admin.user.canDisableAvatar')) {
			if (!empty($_POST['disableAvatar'])) $this->disableAvatar = 1;
			if (isset($_POST['disableAvatarReason'])) $this->disableAvatarReason = StringUtil::trim($_POST['disableAvatarReason']);
			if ($this->disableAvatar && !isset($_POST['disableAvatarNeverExpires'])) {
				if (isset($_POST['disableAvatarExpires'])) $this->disableAvatarExpires = @strtotime(StringUtil::trim($_POST['disableAvatarExpires']));
			}
		}
		
		if (WCF::getSession()->getPermission('admin.user.canDisableCoverPhoto')) {
			if (isset($_POST['deleteCoverPhoto'])) $this->deleteCoverPhoto = 1;
			if (!empty($_POST['disableCoverPhoto'])) $this->disableCoverPhoto = 1;
			if (isset($_POST['disableCoverPhotoReason'])) $this->disableCoverPhotoReason = StringUtil::trim($_POST['disableCoverPhotoReason']);
			if ($this->disableCoverPhoto && !isset($_POST['disableCoverPhotoNeverExpires'])) {
				if (isset($_POST['disableCoverPhotoExpires'])) $this->disableCoverPhotoExpires = @strtotime(StringUtil::trim($_POST['disableCoverPhotoExpires']));
			}
		}*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
	}
	
	/**
	 * Sets the default values.
	 */
	protected function readDefaultValues() {
        /*
		$this->username = $this->user->username;
		$this->email = $this->confirmEmail = $this->user->email;
		$this->groupIDs = $this->user->getGroupIDs(true);
		$this->languageID = $this->user->languageID;
		$this->banned = $this->user->banned;
		$this->banReason = $this->user->banReason;
		$this->banExpires = $this->user->banExpires;
		$this->userTitle = $this->user->userTitle;
		
		$this->signature = $this->user->signature;
		$this->disableSignature = $this->user->disableSignature;
		$this->disableSignatureReason = $this->user->disableSignatureReason;
		$this->disableSignatureExpires = $this->user->disableSignatureExpires;
		
		$this->disableAvatar = $this->user->disableAvatar;
		$this->disableAvatarReason = $this->user->disableAvatarReason;
		$this->disableAvatarExpires = $this->user->disableAvatarExpires;
		
		$this->disableCoverPhoto = $this->user->disableCoverPhoto;
		$this->disableCoverPhotoReason = $this->user->disableCoverPhotoReason;
		$this->disableCoverPhotoExpires = $this->user->disableCoverPhotoExpires;
		
		if ($this->user->avatarID) $this->avatarType = 'custom';
        else if (MODULE_GRAVATAR && $this->user->enableGravatar) $this->avatarType = 'gravatar';
        */
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
			'secretID' => $this->apiSecret->secretID,
			'action' => 'edit',
			'apiSecret' => $this->apiSecret
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();
		/*
		// handle avatar
		if ($this->avatarType != 'custom') {
			// delete custom avatar
			if ($this->user->avatarID) {
				$action = new UserAvatarAction([$this->user->avatarID], 'delete');
				$action->executeAction();
			}
		}
		
		$avatarData = [];
		switch ($this->avatarType) {
			case 'none':
				$avatarData = [
					'avatarID' => null,
					'enableGravatar' => 0
				];
			break;
			
			case 'custom':
				$avatarData = [
					'enableGravatar' => 0
				];
			break;
			
			case 'gravatar':
				$avatarData = [
					'avatarID' => null,
					'enableGravatar' => 1
				];
			break;
		}
		
		$this->additionalFields = array_merge($this->additionalFields, $avatarData);
		
		// add default groups
		$defaultGroups = UserGroup::getAccessibleGroups([UserGroup::GUESTS, UserGroup::EVERYONE, UserGroup::USERS]);
		$oldGroupIDs = $this->user->getGroupIDs();
		foreach ($oldGroupIDs as $oldGroupID) {
			if (isset($defaultGroups[$oldGroupID])) {
				$this->groupIDs[] = $oldGroupID;
			}
		}
		$this->groupIDs = array_unique($this->groupIDs);
		
		// save user
		$saveOptions = $this->optionHandler->save();

		$data = [
			'data' => array_merge($this->additionalFields, [
				'username' => $this->username,
				'email' => $this->email,
				'password' => $this->password,
				'languageID' => $this->languageID,
				'userTitle' => $this->userTitle,
				'signature' => $this->htmlInputProcessor->getHtml()
			]),
			'groups' => $this->groupIDs,
			'languageIDs' => $this->visibleLanguages,
			'options' => $saveOptions
		];

		// handle ban
		if (WCF::getSession()->getPermission('admin.user.canBanUser')) {
			$data['data']['banned'] = $this->banned;
			$data['data']['banReason'] = $this->banReason;
			$data['data']['banExpires'] = $this->banExpires;
		}
		
		// handle disabled signature
		if (WCF::getSession()->getPermission('admin.user.canDisableSignature')) {
			$data['data']['disableSignature'] = $this->disableSignature;
			$data['data']['disableSignatureReason'] = $this->disableSignatureReason;
			$data['data']['disableSignatureExpires'] = $this->disableSignatureExpires;
		}
		
		// handle disabled avatar
		if (WCF::getSession()->getPermission('admin.user.canDisableAvatar')) {
			$data['data']['disableAvatar'] = $this->disableAvatar;
			$data['data']['disableAvatarReason'] = $this->disableAvatarReason;
			$data['data']['disableAvatarExpires'] = $this->disableAvatarExpires;
		}
		
		// handle disabled cover photo
		if (WCF::getSession()->getPermission('admin.user.canDisableCoverPhoto')) {
			$data['data']['disableCoverPhoto'] = $this->disableCoverPhoto;
			$data['data']['disableCoverPhotoReason'] = $this->disableCoverPhotoReason;
			$data['data']['disableCoverPhotoExpires'] = $this->disableCoverPhotoExpires;
			
			if ($this->deleteCoverPhoto) {
				UserProfileRuntimeCache::getInstance()->getObject($this->userID)->getCoverPhoto()->delete();
				
				$data['data']['coverPhotoHash'] = null;
				$data['data']['coverPhotoExtension'] = '';
				
				UserProfileRuntimeCache::getInstance()->removeObject($this->userID);
			}
		}
		
		$this->objectAction = new UserAction([$this->userID], 'update', $data);
		$this->objectAction->executeAction();
		
		// update user rank
		$editor = new UserEditor(new User($this->userID));
		if (MODULE_USER_RANK) {
			$action = new UserProfileAction([$editor], 'updateUserRank');
			$action->executeAction();
		}
		if (MODULE_USERS_ONLINE) {
			$action = new UserProfileAction([$editor], 'updateUserOnlineMarking');
			$action->executeAction();
		}
		
		// remove assignments
		$sql = "DELETE FROM	wcf".WCF_N."_moderation_queue_to_user
			WHERE		userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->user->userID]);
		
		// reset moderation count
		ModerationQueueManager::getInstance()->resetModerationCount($this->user->userID);
		$this->saved();
		
		// reset password
		$this->password = $this->confirmPassword = '';
		
		// reload user when deleting the cover photo
		if ($this->deleteCoverPhoto) $this->user = new User($this->userID);
		
		// show success message
		WCF::getTPL()->assign('success', true);*/
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
	}
}
