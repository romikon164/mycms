<?php 
return array (
	'parent' => 'core.model',
	'table' => 'groups',
	'fields' => array (
		'name' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'access_level' => array (
			'default' => '000',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 3,
		),
	),
	'master' => array (
	),
	'slave' => array (
		'Users' => array (
			'model' => 'core.usergroupmember',
			'internal' => 'id',
			'external' => 'group_id',
			'conformity' => 'many',
		),
	),
);