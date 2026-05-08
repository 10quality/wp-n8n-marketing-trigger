<?php
require '/var/www/html/wp-load.php';
require '/var/www/html/wp-content/plugins/n8n-marketing-trigger/vendor/autoload.php';
$url = n8n_mt_settings()->test_url;
try {
  $client = new \GuzzleHttp\Client(['timeout'=>20,'http_errors'=>true]);
  $res = $client->request('POST', $url, ['json'=>['ping'=>true]]);
  echo 'status='.$res->getStatusCode().PHP_EOL;
} catch (\Throwable $e) {
  echo 'error='.$e->getMessage().PHP_EOL;
}
