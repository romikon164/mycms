<?php 
return array (
	'parent' => 'core.model',
	'table' => 'data_properties',
	'fields' => array (
		'property_id' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'data_id' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'value' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'text',
		),
		'value_number' => array (
			'default' => '0',
			'phptype' => 'real',
			'dbtype' => 'decimal',
			'length' => 14,
		),
	),
	'master' => array (
		'Property' => array (
			'model' => 'core.datatypeproperty',
			'internal' => 'property_id',
			'external' => 'id',
			'conformity' => 'one',
		),
		'Data' => array (
			'model' => 'core.data',
			'internal' => 'data_id',
			'external' => 'id',
			'conformity' => 'one',
		),
	),
	'slave' => array (
	),
);