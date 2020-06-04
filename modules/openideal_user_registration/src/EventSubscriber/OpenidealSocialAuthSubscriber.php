<?php

namespace Drupal\openideal_user_registration\EventSubscriber;

use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\social_auth\Event\SocialAuthEvents;
use Drupal\social_auth\Event\UserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OpenidealSocialAuthSubscriber.
 */
class OpenidealSocialAuthSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;
  use MessengerTrait;

  /**
   * Constructs a new OpenidealSocialAuthSubscriber object.
   */
  public function __construct() {
    $this->messenger();
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [SocialAuthEvents::USER_CREATED => 'socialAuthUserLogin'];
  }

  /**
   * This method is called when the social_auth.user.login is dispatched.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The dispatched event.
   */
  public function socialAuthUserLogin(UserEvent $event) {
    $this->messenger->addMessage($this->t('Please fill your profile.'));
  }

}
