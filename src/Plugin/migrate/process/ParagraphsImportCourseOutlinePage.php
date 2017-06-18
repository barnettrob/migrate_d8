<?php


namespace Drupal\migrate_d8\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "paragraphs_import_courseoutlinepage"
 * )
 */
class ParagraphsImportCourseOutlinePage extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['paragraph_type'])) {
      throw new MigrateException('Specify a paragraph type');
    }

    if (!isset($this->configuration['fields'])) {
      throw new MigrateException('Give fields in same order as source values');
    }

    $paragraph_values = array();

    $courseOutlines = $value;

    if (!empty($courseOutlines['pyLabel'])) {
      $paragraph_values['group_name_outline_page']['value'] = $courseOutlines['pyLabel'];
    }

    if (!empty($courseOutlines)) {
      foreach ($courseOutlines as $outline) {
        if (is_array($outline)) {
          $topics = [];
          foreach ($outline as $topic) {
            $topics[] = [
              'value' => $topic['pyLabel'],
            ];
          }
          $paragraph_values['topic_name'] = $topics;
        }

        // Don't create empty paragraphs.
        if (count($paragraph_values)) {
          $paragraph_values = array_merge(
            array(
              'id' => NULL,
              'type' => $this->configuration['paragraph_type'],
            ),
            $paragraph_values

          );
          $paragraph = Paragraph::create($paragraph_values);
          $paragraph->save();
        }
        return $paragraph;
      }
    }
  }
}