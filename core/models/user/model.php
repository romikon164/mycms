<?php 
class myModelUser extends myModel {
	public $sessionFieldsPrefix = 'mycms_myModelUser_';

	public function authenticate() {
		$result = false;
		if(
			isset($_SESSION[$this->sessionFieldsPrefix.'username']) && 
			isset($_SESSION[$this->sessionFieldsPrefix.'password']) &&
			isset($_SESSION[$this->sessionFieldsPrefix.'token'])
			) {
			
			$data = $this->_load(array (
				'username' => $_SESSION[$this->sessionFieldsPrefix.'username'],
				'password' => $_SESSION[$this->sessionFieldsPrefix.'password'],
				'token' => $_SESSION[$this->sessionFieldsPrefix.'token'],
			), 0, 1);

			if(count($data)) {
				$data = $data[0];
				$token = $this->generateToken($data['username'], $data['password'], $data['loggedon']);
				if($token == $_SESSION[$this->sessionFieldsPrefix.'token']) {
					$this->fromArray($data);
					$result = true;
				}
			}
		}
		return $result;
	}

	public function isMember($group) {
		if($this->get('id') == 0) {
			return $group == 1 || $group == '(anonymous)';
		} else {
			if($group == 1 || $group == '(anonymous)') {
				return false;
			} else {
				$query = myCMS::gI()->db->newQuery('core.user');
				$query->join('core.usergroupmember', null, 'usergroupmember.user_id=user.id');
				$query->join('core.usergroup', null, 'usergroup.id=usergroupmember.group_id');
				$query->where(array (
					'user.id' => $this->get('id'),
					array(
						'usergroup.id' => $group,
						'OR:usergroup.name' => $group,
					),
				));
				$query->select(array ('COUNT(*)'));
				$query = $query->execute();
				return $query->fetch(PDO::FETCH_COLUMN) > 0;
			}
		}
	}

	public function checkPolicy($type = null, $value = null) {
		if($value === null) {
			if($type === null) {

			} else {
				
			}
		} else {

		}
	}

	public function generatePassword($password, $createdon = null) {
		if($createdon === null) {
			$createdon = $this->get('createdon');
		}
		return md5($password.$createdon);
	}

	public function generateToken($username = null, $password = null, $loggedon = null) {
		if($username === null) {
			$username = $this->get('username');
		}
		if($password === null) {
			$password = $this->get('password');
		}
		if($loggedon === null) {
			$loggedon = $this->get('loggedon');
		}
		return md5($username.$password.$loggedon);
	}

	public function beforeSave() {

	}

	public function rememberToSession() {
		$_SESSION[$this->sessionFieldsPrefix.'username'] = $this->get('username');
		$_SESSION[$this->sessionFieldsPrefix.'password'] = $this->get('password');
		$_SESSION[$this->sessionFieldsPrefix.'token'] = $this->get('token');
	}

	public function removeFromSession() {
		unset($_SESSION[$this->sessionFieldsPrefix.'username']);
		unset($_SESSION[$this->sessionFieldsPrefix.'password']);
		unset($_SESSION[$this->sessionFieldsPrefix.'token']);
	}
}

return 'myModelUser';