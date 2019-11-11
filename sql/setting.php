<?php

$databases['default']['default'] = array (
  'database' => 'sites/default/files/.ht.sqlite',
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\sqlite',
  'driver' => 'sqlite',
);

$databases['external']['default'] = array (
  'database' => 'sites/default/files/.arche_cache.sqlite',
  'prefix' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\sqlite',
  'driver' => 'sqlite',
);

$databases['repo']['default'] = array (
  'database' => 'www-data',
  'prefix' => '',
  'username' => 'drupal',
  'password' => '123qwe',
  'host' => 'postgresql',
  'port' => '5432',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
  'driver' => 'pgsql',
);
