<?php


namespace Drupal\migrate_d8\Plugin\migrate\source;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Annotation\MigrateSource;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\source\SourcePluginExtension;
use Drupal\migrate_d8\Configuration\MigrateD8ServicesConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Source plugin for retrieving data via url provided in configuration.
 *
 * @MigrateSource(
 *   id="dynamic_courses_url"
 * )
 */
class DynamicCoursesUrl extends SourcePluginExtension implements ContainerFactoryPluginInterface {

  /**
   * The source URLs to retrieve.
   *
   * @var array
   */
  protected $sourceUrls = [];

  protected $migrateD8ServicesConfiguration;

  /**
   * The data parser plugin.
   *
   * @var \Drupal\migrate_plus\DataParserPluginInterface
   */
  protected $dataParserPlugin;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, MigrateD8ServicesConfigurationInterface $migrateD8ServicesConfiguration) {
    $this->migrateD8ServicesConfiguration = $migrateD8ServicesConfiguration;

    if (!is_array($configuration['urls'])) {
      $configuration['urls'] = [$this->migrateD8ServicesConfiguration->GetMigrateD8CoursesRestEndpointUrl()];
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $migrateD8ServicesConfiguration);

    $this->sourceUrls = $configuration['urls'];
  }

  /**
   * Return a string representing the source URL from configuration.
   *
   * @return string
   */
  public function __toString() {
    $urls = implode(', ', $this->sourceUrls);
    return $urls;
  }

  /**
   * Returns the initialized data parser plugin.
   *
   * @return \Drupal\migrate_plus\DataParserPluginInterface
   *   The data parser plugin.
   */
  public function getDataParserPlugin() {
    if (!isset($this->dataParserPlugin)) {
      $this->dataParserPlugin = \Drupal::service('plugin.manager.migrate_plus.data_parser')->createInstance($this->configuration['data_parser_plugin'], $this->configuration);
    }
    return $this->dataParserPlugin;
  }

  /**
   * Creates and returns a filtered Iterator over the documents.
   *
   * @return \Iterator
   *   An iterator over the documents providing source rows that match the
   *   configured item_selector.
   */
  protected function initializeIterator() {
    return $this->getDataParserPlugin();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('rest_service_d8.service.configuration')
    );
  }
}