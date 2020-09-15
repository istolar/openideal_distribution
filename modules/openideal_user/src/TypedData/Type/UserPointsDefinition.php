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
        ->setDescription("The URL of the site's login page.")
        ->setRequired(TRUE);
      $this->propertyDefinitions['idea'] = DataDefinition::create('string')
        ->setLabel('Idea')
        ->setDescription('Idea value.')
        ->setRequired(TRUE);
    }
    return $this->propertyDefinitions;
  }

}
