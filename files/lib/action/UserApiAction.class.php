<?php
namespace wcf\action;

use wcf\api\UserApi;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class UserApiAction extends AbstractAjaxAction {

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
	}

	/**
	 * @inheritDoc
	 */
	public function execute() {
		parent::execute();
		
        $this->sendJsonResponse((new UserApi())->execute());
	}
}
