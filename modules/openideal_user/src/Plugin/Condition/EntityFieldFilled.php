<?php

namespace Drupal\openideal_user\Plugin\Condition;

use Drupal\Core\Entity\EntityInterface;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides a generic 'Entity field X was filled' condition.
 *
 * @Condition(
 *   id = "openideal_idea_field_filled",
 *   deriver = "Drupal\openideal_user\Plugin\Condition\EntityFieldFilledDeriver"
 * )
 */
class EntityFieldFilled extends RulesConditionBase {

  /**
   * Check if the field X was filled in entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The idea entity.
   *
   * @return bool
   *   TRUE if the field was filled.
   */
  protected function doEvaluate(EntityInterface $entity) {
    // Field is saved as "$field_name . $bundle",
    // so need to get the field from id.
    $id = $this->getDerivativeId();
    $pos = strpos($id, $entity->bundle());
    $field = substr($id, 0, mb_strlen($id) - $pos);
    return !$entity->get($field)->isEmpty();
  }

}
