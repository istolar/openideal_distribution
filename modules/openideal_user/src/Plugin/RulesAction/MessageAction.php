<?php

namespace Drupal\openideal_user\Plugin\RulesAction;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\message\Entity\Message;
use Drupal\node\NodeInterface;
use Drupal\rules\Core\RulesActionBase;
use Drupal\votingapi\VoteInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Flag action.
 *
 * @RulesAction(
 *   id = "openideal_message_action",
 *   label = @Translation("Create a message with a reference field"),
 *   category = @Translation("Message"),
 *   context_definitions = {
 *     "template" = @ContextDefinition("string",
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
class MessageAction extends RulesActionBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * VotedEntity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $votedEntity;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManager $entityTypeManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * Create a message with related field.
   *
   * @param string $template
   *   Template ID.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The referenced entity.
   */
  protected function doExecute($template, EntityInterface $entity) {
    if ($this->isValid($entity, $template)) {
      $message = Message::create(['template' => $template, 'uid' => $entity->getOwnerId()]);
      $entity_type = $entity->getEntityTypeId();

      if ($entity instanceof VoteInterface) {
        $entity_type = $this->votedEntity->getEntityTypeId();
        $entity = $this->votedEntity;
      }

      $message->set('field_' . $entity_type . '_reference', $entity);
      $message->save();
    }
  }

  /**
   * Check if entity is valid.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to check.
   * @param string $template
   *   Template.
   *
   * @return bool
   *   Check if node is published.
   */
  private function isValid(EntityInterface $entity, $template) {
    // @Todo: If if if ?:)
    if ($entity instanceof VoteInterface) {
      if ($this->isUserVoted($entity, $template)) {
        return FALSE;
      }
      $this->votedEntity = $this->entityTypeManager->getStorage($entity->getVotedEntityType())->load($entity->getVotedEntityId());
      if ($this->votedEntity instanceof NodeInterface) {
        return $this->votedEntity->isPublished();
      }
      elseif ($this->votedEntity instanceof CommentInterface) {
        return $this->votedEntity->getCommentedEntity()->isPublished();
      }
    }
    elseif ($entity instanceof CommentInterface) {
      return $entity->getCommentedEntity()->isPublished();
    }
    return TRUE;
  }

  /**
   * Check if user voted today.
   *
   * @param \Drupal\votingapi\VoteInterface $entity
   *   Entity to check.
   * @param string $template
   *   Template.
   *
   * @return bool
   *   TRUE if user already voted, false otherwise.
   */
  private function isUserVoted(VoteInterface $entity, string $template) {
    $query = $this->entityTypeManager->getStorage('message')->getQuery();
    $result = $query
      ->condition('template', $template)
      ->condition('field_' . $entity->getVotedEntityType() . '_reference', $entity->getVotedEntityId())
      ->condition('uid', $entity->getOwnerId())
      ->execute();
    return !empty($result);
  }

}
