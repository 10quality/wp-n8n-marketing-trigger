<?php
require '/var/www/html/wp-load.php';
global $wpdb;
$post_id = 169;
$table = $wpdb->postmeta;
$rows = $wpdb->get_results($wpdb->prepare(
  "SELECT meta_id, meta_key, meta_value FROM {$table} WHERE post_id = %d ORDER BY meta_id ASC",
  $post_id
), ARRAY_A);
echo "RAW_ROWS\n";
foreach ($rows as $r) {
  echo $r['meta_id'].' | '.$r['meta_key'].' | '.substr((string)$r['meta_value'],0,180).PHP_EOL;
}
echo "\nWP_META_TYPES\n";
foreach ($rows as $r) {
  $v = get_post_meta($post_id, $r['meta_key'], true);
  echo $r['meta_key'].' => '.gettype($v);
  if (is_array($v)) echo ' size='.count($v);
  echo PHP_EOL;
}
$platforms = get_post_meta($post_id, 'platforms', true);
echo "\nPLATFORMS_DETAIL\n";
var_export($platforms);
echo PHP_EOL;
