<?php

namespace Drupal\openideal_user\Plugin\RulesAction;

use Drupal\Core\Entity\EntityInterface;
use Drupal\message\Entity\Message;
use Drupal\rules\Core\RulesActionBase;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides a Flag action.
 *
 * @RulesAction(
 *   id = "openideal_message_action",
 *   label = @Translation("Create a message with a reference field"),
 *   category = @Translation("Follow"),
 *   context_definitions = {
 *     "template" = @ContextDefinition("esting",
 *       label = @Translation("The message template"),
 *       assignment_restriction = "input",
 *       required = TRUE
 *     ),
 *     "referenced_entity" = @ContextDefinition("entity",
 *       label = @Translation("Message's referenced entity field."),
 *       assignment_restriction = "selector",
 *       required = TRUE
 *     ),
 *   }
 * )
 */
class MessageAction extends RulesActionBase {

  /**
   * Flag the Entity.
   *
   * @param string $template
   *   Template ID.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The referenced entity.
   */
  protected function doExecute(string $template, EntityInterface $entity) {
    // Todo: finish implementation.
    $isValidTemplate = Message::queryByTemplate($template);
    if ($entity instanceof EntityOwnerInterface && !empty($isValidTemplate)) {
      Message::create(['']);
    }
  }

}
