<?php

namespace Drupal\migrate_d8\Plugin\migrate\process;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "paragraphs_import_tabs"
 * )
 */
class ParagraphsImportTabs extends ProcessPluginBase {
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

    $tabsMapping = [
      'cost' => 'Cost',
      'duration' => 'Duration',
      'is_valid' => 'IsValid',
      'pyid' => 'pyID',
      'delivery_type' => ''
    ];

  // Self Study Tab.
  if (isset($row->getSource()['self_study_tab_source'])) {
    $x = 1;
    $tabSourceArray = isset($row->getSource()['self_study_tab_source']) && !empty($row->getSource()['self_study_tab_source']) ? $row->getSource()['self_study_tab_source'] : NULL;
    $delivery_type = 'self';

    $paragraph_values = array();
    if (!is_null($tabSourceArray)) {
      foreach ($tabsMapping as $drupalField => $source) {
        if ($drupalField != 'delivery_type') {
          $paragraph_values[$drupalField]['value'] = isset($tabSourceArray[$source]) ? $tabSourceArray[$source] : '';
        }
        else {
          $paragraph_values[$drupalField]['value'] = $delivery_type;
        }
      }
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
      $paragraph{$x} = Paragraph::create($paragraph_values);
      $paragraph{$x}->save();
    }
  }

    // Instructor Led Tab.
    if (isset($row->getSource()['instructor_led_tab_source'])) {
      $x = 2;
      $tabSourceArray = isset($row->getSource()['instructor_led_tab_source']) && !empty($row->getSource()['instructor_led_tab_source']) ? $row->getSource()['instructor_led_tab_source'] : NULL;
      $delivery_type = 'instructor';

      $paragraph_values = array();
      if (!is_null($tabSourceArray)) {
        foreach ($tabsMapping as $drupalField => $source) {
          if ($drupalField != 'delivery_type') {
            $paragraph_values[$drupalField]['value'] = isset($tabSourceArray[$source]) ? $tabSourceArray[$source] : '';
          }
          else {
            $paragraph_values[$drupalField]['value'] = $delivery_type;
          }
        }
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
        $paragraph{$x} = Paragraph::create($paragraph_values);
        $paragraph{$x}->save();
      }
    }

    // Private Tab.
    if (isset($row->getSource()['private_tab_source'])) {
      $x = 3;
      $tabSourceArray = isset($row->getSource()['private_tab_source']) && !empty($row->getSource()['private_tab_source']) ? $row->getSource()['private_tab_source'] : NULL;
      $delivery_type = 'private';

      $paragraph_values = array();
      if (!is_null($tabSourceArray)) {
        foreach ($tabsMapping as $drupalField => $source) {
          if ($drupalField != 'delivery_type') {
            $paragraph_values[$drupalField]['value'] = isset($tabSourceArray[$source]) ? $tabSourceArray[$source] : '';
          }
          else {
            $paragraph_values[$drupalField]['value'] = $delivery_type;
          }
        }
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
        $paragraph{$x} = Paragraph::create($paragraph_values);
        $paragraph{$x}->save();
      }
    }

    // Online Mentoring Tab.
    if (isset($row->getSource()['online_mentoring_source'])) {
      $x = 4;
      $tabSourceArray = isset($row->getSource()['online_mentoring_source']) && !empty($row->getSource()['online_mentoring_source']) ? $row->getSource()['online_mentoring_source'] : NULL;
      $delivery_type = 'online';

      $paragraph_values = array();
      if (!is_null($tabSourceArray)) {
        foreach ($tabsMapping as $drupalField => $source) {
          if ($drupalField != 'delivery_type') {
            $paragraph_values[$drupalField]['value'] = isset($tabSourceArray[$source]) ? $tabSourceArray[$source] : '';
          }
          else {
            $paragraph_values[$drupalField]['value'] = $delivery_type;
          }
        }
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
        $paragraph{$x} = Paragraph::create($paragraph_values);
        $paragraph{$x}->save();
      }
    }

    return $paragraph;
  }
}