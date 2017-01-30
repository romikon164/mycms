<?php 
return array (
	'parent' => 'core.model',
	'table' => 'root_actions',
	'fields' => array (
		'root_id' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'action' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'access_level' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
	),
	'master' => array (
		'Root' => array (
			'model' => 'core.root',
			'internal' => 'root_id',
			'external' => 'id',
			'conformity' => 'one',
		),
	),
	'slave' => array (
	),
);