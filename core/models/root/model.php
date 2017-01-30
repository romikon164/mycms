<?php 
class myModelRoot extends myModel {
	public function checkActionPermission ($action = null, $access_level = 0) {
		$result = true;
		if($access_level == null) {
			$access_level = myCMS::gI()->user->getPermission();
		}
		$query = myCMS::gI()->db->newQuery('core.rootaction');
		$query = $query->where(array (
			'root_id' => $this->get('id'),
			'action' => $action != null ? $action : $this->get('default_action'),
		))->execute();
		while($action = $query->fetch(PDO::FETCH_ASSOC)) {
			if($action['access_level'] * 10 + $this->get('access_level') * 100 > $access_level) {
				$result = false;
				break;
			}
		}

		return $result;
	}
}

return 'myModelRoot';