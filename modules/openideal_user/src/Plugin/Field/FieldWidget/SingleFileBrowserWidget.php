<?php

namespace Drupal\openideal_user\Plugin\Field\FieldWidget;

use Drupal\Core\Url;
use Drupal\entity_browser\Plugin\Field\FieldWidget\FileBrowserWidget;

/**
 * Entity browser single file widget.
 *
 * @FieldWidget(
 *   id = "openideal_user_entity_browser_single_file",
 *   label = @Translation("Single file entity browser"),
 *   provider = "entity_browser",
 *   multiple_values = TRUE,
 *   field_types = {
 *     "file",
 *     "image"
 *   }
 * )
 */
class SingleFileBrowserWidget extends FileBrowserWidget {

  /**
   * {@inheritdoc}
   *
   * Removed table implementation for multiple values,
   * because we need this only for single image.
   */
  protected function displayCurrentSelection($details_id, array $field_parents, array $entities) {
    $field_type = $this->fieldDefinition->getType();
    $field_settings = $this->fieldDefinition->getSettings();
    $field_machine_name = $this->fieldDefinition->getName();
    $widget_settings = $this->getSettings();
    $view_mode = $widget_settings['view_mode'];
    $file_settings = $this->configFactory->get('file.settings');
    $can_edit = (bool) $widget_settings['field_widget_edit'];
    $has_file_entity = $this->moduleHandler->moduleExists('file_entity');
    $order_class = $field_machine_name . '-delta-order';

    $delta = 0;

    $current = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entities-list', 'single-entity-browser-widget']],
    ];

    /** @var \Drupal\file\FileInterface[] $entities */
    foreach ($entities as $entity) {
      // Check to see if this entity has an edit form. If not, the edit button
      // will only throw an exception.
      if (!$entity->getEntityType()->getFormClass('edit')) {
        $edit_button_access = FALSE;
      }
      elseif ($has_file_entity) {
        $edit_button_access = $can_edit && $entity->access('update', $this->currentUser);
      }

      $entity_id = $entity->id();

      // Find the default description.
      $description = '';
      $display_field = $field_settings['display_default'];
      $alt = '';
      $title = '';
      $weight = $delta;
      $width = NULL;
      $height = NULL;
      foreach ($this->items as $item) {
        if ($item->target_id == $entity_id) {
          if ($field_type == 'file') {
            $description = $item->description;
            $display_field = $item->display;
          }
          elseif ($field_type == 'image') {
            $alt = $item->alt;
            $title = $item->title;
            $width = $item->width;
            $height = $item->height;
          }
          $weight = $item->_weight ?: $delta;
        }
      }

      // Provide a rendered entity if a view builder is available.
      if ($has_file_entity) {
        $current[$entity_id]['display'] = $this->entityTypeManager->getViewBuilder('file')->view($entity, $view_mode);
      }
      // For images, support a preview image style as an alternative.
      elseif ($field_type == 'image' && !empty($widget_settings['preview_image_style'])) {
        $uri = $entity->getFileUri();
        $current[$entity_id]['display'] = [
          '#weight' => -10,
          '#theme' => 'image_style',
          '#width' => $width,
          '#height' => $height,
          '#style_name' => $widget_settings['preview_image_style'],
          '#uri' => $uri,
        ];
      }

      $current[$entity_id] += [
        'meta' => [
          'display_field' => [
            '#type' => 'checkbox',
            '#title' => $this->t('Include file in display'),
            '#default_value' => (bool) $display_field,
            '#access' => FALSE,
          ],
          'description' => [
            '#type' => $file_settings->get('description.type'),
            '#title' => $this->t('Description'),
            '#default_value' => $description,
            '#size' => 45,
            '#maxlength' => $file_settings->get('description.length'),
            '#description' => $this->t('The description may be used as the label of the link to the file.'),
            '#access' => FALSE,
          ],
          'alt' => [
            '#type' => 'textfield',
            '#title' => $this->t('Alternative text'),
            '#default_value' => $alt,
            '#size' => 45,
            '#maxlength' => 512,
            '#description' => $this->t('This text will be used by screen readers, search engines, or when the image cannot be loaded.'),
            '#access' => $field_type == 'image' && $field_settings['alt_field'],
            '#required' => FALSE,
          ],
          'title' => [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#default_value' => $title,
            '#size' => 45,
            '#maxlength' => 1024,
            '#description' => $this->t('The title is used as a tool tip when the user hovers the mouse over the image.'),
            '#access' => FALSE,
            '#required' => $field_type == 'image' && $field_settings['title_field_required'],
          ],
        ],
        'edit_button' => [
          '#type' => 'submit',
          '#value' => $this->t('Edit'),
          '#ajax' => [
            'url' => Url::fromRoute('entity_browser.edit_form', ['entity_type' => $entity->getEntityTypeId(), 'entity' => $entity_id]),
            'options' => ['query' => ['details_id' => $details_id]],
          ],
          '#attributes' => [
            'data-entity-id' => $entity->getEntityTypeId() . ':' . $entity->id(),
            'data-row-id' => $delta,
            'class' => ['edit-button'],
          ],
          '#access' => $edit_button_access,
        ],
        'remove_button' => [
          '#type' => 'submit',
          '#value' => $this->t('Remove'),
          '#ajax' => [
            'callback' => [get_class($this), 'updateWidgetCallback'],
            'wrapper' => $details_id,
          ],
          '#submit' => [[get_class($this), 'removeItemSubmit']],
          '#name' => $field_machine_name . '_remove_' . $entity_id . '_' . md5(json_encode($field_parents)),
          '#limit_validation_errors' => [
            array_merge($field_parents, [$field_machine_name, 'target_id']),
          ],
          '#attributes' => [
            'data-entity-id' => $entity->getEntityTypeId() . ':' . $entity->id(),
            'data-row-id' => $delta,
            'class' => ['remove-button'],
          ],
          '#access' => (bool) $widget_settings['field_widget_remove'],
        ],
        '_weight' => [
          '#type' => 'weight',
          '#title' => $this->t('Weight for row @number', ['@number' => $delta + 1]),
          '#title_display' => 'invisible',
          // Note: this 'delta' is the FAPI #type 'weight' element's property.
          '#delta' => count($entities),
          '#default_value' => $weight,
          '#attributes' => ['class' => [$order_class]],
          '#access' => FALSE,
        ],
      ];

      $current['#attached']['library'][] = 'entity_browser/file_browser';

      $delta++;
    }

    return $current;
  }

}
