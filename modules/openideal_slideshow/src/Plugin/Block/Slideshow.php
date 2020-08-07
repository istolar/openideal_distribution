<?php

namespace Drupal\openideal_slideshow\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Slideshow' block.
 *
 * @Block(
 *   id = "openidel_slideshow_block",
 *   admin_label = @Translation("Slideshow"),
 *   category = @Translation("Openideal")
 * )
 */
class Slideshow extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $configuration = $this->getConfiguration();
    $tags = isset($configuration['node']) ? $configuration['node']->getCacheTags() : [];
    return empty($configuration['images'])
      ? []
      : [
        '#theme' => 'openideal_slideshow',
        '#items' => $configuration['images'],
        '#attached' => [
          'library' => ['openideal_slideshow/openideal_slideshow.carousel'],
        ],
        '#cache' => [
          'tags' => $tags,
        ],
      ];
  }

}
