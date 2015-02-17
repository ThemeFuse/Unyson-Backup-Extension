<?php if (!defined('FW')) die('Forbidden');

/**
 * @var FW_Extension_Backup $backup
 */

$backup = fw()->extensions->get('backup');
$theme_name = fw()->theme->manifest->get_name();
$demo_page_link = $backup->get_config('demo_page_link');

?>
<div class="wrap">

	<div id="icon-tools" class="icon32"></div>

	<h2><?php strtr(__('{theme_name} WordPress Theme', 'fw'), array('{theme_name}' => esc_html($theme_name))) ?></h2>

	<h3><?php _e('Make you theme look exactly like our demo', 'fw') ?></h3>

	<p><?php echo sprintf('By importing the demo content, your theme will look like the one
		you see on %s our demo %s.
		This install is not necessary but will help you get the core pages,
		categories and meta setup correctly.
		This action will also let you understand how the theme works by
		allowing you to modify a content that is already there rather than
		creating it from scratch.', '<a href="' . esc_attr($demo_page_link). '">', '</a>' ) ?></p>

	<div class="error">
		<p>
			<strong><?php _e('Important', 'fw') ?>:</strong> <?php printf(__('The demo content %s will replace %s all of your content (i.e. all of your content %s will be deleted %s).', 'fw'), '<strong>', '</strong>', '<strong>', '</strong>') ?>
		</p>
	</div>

	<p>
		<a href="<?php echo esc_attr($backup->action()->url_backup_auto_install()) ?>" onclick="return window.confirm('<?php _e('All your current content will be deleted and replaced with the demo content of the theme!', 'fw'); ?>');" class="button button-primary"><?php _e('Import Demo Content', 'fw') ?></a>
	</p>

</div>
