<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

// Configure Redis


$variables = array (
  'domains' =>
  array (
    'canonical' => '',
    'synonyms' =>
    array (
      0 => 'dev-build-tool-demo.pantheonsite.io'
    ),
  ),
  'redis' => true,
);

  if (array_key_exists('redis', $variables) && $variables['redis']) {
    // Set possible redis module paths.
    $redis_paths = array(
      implode(DIRECTORY_SEPARATOR, array('sites', 'default', 'modules', 'contrib', 'redis')),
      implode(DIRECTORY_SEPARATOR, array('sites', 'default', 'modules', 'redis')),
      implode(DIRECTORY_SEPARATOR, array('modules', 'contrib', 'redis')),
      implode(DIRECTORY_SEPARATOR, array('modules', 'redis')),
    );

    if (array_key_exists('CACHE_HOST', $_ENV) && !empty($_ENV['CACHE_HOST'])) {
      foreach ($redis_paths as $path) {
        if (is_dir($path)) {
          if (in_array('example.services.yml', scandir($path))) {
            $settings['container_yamls'][] = $path . DIRECTORY_SEPARATOR . 'example.services.yml';

            $settings['redis.connection']['interface'] = 'PhpRedis';
            $settings['redis.connection']['host'] = $_ENV['CACHE_HOST'];
            $settings['redis.connection']['port'] = $_ENV['CACHE_PORT'];
            $settings['redis.connection']['password'] = $_ENV['CACHE_PASSWORD'];

            $settings['cache']['default'] = 'cache.backend.redis';
            $settings['cache_prefix']['default'] = 'pantheon-redis';

            $settings['cache']['bins']['bootstrap'] = 'cache.backend.chainedfast';
            $settings['cache']['bins']['discovery'] = 'cache.backend.chainedfast';
            $settings['cache']['bins']['config'] = 'cache.backend.chainedfast';

            break;
          }
        }
      }
    }
  }