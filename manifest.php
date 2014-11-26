<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Backup', 'fw' );
$manifest['description'] = __( 'This extension lets you set up daily, weekly or monthly backup schedule. You will have the option to choose between a full backup or a data base only backup.', 'fw' );
$manifest['version'] = '1.0.0';
$manifest['display'] = true;
$manifest['standalone'] = true;