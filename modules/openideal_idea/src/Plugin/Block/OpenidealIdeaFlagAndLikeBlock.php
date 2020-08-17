<?php

namespace Drupal\openideal_idea\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\flag\FlagLinkBuilderInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'FlagAndLike' block.
 *
 * @Block(
 *  id = "openideal_idea_flag_and_like_block",
 *  admin_label = @Translation("Flag and Like block"),
 * )
 */
class OpenidealIdeaFlagAndLikeBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * Flag link builder service.
   *
   * @var \Drupal\flag\FlagLinkBuilderInterface
   */
  protected $flagLinkBuilder;

  /**
   * Current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new MobileFooterBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\flag\FlagLinkBuilderInterface $flag_link_builder
   *   Flag link builder service.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   Current route match.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    FlagLinkBuilderInterface $flag_link_builder,
    CurrentRouteMatch $current_route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->flagLinkBuilder = $flag_link_builder;
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
      $container->get('flag.link_builder'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->currentRouteMatch->getParameter('node');
    $build['#theme'] = 'openideal_idea_flag_and_like_block';
    if ($node instanceof NodeInterface) {
      $flag_link = $this->flagLinkBuilder->build($node->getEntityTypeId(), $node->id(), 'follow');
      $build['#follow'] = $flag_link;

      $settings = [
        'settings' => [
          'show_summary' => FALSE,
          'show_icon' => TRUE,
          'show_label' => TRUE,
          'show_count' => FALSE,
        ],
      ];
      $like = $node->field_like[0]->view($settings);
      $build['#like'] = $like;
      $build['#cache']['tags'] = $node->getCacheTags();
    }

    return $build;
  }

}
