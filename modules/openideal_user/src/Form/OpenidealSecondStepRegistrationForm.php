<?php

namespace Drupal\openideal_user\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Url;

/**
 * OpenidealSecondStepRegistration object.
 */
class OpenidealSecondStepRegistrationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user-additional-details';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
    self::setEntity($user);

    /* @var $entity \Drupal\user\Entity\User */
    $form = parent::buildForm($form, $form_state);

    $last_name = $user->get('field_last_name')->getString();
    $first_name = $user->get('field_first_name')->getString();
    if (!empty($first_name)) {
      $form['field_last_name']['widget']['0']['value']['#required'] = FALSE;
      $form['field_last_name']['#type'] = 'hidden';
    }
    if (!empty($last_name)) {
      $form['field_first_name']['widget']['0']['value']['#required'] = FALSE;
      $form['field_first_name']['#type'] = 'hidden';
    }

    if (!empty($first_name) && !empty($last_name)) {
      $form['actions']['skip'] = [
        '#type' => 'link',
        '#title' => $this->t('Skip'),
        '#url' => Url::fromRoute('<front>'),
        '#weight' => 2,
        '#attributes' => ['class' => ['btn', 'btn-warning']],
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    $form_state->setRedirect('<front>');
  }

}
