<?php

namespace Drupal\openideal_idea\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;

/**
 * Plugin implementation of the overall score formatter.
 *
 * @FieldFormatter(
 *   id = "openideal_overall_score_formatter",
 *   label = @Translation("Openideal overall score formatter"),
 *   description = @Translation("Overall score formatter."),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class OpenidealComputedFieldFormatter extends StringFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $item->value,
        '#cache' => [
          'max-age' => 3600,
        ],
      ];
    }
    return $elements;
  }

}
