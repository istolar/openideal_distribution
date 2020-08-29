<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Component\Datetime\Time;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsByCategoryCharts' block.
 *
 * @Block(
 *  id = "openideal_statistics_per_day_charts_block",
 *  admin_label = @Translation("Charts per day"),
 * )
 */
class OpenidealStatisticsPerDayCharts extends BlockBase implements ContainerFactoryPluginInterface {

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
   * Time.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManager $entityTypeManager,
    Json $json,
    DateFormatter $dateFormatter,
    Time $time
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->serializer = $json;
    $this->dateFormatter = $dateFormatter;
    $this->time = $time;
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
      $container->get('date.formatter'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = $this->configuration['entity'];
    $data = $this->getData();

    $data = $this->serializer->encode($data);
    $build['#attached']['drupalSettings']['charts']['perDay']['data'] = $data;
    $build['#attached']['drupalSettings']['charts']['perDay']['bindTo'] = '#per-day-' . $entity;
    $build['#attached']['drupalSettings']['charts']['perDay']['label'] = 'No. Of ' . ucfirst($entity) . 's';
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.charts';
    $build['#cache']['tags'] = [$entity . '_list' . ($entity == 'node' ? ':idea' : '')];

    $build[] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['per-day-' . $entity],
        'id' => 'per-day-' . $entity,
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['entity'] = [
      '#type' => 'select',
      '#options' => [
        'user' => $this->t('Users'),
        'votes' => $this->t('Votes'),
        'comment' => $this->t('Comments'),
        'node' => $this->t('Ideas'),
      ],
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['entity'] = $form_state->getValue('entity');
  }

  /**
   * Get data for chart.
   *
   * @return array
   *   Data.
   */
  protected function getData() {
    $entity = $this->configuration['entity'];
    $created = $entity == 'vote' ? 'timestamp' : 'created';
    $storage = $this->entityTypeManager->getStorage($entity);
    // @Todo: optimize.
    $query = $storage->getQuery()
      ->condition($created, $this->time->getRequestTime() - 2592000, '>');
    if ($entity == 'node') {
      $query->condition('type', 'idea');
    }
    $ids = $query->execute();
    $data = $storage->loadMultiple($ids);
    $result = [];
    foreach ($data as $entity) {
      $date = $this->dateFormatter->format($entity->{$created}->value, 'html_date');
      $result[$date] = [
        'date' => $date,
        'total' => ($result[$date]['total'] ?? 0) + 1,
      ];
    }
    return array_values($result);
  }

}
