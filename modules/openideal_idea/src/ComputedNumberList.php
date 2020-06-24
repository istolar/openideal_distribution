<?php

namespace Drupal\openideal_idea;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use InvalidArgumentException;

/**
 * A computed field that provides a overall score for Idea node bundle.
 */
class ComputedNumberList extends FieldItemList implements CacheableDependencyInterface {

  use ComputedItemListTrait {
    get as traitGet;
  }

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    $value = $this->getOverallScore();
    // Do not store NULL values, in the case where an entity does not have a
    // score associated with it, we do not create list items for
    // the computed field.
    $this->list[0] = $this->createItem(0, $value);
  }

  /**
   * Get overall score.
   */
  protected function getOverallScore() {
    $configuration = Drupal::configFactory()->get('openideal_idea.scoreconfig');
    // Node id.
    $id = $this->getEntity()->id();

    // Get node comments.
    $comments = Drupal::entityQuery('comment')
      ->condition('entity_id', $id)
      ->condition('entity_type', 'node')
      ->count()
      ->execute();

    // Get node votes.
    $votes = Drupal::entityQuery('vote')
      ->condition('entity_id', $id)
      ->condition('entity_type', 'node')
      ->count()
      ->execute();

    // Get node votes.
    $votes = Drupal::entityQuery('vote')
      ->condition('entity_id', $id)
      ->condition('entity_type', 'node')
      ->count()
      ->execute();

    $query = Drupal::database()
      ->select('votingapi_vote', 'vv')
      ->fields('vv', ['id'])
      ->condition('vv.entity_type', 'comment');

    $query->join('comment_field_data', 'cd', 'cd.entity_id = :id and cd.cid = vv.entity_id', [':id' => $id]);
    $comment_votes = $query
      ->countQuery()
      ->execute()
      ->fetchField();

    // Compute the score.
    $node_counter_value = 0;

    // If statistics module is enabled then add node view count to score.
    if (Drupal::moduleHandler()->moduleExists('statistics')) {
      /** @var \Drupal\statistics\StatisticsViewsResult $statistics_result */
      $statistics_result = Drupal::service('statistics.storage.node')->fetchView($id);
      $node_counter_value = $statistics_result->getTotalCount() * ($configuration->get('node_value') ?? 0.2);
    }

    return $comments * ($configuration->get('comments_value') ?? 10)
      + $votes * ($configuration->get('votes_value') ?? 5)
      + $comment_votes * ($configuration->get('comments_score_value') ?? 2)
      + $node_counter_value;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE) {
    parent::setValue($values, $notify);
    $this->valueComputed = TRUE;

    // If the parent created a field item and if the parent should be notified
    // about the change (e.g. this is not initialized with the current value),
    // update the overall score.
    if (isset($this->list[0]) && $notify) {
      $this->list[0]->value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function get($index) {
    if ($index !== 0) {
      throw new InvalidArgumentException('An entity can not have multiple scores at the same time.');
    }
    return $this->traitGet($index);
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts() {
    Cache::mergeContexts($this->getEntity()->getCacheContexts());
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    // Todo: finish logic.
    $node = $this->getEntity();
    Cache::mergeTags($node->getCacheTags(), ['openideal:node:overall_score' . $node->id()]);
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheMaxAge() {
    $this->getEntity()->getCacheMaxAge();
  }

}
