<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'SiteWideStatisticsBlock' block.
 *
 * @Block(
 *  id = "site_wide_statistics_block",
 *  admin_label = @Translation("Site wide statistics block"),
 * )
 */
class SiteWideStatisticsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new SiteWideStatisticsBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['#theme'] = 'site_wide_statistics_block';
    $build['#content'] = [
      'ideas' => [
        '#lazy_builder' => ['openideal_statistics.lazy_builder:getIdeas', []],
        '#create_placeholder' => TRUE,
      ],
      'members' => [
        '#lazy_builder' => ['openideal_statistics.lazy_builder:getMember', []],
        '#create_placeholder' => TRUE,
      ],
      'comments' => [
        '#lazy_builder' => ['openideal_statistics.lazy_builder:getComments', []],
        '#create_placeholder' => TRUE,
      ],
      'votes' => [
        '#lazy_builder' => ['openideal_statistics.lazy_builder:getVotes', []],
        '#create_placeholder' => TRUE,
      ],
    ];
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.block';
    return $build;
  }

}
