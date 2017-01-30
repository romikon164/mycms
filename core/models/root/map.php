<?php 
return array (
	'parent' => 'core.model',
	'table' => 'roots',
	'fields' => array (
		'name' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'path' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'controller' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'default_action' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'access_level' => array (
			'default' => '0',
			'phptype' => 'tinyiny',
			'dbtype' => 'integer',
			'length' => 1,
		),
	),
	'master' => array (
	),
	'slave' => array (
		'Actions' => array (
			'model' => 'core.rootaction',
			'internal' => 'id',
			'external' => 'root_id',
			'conformity' => 'many',
		),
	),
);