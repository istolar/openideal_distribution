<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsIdeaStatisticsBlock' block.
 *
 * @Block(
 *  id = "openideal_statistics_idea_statistics",
 *  admin_label = @Translation("Idea statistics block"),
 * )
 */
class OpenidealStatisticsIdeaStatisticsBlock extends SiteWideStatisticsBlock implements ContainerFactoryPluginInterface {

  /**
   * Current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $configurations = $this->getConfiguration();
    $node = $this->routeMatch->getParameter('node');
    $public_stream = isset($configurations['view_mode']) && $configurations['view_mode'] == 'message';
    $id = NULL;

    if (isset($configurations['node'])) {
      $id = $configurations['node']->id();
    }
    elseif ($node instanceof NodeInterface && $node->bundle() == 'idea') {
      $id = $node->id();
    }
    else {
      return [];
    }

    $theme_path = base_path() . $this->themeManager->getActiveTheme()->getPath();
    $build['#theme'] = 'site_wide_statistics_block';
    $build['#main_class'] = 'idea-statistics-block';
    $build['#show_title'] = !$public_stream;
    $build['#content'] = [
      'votes' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getVotes', [$id]],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Votes'),
        'img' => $theme_path . '/misc/icons/' . ($public_stream ? 'public_stream_like' : 'like_tag') . '.svg',
      ],
      'comments' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getComments', [$id]],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Comments'),
        'img' => $theme_path . '/misc/icons/' . ($public_stream ? 'public_stream_comment' : 'comment_tag') . '.svg',
      ],
      'views' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getViews', [$id]],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Views'),
        'img' => $theme_path . '/misc/icons/' . ($public_stream ? 'public_stream_view' : 'view_tag') . '.svg',
      ],
    ];
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.block';
    return $build;
  }

}
