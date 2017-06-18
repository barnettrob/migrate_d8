<?php

namespace Drupal\migrate_d8\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\node\Entity\Node;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "check_node_on_update"
 * )
 */
class CheckNodeOnUpdate extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    // Checks for migrate map data.
    $idMap = $row->getIdMap();
    $nid = isset($idMap['destid1']) ? $idMap['destid1'] : NULL;

    // Load Node object if destid1 exists (node already exists and is getting updated).
    if (!is_null($nid)) {
      $node = !is_null($nid) ? Node::load($nid) : NULL;

      // Remove Role values before they get updated with entity_generate/entity_lookup plugin.
      // Values added by the feed but then removed in the feed were not getting removed by migration.
      $node->role->setValue(null);

      // Lesson Groups Paragraphs.
      $lesson_groups = isset($node->lesson_groups) ? $node->lesson_groups : NULL;
      if (!is_null($lesson_groups)) {
        $lGroups = array();
        foreach ($lesson_groups as $lesson_group) {
          if (!is_null($lesson_group->entity)) {
            $lGroups[] = $lesson_group->entity->id();
          }
        }
        if (!empty($lGroups)) {
          // Delete Lesson Groups paragraphs entities before update.
          $lessonGroupEntitiesController = \Drupal::entityTypeManager()
            ->getStorage('paragraph');
          $lessonGroupsEntities = $lessonGroupEntitiesController->loadMultiple($lGroups);
          $lessonGroupEntitiesController->delete($lessonGroupsEntities);
        }
      }
      // Course Outline Page Paragraphs.
      $course_outline_page = isset($node->course_outline_page) ? $node->course_outline_page : NULL;
      if (!is_null($course_outline_page)) {
        $courseOutlinePage = array();
        foreach ($course_outline_page as $course_outline) {
          if (!is_null($course_outline->entity)) {
            $courseOutlinePage[] = $course_outline->entity->id();
          }
        }
        if (!empty($courseOutlinePage)) {
          // Delete Course Outline Page paragraphs entities before update.
          $courseOutlineEntitiesController = \Drupal::entityTypeManager()
            ->getStorage('paragraph');
          $courseOutlineEntities = $courseOutlineEntitiesController->loadMultiple($courseOutlinePage);
          $courseOutlineEntitiesController->delete($courseOutlineEntities);
        }
      }
      // Course Delivery Container Paragraph.
      $course_delivery_container = isset($node->delivery_container) ? $node->delivery_container : NULL;
      if (!is_null($course_delivery_container)) {
        $courseDeliveryTab = array();
        foreach ($course_delivery_container as $course_delivery) {
          if (!is_null($course_delivery->entity)) {
            $courseDeliveryTab[] = $course_delivery->entity->id();
          }
        }
        if (!empty($courseDeliveryTab)) {
          // Delete Self Study paragraphs entities before update.
          $courseDeliveryEntitiesController = \Drupal::entityTypeManager()
            ->getStorage('paragraph');
          $courseDeliveryEntities = $courseDeliveryEntitiesController->loadMultiple($courseDeliveryTab);
          $courseDeliveryEntitiesController->delete($courseDeliveryEntities);
        }
      }
    }

    return $value;
  }
}