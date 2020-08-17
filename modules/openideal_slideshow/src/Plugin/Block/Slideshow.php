<?php

namespace Drupal\openideal_slideshow\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Slideshow' block.
 *
 * @Block(
 *   id = "openidel_slideshow_block",
 *   admin_label = @Translation("Slideshow"),
 *   category = @Translation("Openideal"),
 *   context = {
 *      "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE,
 *     )
 *   }
 * )
 */
class Slideshow extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $contexts = $this->getContexts();
    $images = $this->getImages();
    $tags = isset($contexts['node']) ? $contexts['node']->getContextValue()->getCacheTags() : [];
    return empty($images)
      ? []
      : [
        '#theme' => 'openideal_slideshow',
        '#items' => $images,
        '#attached' => [
          'library' => ['openideal_slideshow/openideal_slideshow.carousel'],
        ],
        '#cache' => [
          'tags' => $tags,
        ],
      ];
  }

  /**
   * Get the images from node.
   */
  protected function getImages() {
    $contexts = $this->getContexts();
    $result = [];

    if (!isset($contexts['node'])) {
      return $result;
    }

    $node = $contexts['node']->getContextValue();
    if ($node->bundle() == 'challenge' && !$node->field_main_image->isEmpty()) {
      $result[] = $node->field_main_image->first()->entity;
    }

    /** @var \Drupal\file\Plugin\Field\FieldType\FileFieldItemList  $images */
    $images = $node->field_images;
    foreach ($images as $image) {
      $result[] = $image->entity;
    }

    return $result;
  }

}
