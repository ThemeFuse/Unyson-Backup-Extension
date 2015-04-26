<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$manifest = array();

$manifest['name']        = __( 'Backup', 'fw' );
$manifest['description'] = __( 'This extension lets you set up daily, weekly or monthly backup schedule. You can choose between a full backup or a data base only backup.', 'fw' );
$manifest['version'] = '1.0.3';
$manifest['display'] = true;
$manifest['standalone'] = true;

$manifest['github_update'] = 'ThemeFuse/Unyson-Backup-Extension';
