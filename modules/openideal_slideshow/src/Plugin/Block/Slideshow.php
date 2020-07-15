<?php

namespace Drupal\openideal_slideshow\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\slick\SlickFormatter;
use Drupal\slick\SlickManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Slideshow' block.
 *
 * @Block(
 *   id = "openidel_slideshow_block",
 *   admin_label = @Translation("Slideshow"),
 *   category = @Translation("Openideal")
 * )
 */
class Slideshow extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Slick manager service.
   *
   * @var \Drupal\slick\SlickManager
   */
  protected $slickManager;

  /**
   * Slick formatter service.
   *
   * @var \Drupal\slick\SlickFormatter
   */
  protected $slickFormatter;

  /**
   * Constructs a new Slideshow.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\slick\SlickManager $slick_manager
   *   Slick manager.
   * @param \Drupal\slick\SlickFormatter $slick_formatter
   *   Slick formatter manager.
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    SlickManager $slick_manager,
    SlickFormatter $slick_formatter
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->slickManager = $slick_manager;
    $this->slickFormatter = $slick_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('slick.manager'),
      $container->get('slick.formatter')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    $configuration = $this->getConfiguration();
    return empty($configuration['images'])
      ? []
      : [
        '#theme' => 'openideal_slideshow',
        '#items' => $configuration['images'],
        '#attached' => [
          'library' => ['openideal_slideshow/openideal_slideshow.slick'],
        ],
      ];
  }

}
