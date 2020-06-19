<?php

namespace Drupal\openideal_user\Plugin\Condition;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Context\ContextDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derives condition plugin plugin definitions based on workflow types.
 *
 * @see \Drupal\openideal_user\Plugin\Condition\IdeaIsInState
 */
class IdeaFieldFilledDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  private $entityFieldManager;

  /**
   * Creates a new TransactionCreateDeriver object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   Entity field manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    /** @var \Drupal\workflows\Entity\Workflow $workflow */
    $idea_fields = $this->entityFieldManager->getFieldDefinitions('node', 'idea');
    foreach ($idea_fields as $field_name => $fieldDefinition) {
      // Add the derivative.
      if ($fieldDefinition instanceof FieldConfig) {
        $this->derivatives[$field_name] = [
          'label' => $this->t('Idea @field field was filled', ['@field' => $fieldDefinition->getLabel()]),
          'category' => $this->t('Idea'),
          'provides' => [],
          'context_definitions' => [
            'idea' => ContextDefinition::create('entity:node')
              ->setLabel($this->t('Idea entity.'))
              ->setAssignmentRestriction(ContextDefinitionInterface::ASSIGNMENT_RESTRICTION_SELECTOR)
              ->setRequired(TRUE),
          ],
        ] + $base_plugin_definition;
      }
    }

    return $this->derivatives;
  }

}
