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
 *   id = "paragraphs_import_lesson_groups"
 * )
 */
class ParagraphsImport extends ProcessPluginBase {
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

    $lessonGroups = isset($row->getSource()['group_name']) && !empty($row->getSource()['group_name']) ? $row->getSource()['group_name'] : NULL;
    $paragraph_values = array();
     if (!is_null($lessonGroups)) {
       $x = 0;
       foreach  ($lessonGroups as $lessonGroup) {
         // Get Nids for Lessons.
         $lessons = [];
         $lessonDestids = [];
         foreach ($lessonGroup['Lessons'] as $lesson) {
           $lessons[] = $lesson['pyID'];
         }
         if (!empty($lessons)) {
           $lessonDestidQuery = \Drupal::database()
             ->select('migrate_map_lessons', 'mml');
           $lessonDestidQuery->fields('mml', ['destid1' , 'sourceid1']);
           $lessonDestidQuery->condition('mml.sourceid1', $lessons, 'IN');
           $lessonDestids = $lessonDestidQuery->execute()->fetchAll();
         }
          // Convert to array of nids.
          $lessonNids = [];
          foreach ($lessonDestids as $lessonDestid) {
            $lessonNids[$lessonDestid->sourceid1] = $lessonDestid->destid1;
          }

         // Order the lessons the same as the REST feed.
         $lessonNidsOrdered = [];
         foreach ($lessons as $lesson) {
           $lessonNidsOrdered[$lesson] = $lessonNids[$lesson];
         }
         // End to getting the Nids for Lessons.

         foreach ($this->configuration['fields'] as $field) {
           foreach ($field as $field_name => $source_value) {
             // Only populate field with values.
             if (!empty($source_value) && !is_null($source_value)) {
               switch ($field_name) {
                 case 'lesson':
                   $courseLessons = Node::loadMultiple($lessonNidsOrdered);
                   // Get an array of target ids (nids) and revision ids from the array of Lesson nodes to get added to the Paragraph.
                   // Entity reference field requires target_id and target_reference_id. @see paragraph_demo.install for example.
                   $courseLessonIds = [];
                   foreach ($courseLessons as $lessonEntity) {
                     $courseLessonIds[] = [
                       'target_id' => $lessonEntity->id(),
                       'target_revision_id' => $lessonEntity->getRevisionId(),
                     ];
                   }
                   // Set the lesson paragraph field to the array of target ids/revision ids.
                   // @see ParagraphsCompositeRelationshipTest for example.
                   $paragraph_values[$field_name] = $courseLessonIds;
                   // Get Group Name as part of lessons to get it saved.
                   $paragraph_values['group_name']['value'] = $lessonGroup['GroupName'];
                   break;
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
           }
         }
         $x++;
       }
       return $paragraph;
     }
  }
}
