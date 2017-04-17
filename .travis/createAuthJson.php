<?php

/**
 * Shamelessly stolen from here https://github.com/algolia/algoliasearch-magento-2
 */

$json['http-basic']['repo.magento.com'] = array(
    'username' => getenv('MAGENTO_AUTH_USERNAME'),
    'password' => getenv('MAGENTO_AUTH_PASSWORD'),
);
$dirname = dirname(getenv('AUTH_DIR'));
if (!is_dir($dirname)) {
    mkdir($dirname, 0777, true);
}
file_put_contents(getenv('AUTH_DIR'), json_encode($json));
