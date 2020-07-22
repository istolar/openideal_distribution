<?php

namespace Drupal\openideal_statistics\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Theme\ThemeManager;
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
   * Theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

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
   * @param \Drupal\Core\Theme\ThemeManager $theme_manager
   *   The theme manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_manager,
    ThemeManager $theme_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
    $this->themeManager = $theme_manager;
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
      $container->get('theme.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $theme_path = base_path() . $this->themeManager->getActiveTheme()->getPath();
    $build['#theme'] = 'site_wide_statistics_block';
    $build['#content'] = [
      'ideas' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getIdeas', []],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Ideas'),
        'img' => $theme_path . '/misc/icons/ideas_statistics.svg',
      ],
      'members' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getMembers', []],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Members'),
        'img' => $theme_path . '/misc/icons/members_teg.svg',
      ],
      'comments' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getComments', []],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Comments'),
        'img' => $theme_path . '/misc/icons/comment_teg.svg',
      ],
      'votes' => [
        'bottom' => [
          '#lazy_builder' => ['openideal_statistics.lazy_builder:getVotes', []],
          '#create_placeholder' => TRUE,
        ],
        'title' => $this->t('Votes'),
        'img' => $theme_path . '/misc/icons/like_tag.svg',
      ],
    ];
    $build['#attached']['library'][] = 'openideal_statistics/openideal_statistics.block';
    return $build;
  }

}
