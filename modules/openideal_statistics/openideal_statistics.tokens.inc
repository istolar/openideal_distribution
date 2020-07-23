<?php

/**
 * @file
 * OpenideaL Challenge module tokens support.
 */

use Drupal\Core\Render\BubbleableMetadata;

/**
 * Implements hook_token_info().
 */
function openideal_statistics_token_info() {
  $types = [
    'name' => t('OpenideaL'),
    'description' => t('OpenideaL token types.'),
  ];
  $tokens = [
    'ideas-count' => [
      'name' => t('OpenideaL - the count of all ideas'),
      'description' => t('The count of all ideas in the site'),
      'type' => 'openideal',
    ],
    'comments-count' => [
      'name' => t('OpenideaL - the count of all comments'),
      'description' => t('The count of all comments in the site'),
      'type' => 'openideal',
    ],
    'members-count' => [
      'name' => t('OpenideaL - the count of al members'),
      'description' => t('The count of all member is the site'),
      'type' => 'openideal',
    ],
    'votes-count' => [
      'name' => t('OpenideaL - the count of all votes'),
      'description' => t('The count of all votes in the site'),
      'type' => 'openideal',
    ],
  ];

  return [
    'types' => [
      'openideal' => $types,
    ],
    'tokens' => [
      'openideal' => $tokens,
    ],
  ];
}

/**
 * Implements hook_tokens().
 */
function openideal_statistics_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  // Add tokens that return total count of: users, comments, votes.
  if ($type == 'openideal') {
    $entity_type_manager = \Drupal::entityTypeManager();
    foreach ($tokens as $name => $original) {

      switch ($name) {
        case 'members-count':
          $query = $entity_type_manager->getStorage('user')->getQuery();
          $result = $query
            ->condition('status', '1')
            ->count()
            ->execute();
          $replacements[$original] = $result;
          $bubbleable_metadata->addCacheTags(['user_list']);
          break;

        case 'comments-count':
          $query = $entity_type_manager->getStorage('comment')->getQuery();
          $result = $query
            ->condition('status', '1')
            ->count()
            ->execute();
          $replacements[$original] = $result;
          $bubbleable_metadata->addCacheTags(['comment_list']);
          break;

        case 'ideas-count':
          $query = $entity_type_manager->getStorage('node')->getQuery();
          $result = $query
            ->condition('status', '1')
            ->count()
            ->execute();
          $replacements[$original] = $result;
          $bubbleable_metadata->addCacheTags(['node_list:idea']);
          break;

        case 'votes-count':
          $query = $entity_type_manager->getStorage('vote')->getQuery();
          $result = $query
            ->count()
            ->execute();
          $replacements[$original] = $result;
          $bubbleable_metadata->addCacheTags(['vote_list']);
          break;
      }
    }
  }

  return $replacements;
}
