<?php

namespace Drupal\openideal_statistics;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Utility\Token;

/**
 * LazyBuilder object.
 */
class OpenidealStatisticsLazyBuilder {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * LazyBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\Core\Utility\Token $token
   *   Token service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Token $token) {
    $this->entityTypeManager = $entity_type_manager;
    $this->token = $token;
  }

  /**
   * Build element that return idea count.
   */
  public function getIdeas() {
    return [
      '#markup' => $this->token->replace('[openideal:ideas-count]'),
      '#cache' => [
        'tags' => ['node_list:idea'],
      ],
    ];
  }

  /**
   * Build element that return members count.
   */
  public function getMembers() {
    return [
      '#markup' => $this->token->replace('[openideal:members-count]'),
      '#cache' => [
        'tags' => ['user_list'],
      ],
    ];
  }

  /**
   * Build element that return comments count.
   */
  public function getComments() {
    return [
      '#markup' => $this->token->replace('[openideal:comments-count]'),
      '#cache' => [
        'tags' => ['comment_list'],
      ],
    ];
  }

  /**
   * Build element that return votes count.
   */
  public function getVotes() {
    return [
      '#markup' => $this->token->replace('[openideal:votes-count]'),
      '#cache' => [
        'tags' => ['vote_list'],
      ],
    ];
  }

}
