<?php 
class myPageControllerAction_view extends myControllerAction {
	public function process () {
		$data = array (
			'message' => 'Hello World!'
		);
		return $this->success(200, $data);
	}
}

return 'myPageControllerAction_view';