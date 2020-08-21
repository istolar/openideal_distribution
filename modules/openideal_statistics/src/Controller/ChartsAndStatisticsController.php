<?php

namespace Drupal\openideal_statistics\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManagerInterface;

/**
 * Class ChartsAndStatisticsController.
 */
class ChartsAndStatisticsController extends ControllerBase {

  /**
   * Drupal\Core\Block\BlockManagerInterface definition.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $pluginManagerBlock;

  /**
   * Constructs a new ChartsAndStatisticsController object.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $plugin_manager_block
   *   Plugin manager block.
   */
  public function __construct(BlockManagerInterface $plugin_manager_block) {
    $this->pluginManagerBlock = $plugin_manager_block;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block')
    );
  }

  /**
   * Charts.
   *
   * @return string
   *   Return Hello string.
   */
  public function charts() {
    $charts = $this->pluginManagerBlock->createInstance('openideal_statistics_charts_block');
    return $charts->build();
  }

}
