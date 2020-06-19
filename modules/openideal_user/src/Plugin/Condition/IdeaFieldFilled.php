<?php

namespace Drupal\openideal_user\Plugin\Condition;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides a generic 'Idea in state' condition.
 *
 * @Condition(
 *   id = "openideal_idea_field_filled",
 *   deriver = "Drupal\openideal_user\Plugin\Condition\IdeaFieldFilledDeriver"
 * )
 */
class IdeaFieldFilled extends RulesConditionBase {

  /**
   * Check if the field X was filled in idea.
   *
   * @param \Drupal\Core\Entity\EntityInterface $idea
   *   The idea entity.
   *
   * @return bool
   *   TRUE if the field was filled.
   */
  protected function doEvaluate(EntityInterface $idea) {
    return !$idea->get($this->getDerivativeId())->isEmpty();
  }

}
