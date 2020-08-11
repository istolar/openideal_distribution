<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Node tags' block.
 *
 * @Block(
 *  id = "openideal_idea_tags_block",
 *  admin_label = @Translation("Node tags"),
 * )
 */
class OpenidealIdeaTags extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $current_route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    $build = [];
    if ($node instanceof NodeInterface) {
      $build = [
        '#theme' => 'item_list',
        '#title' => $this->t('Tags'),
        '#attributes' => ['class' => ['idea-tags']],
      ];
      $items = [];
      foreach ($node->field_idea_tags as $tag) {
        $items[] = $tag->entity->label();
      }
      $build['#items'] = $items;
      $build['#cache']['tags'] = $node->getCacheTags();
    }

    return $build;
  }

}
