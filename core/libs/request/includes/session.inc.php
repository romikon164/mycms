<?php 
session_set_save_handler('sess_open', 'sess_close', 'sess_read', 'sess_write', 'sess_destroy', 'sess_gb');

function sess_open($sess_path, $sess_name) {
	return true;
}

function sess_close() {
	return true;
}

function sess_read($sess_id) {
	if(!$session = myCMS::gI()->model->getOne('session', array ('session_id' => $sess_id))) {
		$session = myCMS::gI()->model->create('session');
		$session->session_id = $sess_id;
	}
	$session->updatedon = date('Y-m-d H:i:s');
	$session->save();

	return true;
}

function sess_write($sess_id, $data) {
	if(!$session = myCMS::gI()->model->getOne('session', array ('session_id' => $sess_id))) {
		$session = myCMS::gI()->model->create('session');
		$session->session_id = $sess_id;
	}
	$session->data = $data;
	$session->save();

	return true;
}

function sess_destroy($sess_id) {
	myCMS::gI()->model->remove('session', array ('session_id' => $sess_id));
	return true;
}

function sess_gb($sess_maxlifetime) {
	$date = date('Y-m-d H:i:s', time() - $sess_maxlifetime);
	myCMS::gI()->model->removeList('session', array ('updatedon:<' => $date));
	return true;
}