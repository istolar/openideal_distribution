<?php

/**
 * @file
 * Place holder for the message example module.
 */

/**
 * Implements hook_token_info().
 *
 * We need this part until the token module will be exported to Drupal 8.
 */
function openideal_user_token_info() {
  $type = [
    'name' => t('Message additional'),
    'description' => t('Tokens for the openideal acitivity module.'),
    'needs-data' => 'message',
  ];

  $message['node-title'] = [
    'name' => t('Node title'),
    'description' => t("The related node title."),
  ];

  $message['node-url'] = [
    'name' => t('Node URL'),
    'description' => t("The related node URL."),
  ];

  $message['idea-life-cycle'] = [
    'name' => t('Idea life cycle'),
    'description' => t("The idea life cycle."),
  ];

  $message['vote-owner-name'] = [
    'name' => t('Vote owner name'),
  ];

  $message['vote-owner-url'] = [
    'name' => t('Vote owner url'),
  ];

  $message['vote-voted-entity-title'] = [
    'name' => t('Voted entity title'),
  ];

  $message['vote-voted-entity-url'] = [
    'name' => t('Voted entity url'),
  ];

  return [
    'types' => ['message' => $type],
    'tokens' => [
      'message' => $message,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function openideal_user_tokens($type, $tokens, array $data = [], array $options = []) {
  $replacements = [];

  if ($type == 'message' && !empty($data['message'])) {
    /** @var \Drupal\message\Entity\Message $message */
    $message = $data['message'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Simple key values on the comment.
        case 'node-title':
          if (strpos($message->bundle(), 'comment') !== FALSE) {
            $replacements[$original] = $message->field_comment_reference->entity->getCommentedEntity()->label();
          }
          else {
            $replacements[$original] = $message->field_node_reference->get(0)->entity->label();
          }
          break;

        case 'idea-life-cycle':
          if (strpos($message->bundle(), 'idea') !== FALSE) {
            $replacements[$original] = $message->field_node_reference->entity->moderation_state->value;
          }
          break;

        case 'vote-voted-entity-title':
          if (strpos($message->bundle(), 'like') !== FALSE) {
            $vote = $message->field_vote_reference->entity;
            $node = \Drupal::entityTypeManager()->getStorage($vote->getVotedEntityType())->load($vote->getVotedEntityId());
            $replacements[$original] = $node->getTitle();
          }
          break;

        case 'vote-voted-entity-url':
          if (strpos($message->bundle(), 'like') !== FALSE) {
            $vote = $message->field_vote_reference->entity;
            $node = \Drupal::entityTypeManager()->getStorage($vote->getVotedEntityType())->load($vote->getVotedEntityId());
            $replacements[$original] = $node->url();
          }
          break;

        case 'vote-owner-url':
          if (strpos($message->bundle(), 'like') !== FALSE) {
            $replacements[$original] = $message->field_vote_reference->entity->getOwner()->toUrl()->toString();
          }
          break;

        case 'vote-owner-name':
          if (strpos($message->bundle(), 'like') !== FALSE) {
            $replacements[$original] = $message->field_vote_reference->entity->getOwner()->getDisplayName();
          }
          break;

        case 'node-url':
          if (strpos($message->bundle(), 'comment') !== FALSE) {
            $replacements[$original] = $message->field_comment_reference->entity->getCommentedEntity()->url();
          }
          else {
            $replacements[$original] = $message->field_node_reference->get(0)->entity->url();
          }
          break;

        case 'comment-url':
          $replacements[$original] = $message->field_comment_reference->entity->id();
          break;
      }
    }
  }

  return $replacements;
}
