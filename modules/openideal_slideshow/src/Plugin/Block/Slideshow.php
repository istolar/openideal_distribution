<?php

namespace Drupal\openideal_slideshow\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
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
    $build = [];
    $settings = [];

    /** @var \Drupal\file\Plugin\Field\FieldType\FileFieldItemList $image_field */
    if (isset($configuration['images'])) {
      $images = $configuration['images'];
      $entities = $this->getEntitiesToView($images);
      $this->slickFormatter->buildSettings($settings, $images);
      // Build the settings.
      $build = ['settings' => $settings];

      $entities = empty($entities) ? [] : array_values($entities);
      $elements = $entities ?: $images;
      $this->buildElements($build, $elements);
    }

    return $this->slickManager->build($build);
  }

  /**
   * Returns the referenced entities for display.
   *
   * @param \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items
   *   The item list.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   The array of referenced entities to display, keyed by delta.
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items) {
    $entities = [];

    foreach ($items as $delta => $item) {
      $entity = $item->entity;
      $access = $entity->access('view', NULL, TRUE);

      if ($access->isAllowed()) {
        $entities[$delta] = $entity;
      }
    }

    return $entities;
  }

  /**
   * Build the slick carousel elements.
   *
   * @param array $build
   *   Build array.
   * @param mixed $files
   *   Files to render.
   */
  public function buildElements(array &$build, $files) {
    $settings = &$build['settings'];

    foreach ($files as $delta => $file) {
      $settings['delta'] = $delta;
      $settings['type'] = 'image';

      $item = $file->_referringItem;

      $settings['file_tags'] = $file->getCacheTags();
      $settings['uri']       = $file->getFileUri();

      $element = ['item' => $item, 'settings' => $settings];

      $settings = $element['settings'];

      // Image with responsive image, lazyLoad, and lightbox supports.
      $element['slide'] = $this->slickFormatter->getBlazy($element);

      // Build individual slick item.
      $build['items'][$delta] = $element;

      unset($element);
    }
  }

}
