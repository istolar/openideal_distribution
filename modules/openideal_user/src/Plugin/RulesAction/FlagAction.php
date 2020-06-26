<?php

namespace Drupal\openideal_user\Plugin\RulesAction;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\flag\FlagServiceInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Save entity' action.
 *
 * @RulesAction(
 *   id = "openideal_user_flag_action",
 *   label = @Translation("Follow/unfollow the entity"),
 *   category = @Translation("Follow"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity to follow"),
 *       assignment_restriction = "selector"
 *     ),
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User that will follow the entity"),
 *       assignment_restriction = "selector"
 *     ),
 *     "operation" = @ContextDefinition("string",
 *       label = @Translation("The flag operation."),
 *       description = @Translation("The operation can be one of next: flag or unflag"),
 *       assignment_restriction = "input",
 *       required = TRUE
 *     ),
 *   }
 * )
 */
class FlagAction extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The flag service.
   *
   * @var \Drupal\flag\FlagServiceInterface
   */
  protected $flagService;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    FlagServiceInterface $flag_service
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->flagService = $flag_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('flag')
    );
  }

  /**
   * Flag the Entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to be flagged.
   * @param \Drupal\user\UserInterface $user
   *   User to flag.
   * @param string $operation
   *   Operation (flag or unflag)
   */
  protected function doExecute(EntityInterface $entity, UserInterface $user, string $operation) {
    if ($this->validateOperation($operation)) {
      $flag = $this->flagService->getFlagById('follow');
      $this->flagService->{$operation}($flag, $entity, $user);
    }
  }

  /**
   * Validate operation.
   *
   * @param string $operation
   *   Operation.
   *
   * @return bool
   *   TRUE if validate, FALSE otherwise.
   */
  private function validateOperation(string $operation) {
    return $operation == 'flag' || $operation == 'unflag';
  }

}
