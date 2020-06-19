<?php

namespace Drupal\openideal_user\Plugin\Condition;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Component\Datetime\Time;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\votingapi\VoteInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'User is voted' condition.
 *
 * @Condition(
 *   id = "openideal_user_is_voted",
 *   label = @Translation("User voted today"),
 *   category = @Translation("Vote"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity:vote",
 *       label = @Translation("Vote entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
class UserVotedToday extends RulesConditionBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Time service.
   *
   * @var \Drupal\Component\Datetime\Time
   */
  protected $time;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entityTypeManager, Time $time) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('datetime.time')
    );
  }

  /**
   * Check if the provided entity is moderated.
   *
   * @param \Drupal\votingapi\VoteInterface $vote
   *   The entity to check.
   *
   * @return bool
   *   TRUE if the provided entity is moderated.
   */
  protected function doEvaluate(VoteInterface $vote) {
    $storage = $this->entityTypeManager->getStorage('vote');
    $user = $vote->getOwner();
    // Check if user voted.
    $count = $storage->getQuery()
      ->condition('user_id', $user->id(), '=')
      ->count()
      ->execute();
    // Check if user first time voted.
    if ($count > 1) {
      // Fetch last vote.
      $id = $storage->getQuery()
        ->condition('user_id', $user->id(), '=')
        ->range($count - 2, 1)
        ->execute();
      $last_vote = $this->entityTypeManager->getStorage('vote')->load(key($id));
      // Check if last voted was create more then one day after current vote.
      return ($vote->getCreatedTime() - $last_vote->getCreatedTime()) > 86400;
    }
    return TRUE;
  }

}
