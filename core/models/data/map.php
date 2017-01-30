<?php 
return array (
	'parent' => 'core.model',
	'table' => 'data',
	'fields' => array (
		'type' => array (
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
		'parent' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'int',
			'length' => 11,
		),
		'createdon' => array (
			'default' => '1970-01-01',
			'phptype' => 'text',
			'dbtype' => 'datetime',
			'length' => 0,
		),
		'editedon' => array (
			'default' => '1970-01-01',
			'phptype' => 'text',
			'dbtype' => 'datetime',
			'length' => 0,
		),
		'published' => array (
			'default' => '1',
			'phptype' => 'boolean',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
		'deleted' => array (
			'default' => '0',
			'phptype' => 'boolean',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
		'access_level' => array (
			'default' => '0',
			'phptype' => 'integer',
			'dbtype' => 'tinyint',
			'length' => 1,
		),
	),
	'master' => array (
		'Type' => array (
			'model' => 'core.datatype',
			'internal' => 'type',
			'external' => 'id',
			'conformity' => 'one',
		),
		'Parent' => array (
			'model' => 'core.data',
			'internal' => 'id',
			'external' => 'id',
			'conformity' => 'one',
		),
	),
	'slave' => array (
		'Properties' => array (
			'model' => 'core.datatypeproperty',
			'internal' => 'id',
			'external' => 'type_id',
			'conformity' => 'many',
		),
		'Values' => array (
			'model' => 'core.dataproperty',
			'internal' => 'id',
			'external' => 'data_id',
			'conformity' => 'many',
		),
	),
);