<?php

namespace Drupal\openideal_comment;

use Drupal\comment\CommentViewBuilder;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * View builder handler for comments.
 */
class OpenidealCommentViewBuilder extends CommentViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    parent::buildComponents($build, $entities, $displays, $view_mode);
    $previous_thread = NULL;
    $previous_key = NULL;

    // Point last child of thread.
    foreach ($entities as $key => $entity) {
      $thread = $entity->getThread();
      if (strpos($thread, '.') === FALSE
        && isset($previous_thread)
        && (isset($previous_key) && $entities[$previous_key]->hasParentComment())) {
        $build[$previous_key]['#attributes']['class'][] = 'comments--thread__last_child';
      }
      $previous_thread = $thread;
      $previous_key = $key;
    }

    // Point the last item as section end and last child.
    $build[$key]['#comment_section_end'] = TRUE;
    if ($entity->hasParentComment()) {
      $build[$key]['#attributes']['class'][] = 'comments--thread__last_child';
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $comment, EntityViewDisplayInterface $display, $view_mode) {
    parent::alterBuild($build, $comment, $display, $view_mode);
    // Change default drupal comments behaviour
    // to wrap thread and "main" comment together.
    if (empty($comment->in_preview)) {
      // If comment has parent then nothing to do here.
      if (!$comment->hasParentComment()) {
        // Check if it's not the first comment.
        $closing_div = isset($this->previousIntent) ? '</div>' : '';
        // Open thread.
        $build['#prefix'] .= $closing_div . '<div class="comments--thread card">';
      }

      // Need to keep in memory last intent.
      $this->previousIntent = $build['#comment_indent'];

      if (isset($build['#comment_section_end'])) {
        $build['#suffix'] = isset($build['#suffix']) ? ($build['#suffix'] . '</div>') : '</div>';
      }
    }
  }

}
