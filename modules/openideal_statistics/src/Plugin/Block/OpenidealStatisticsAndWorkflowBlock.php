<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsAndWorkflowBlock' block.
 *
 * @Block(
 *  id = "openideal_statistics_and_status",
 *  admin_label = @Translation("Statistics and status block"),
 * )
 */
class OpenidealStatisticsAndWorkflowBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Block manager.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $blockManager;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    BlockManager $block_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->blockManager = $block_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $configuration = $this->getConfiguration();
    $build = [];
    if (isset($configuration['node']) && $configuration['node'] instanceof NodeInterface) {
      $node = $configuration['node'];
      $statistics_block = $this->blockManager->createInstance('openideal_statistics_idea_statistics', ['node' => $node]);
      $status = $this->blockManager->createInstance('openideal_statistics_status', ['node' => $node]);
      $build = [
        '#type' => 'container',
        '#attributes' => ['class' => ['idea-statistics-and-status-block']],
        'statistics' => $statistics_block->build(),
        'status' => $status->build(),
      ];
    }

    return $build;
  }

}
