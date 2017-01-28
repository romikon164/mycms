<?php 
return array (
	'parent' => 'core.model',
	'table' => 'settings',
	'fields' => array (
		'namespace' => array (
			'default' => 'core',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'type' => array (
			'default' => 'text',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'name' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'value' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'text',
			'length' => 0,
		),
	),
	'master' => array (
	),
	'slave' => array (
	),
);