<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'OpenidealStatisticsIdeaStatisticsBlock' block.
 *
 * @Block(
 *  id = "openideal_statistics_idea_statistics",
 *  admin_label = @Translation("Idea statistics block"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
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
    $contexts = $this->getContexts();
    $public_stream = isset($contexts['view_mode']) && $contexts['view_mode']->getContextValue() == 'message';
    $id = NULL;

    if (isset($contexts['node']) && !$contexts['node']->getContextValue()->isNew()) {
      $node = $contexts['node']->getContextValue();
      $id = $node->id();
    }
    else {
      return [];
    }

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
        'img_class' => $public_stream ? 'public_stream_like' : 'like_tag',
      ],
      'comments' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getComments', [$id]],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Comments'),
        'img_class' => $public_stream ? 'public_stream_comment' : 'comment_tag',
      ],
      'views' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getViews', [$id]],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Views'),
        'img_class' => $public_stream ? 'public_stream_view' : 'view_tag',
      ],
      'overall_score' => [
        'bottom' => [
          $node->field_overall_score->first()->view(['settings' => ['scale' => 0]]),
        ],
        'title' => $this->t('Overall score'),
        // @Todo: ask for appropriate icon.
        'img_class' => '',
      ],
    ];
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.block';
    return $build;
  }

}
