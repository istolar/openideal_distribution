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
    return 'user_additional_details';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = $this->entityTypeManager->getStorage('user')->load($this->currentUser()->id());
    self::setEntity($user);

    /* @var $entity \Drupal\user\Entity\User */
    $form = parent::buildForm($form, $form_state);
    $first_name = $user->get('field_first_name');
    $last_name = $user->get('field_last_name');
    if (!$first_name->isEmpty()) {
      $form['field_first_name']['widget']['0']['value']['#required'] = FALSE;
      $form['field_first_name']['#access'] = FALSE;
    }
    if (!$last_name->isEmpty()) {
      $form['field_last_name']['widget']['0']['value']['#required'] = FALSE;
      $form['field_last_name']['#access'] = FALSE;
    }

    if (!$first_name->isEmpty() && !$last_name->isEmpty()) {
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
