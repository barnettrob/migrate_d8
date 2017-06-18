<?php

namespace Drupal\migrate_d8\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "iterate_array_pyvalue"
 * )
 */
class IterateArrayPyvalue extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    try {
      if (!empty($value) && is_array($value)) {
        return isset($value['pyValue']) ? $value['pyValue'] : '';
      }
    }
    catch (\Exception $exception) {
      throw new MigrateException('Invalid source data');
    }
  }
}