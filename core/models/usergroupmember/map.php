<?php 
return array (
	'parent' => 'core.model',
	'table' => 'user_groups',
	'fields' => array (
		'user_id' => array (
			'default' => '',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'group_id' => array (
			'default' => '',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
	),
	'master' => array (
		'User' => array (
			'model' => 'core.user',
			'internal' => 'user_id',
			'external' => 'id',
		),
		'Group' => array (
			'model' => 'core.usergroup',
			'internal' => 'group_id',
			'external' => 'id',
		),
	),
	'slave' => array (
	),
);