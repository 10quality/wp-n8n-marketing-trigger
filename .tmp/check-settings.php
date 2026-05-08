<?php
require '/var/www/html/wp-load.php';
$settings = n8n_mt_settings();
echo 'test_url='.(string)$settings->test_url.PHP_EOL;
echo 'production_url='.(string)$settings->production_url.PHP_EOL;
