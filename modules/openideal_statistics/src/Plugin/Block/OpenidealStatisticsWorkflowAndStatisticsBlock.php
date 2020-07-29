<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Theme\ThemeManager;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsWorkflowAndStatisticsBlock' block.
 *
 * @Block(
 *  id = "openideal_statistics_workflow_and_statistics_block",
 *  admin_label = @Translation("Statistics and workflow block."),
 * )
 */
class OpenidealStatisticsWorkflowAndStatisticsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * Block manager.
   *
   * @var \Drupal\Core\Block\BlockManager
   */
  protected $blockManager;

  /**
   * Constructs a new OpenidealStatisticsWorkflowAndStatisticsBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity type manager.
   * @param \Drupal\Core\Theme\ThemeManager $theme_manager
   *   The theme manager.
   * @param \Drupal\Core\Block\BlockManager $block_manager
   *   Block plugin manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_manager,
    ThemeManager $theme_manager,
    BlockManager $block_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
    $this->themeManager = $theme_manager;
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
      $container->get('entity_type.manager'),
      $container->get('theme.manager'),
      $container->get('plugin.manager.block'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->getConfiguration()['node'];
    $build = [];
    if ($node instanceof NodeInterface) {
      $statistics_block = $this->blockManager->createInstance('openideal_statistics_idea_statistics', ['node_id', $node->id()]);
      $build = [
        '#type' => 'container',
        '#attributes' => ['class' => ['idea-statistics-and-status-block']],
        'statistics' => $statistics_block->build(),
        'status' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#attributes' => ['class' => ['idea-statistics-and-status-block--status']],
          '#value' => $node->moderation_state->value,
        ],
      ];
    }

    return $build;
  }

}
