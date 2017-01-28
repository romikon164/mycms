<?php 
return array (
	'parent' => 'core.model',
	'table' => 'group_access',
	'fields' => array (
		'group_id' => array (
			'default' => '',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'root' => array (
			'default' => '',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'controller' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'method' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
	),
	'master' => array (
		'Group' => array (
			'model' => 'core.usergroup',
			'internal' => 'group_id',
			'external' => 'id',
		),
		'Root' => array (
			'model' => 'core.root',
			'internal' => 'root',
			'external' => 'id',
		),
	),
	'slave' => array (
	),
);