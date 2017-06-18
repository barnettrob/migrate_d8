<?php

namespace Drupal\migrate_d8\Event;

use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePlusEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrateD8MigrateEvent implements EventSubscriberInterface {
  /**
   * {@inheritdoc}
   */
  static public function getSubscribedEvents() {
    $events[MigrateEvents::PREPARE_ROW][] = array('onPrepareRow', 0);
    return $events;
  }

  public function onPrepareRow(MigratePrepareRowEvent $event) {
    // Get all of the source rows from json feed.
    $row = $event->getRow();

    // Comma-separated values that will be exploded into an array
    // and inserted into multi-value Drupal fields.
    $source_array_values = [
      'role',
    ];

    foreach ($source_array_values as $source_value) {
      // Get rid of comma at beginning of value and set the newly trimmed value for insert.
      $row->setSourceProperty($source_value, ltrim($row->getSourceProperty($source_value), ','));
    }
  }
}