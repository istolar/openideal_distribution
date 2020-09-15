<?php

namespace Drupal\openideal_user\TypedData\Type;

use Drupal\Core\TypedData\ComplexDataDefinitionBase;
use Drupal\Core\TypedData\DataDefinition;

/**
 * A typed data definition class for describing user points information.
 */
class UserPointsDefinition extends ComplexDataDefinitionBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $this->propertyDefinitions['vote'] = DataDefinition::create('string')
        ->setLabel('Vote')
        ->setDescription("Vote weight")
        ->setRequired(TRUE);
      $this->propertyDefinitions['comment'] = DataDefinition::create('string')
        ->setLabel('Comment')
        ->setDescription("Comments weight")
        ->setRequired(TRUE);
      $this->propertyDefinitions['idea'] = DataDefinition::create('string')
        ->setLabel('Idea')
        ->setDescription('Idea weight')
        ->setRequired(TRUE);
    }
    return $this->propertyDefinitions;
  }

}
