<?php 
return array (
	'parent' => 'core.model',
	'table' => 'session',
	'fields' => array (
		'session_id' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'varchar',
			'length' => 255,
		),
		'data' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'text',
			'length' => 0,
		),
		'updatedon' => array (
			'default' => '',
			'phptype' => 'text',
			'dbtype' => 'datetime',
			'length' => 0,
		),
	),
	'master' => array (
	),
	'slave' => array (
	),
);