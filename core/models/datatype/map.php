<?php 
return array (
	'parent' => 'core.model',
	'table' => 'data_type',
	'fields' => array (
		'code' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'parent' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'access_level' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
	),
	'master' => array (
		'Parent' => array (
			'model' => 'core.data',
			'internal' => 'id',
			'external' => 'id',
			'conformity' => 'one',
		),
	),
	'slave' => array (
		'Data' => array (
			'model' => 'core.data',
			'internal' => 'id',
			'external' => 'type',
			'conformity' => 'many',
		),
		'Properties' => array (
			'model' => 'core.datatypeproperty',
			'internal' => 'id',
			'external' => 'data_type',
			'conformity' => 'many',
		),
	),
);