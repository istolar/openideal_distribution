<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsByCategoryCharts' block.
 *
 * @Block(
 *  id = "openideal_statistics_by_category_charts_block",
 *  admin_label = @Translation("Charts by category block"),
 * )
 */
class OpenidealStatisticsByCategoryCharts extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Json serialization service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $serializer;

  /**
   * Date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManager $entityTypeManager,
    Json $json,
    DateFormatter $dateFormatter
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->serializer = $json;
    $this->dateFormatter = $dateFormatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('serialization.json'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $storage = $this->entityTypeManager->getStorage('node');
    // @Todo: optimize.
    $entity_query = $storage->getQuery();
    $ids = $entity_query->condition('type', 'idea')
      ->exists('field_category')
      ->execute();

    $ideas = $storage->loadMultiple($ids);

    $data = [];
    /** @var \Drupal\node\NodeInterface $idea */
    foreach ($ideas as $idea) {
      $category = $idea->field_category->value;
      $data[$category] = ($data[$category] ?? 0) + 1;
    }

    $data = $this->serializer->encode($data);
    $build['#attached']['drupalSettings']['charts']['byCategory']['data'] = $data;
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.charts';
    $build['#cache']['tags'] = ['node_list:idea'];

    $build[] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['charts-by-category']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('By category chart'),
      ],
    ];

    return $build;
  }

}
