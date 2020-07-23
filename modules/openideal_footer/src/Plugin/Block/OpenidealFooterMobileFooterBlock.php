<?php

namespace Drupal\openideal_footer\Plugin\Block;

use Drupal\openideal_idea\Plugin\Block\OpenidealIdeaFlagAndLikeBlock;

/**
 * Provides a 'MobileFooterBlock' block.
 *
 * @Block(
 *  id = "openideal_footer_mobile_footer_block",
 *  admin_label = @Translation("Mobile footer block"),
 * )
 */
class OpenidealFooterMobileFooterBlock extends OpenidealIdeaFlagAndLikeBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    $build['#theme'] = 'openideal_footer_mobile_footer_block';

    return $build;
  }

}
