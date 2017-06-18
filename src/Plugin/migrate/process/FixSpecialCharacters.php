<?php

namespace Drupal\migrate_d8\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "fix_special_chars"
 * )
 */
class FixSpecialCharacters extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    try {
      if (!empty($value) && !is_array($value)) {
        // LessonName coming through with â€ characters instead of a long dash.
        $value = utf8_decode($value);
      }
    }
    catch (\Exception $exception) {
      throw new MigrateException('Invalid source data');
    }

    return $value;
  }
}