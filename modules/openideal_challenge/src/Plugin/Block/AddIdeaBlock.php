<?php

namespace Drupal\openideal_challenge\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a button "Add your idea".
 *
 * @Block(
 *   id = "openidel_challenge_add_idea",
 *   admin_label = @Translation("Add idea button"),
 *   category = @Translation("Openideal")
 * )
 */
class AddIdeaBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Constructs a new Slideshow.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   Current route match service.
   */
  public function __construct(array $configuration,
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
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    // Create the button that automatically populates
    // the challenge reference field in the idea.
    $node = $this->currentRouteMatch->getParameter('node');
    $route_parameters = ['node_type' => 'idea'];

    // If displayed in layout builder node isn't presented.
    if ($node instanceof NodeInterface && $node->bundle() == 'challenge') {
      $route_parameters['challenge'] = $node->id();
    }

    return [
      '#type' => 'link',
      '#title' => $this->t('Add your idea'),
      '#url' => Url::fromRoute('node.add', $route_parameters),
      '#attributes' => ['class' => ['add_your_idea', 'btn', 'btn-primary']],
    ];
  }

}