<?php

namespace Drupal\openideal_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Theme\ThemeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a "Powered by" Block.
 *
 * @Block(
 *   id = "openidel_footer_powered_by",
 *   admin_label = @Translation("Powered by"),
 *   category = @Translation("Openideal")
 * )
 */
class OpenidealFooterPoweredByBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Theme manger.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              $plugin_definition,
                              ThemeManager $themeManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->themeManager = $themeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('theme.manager'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    $path = $this->themeManager->getActiveTheme()->getPath();
    $img = '<img src="' . base_path() . $path . '/misc/icons/logo_openideal.png">';
    return ['#markup' => '<div class="site-footer--powered-by">' . $this->t('Powered by <span class="site-footer--powered-by__drupal">OpenideaL</span>') . "${img}</div>"];
  }

}
