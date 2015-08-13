<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

/*
 * Test if HTTP Loopback Connections are enabled on this server
 */
function fw_ext_backup_loopback_test()
{
    $gmt_time      = microtime(true);
    $doing_wp_cron = sprintf('%.22F', $gmt_time);
    $cronURL       = add_query_arg('doing_wp_cron', $doing_wp_cron, site_url('wp-cron.php'));

    $cron_request = array(
        'url'  => $cronURL,
        'key'  => $doing_wp_cron,
        'args' => array(
            'timeout'   => 4, // X second delay. A loopback should be very fast.
            'blocking'  => true,
            'sslverify' => apply_filters('https_local_ssl_verify', false),
        ),
    );

    $response = wp_remote_post($cron_request['url'], $cron_request['args']);

    if (is_wp_error($response)) {
        $error = $response->get_error_message();
        $error = 'If you need to contact your web host, tell them that when PHP tries to connect back to the site at the URL `'.$cronURL.'` via curl (or other fallback connection method built into WordPress) that it gets the error `'.$error.'`. This means that WordPress\' built-in simulated cron system cannot function properly, breaking some WordPress features & subsequently some plugins. There may be a problem with the server configuration (eg local DNS problems, mod_security, etc) preventing connections from working properly.';

        return array(
            'success' => false,
            'data'    => array(
                'message' => $error,
            ),
        );
    }

    return array(
        'success' => true,
    );
}