<?php 
return array (
	'parent' => 'core.model',
	'table' => 'data_type_properties',
	'fields' => array (
		'data_type' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'code' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'type' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'enum' => array (
			'default' => '0',
			'phptype' => 'boolean',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
		'default' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'text',
		),
		'data_number' => array (
			'default' => '0',
			'phptype' => 'real',
			'dbtype' => 'decimal',
			'length' => 14,
		),
	),
	'master' => array (
		'Type' => array (
			'model' => 'core.datatype',
			'internal' => 'type',
			'external' => 'id',
			'conformity' => 'one',
		),
		'Values' => array (
			'model' => 'core.dataproperty',
			'internal' => 'id',
			'external' => 'property_id',
			'conformity' => 'many',
		),
	),
	'slave' => array (
	),
);