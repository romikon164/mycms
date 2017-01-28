<?php 
return array (
	'parent' => 'core.model',
	'table' => 'users',
	'fields' => array (
		'username' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'password' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 32,
		),
		'token' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 32,
		),
		'createdon' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'datetime',
			'length' => 0,
		),
		'loggedon' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'datetime',
			'length' => 0,
		),
		'active' => array (
			'default' => '',
			'phptype' => 'boolean',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
		'blocked' => array (
			'default' => '',
			'phptype' => 'boolean',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
	),
	'master' => array (
	),
	'slave' => array (
		'Groups' => array (
			'model' => 'core.usergroupmember',
			'internal' => 'id',
			'external' => 'user_id',
			'conformity' => 'many',
		),
	),
);