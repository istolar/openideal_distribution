<?php

namespace Drupal\openideal_user\EventSubscriber;

use Drupal\social_auth_facebook\FacebookAuthManager;
use Drupal\social_auth_linkedin\LinkedInAuthManager;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\social_auth\Event\SocialAuthEvents;
use Drupal\social_auth\Event\UserEvent;
use Drupal\social_auth\Event\UserFieldsEvent;
use Drupal\social_auth_google\GoogleAuthManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OpenidealSocialAuthSubscriber.
 */
class OpenidealSocialAuthSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;
  use MessengerTrait;

  const GOOGLE_PLUGIN_ID = 'social_auth_google';
  const LINKEDIN_PLUGIN_ID = 'social_auth_linkedin';
  const FB_PLUGIN_ID = 'social_auth_facebook';

  /**
   * Google auth manager.
   *
   * @var \Drupal\social_auth_google\GoogleAuthManager
   */
  protected $googleAuthManager;

  /**
   * LinkedIn auth manager.
   *
   * @var \Drupal\social_auth_linkedin\LinkedInAuthManager
   */
  protected $linkedInAuthManager;

  /**
   * FB auth manager.
   *
   * @var \Drupal\social_auth_google\GoogleAuthManager
   */
  protected $fbAuthManager;

  /**
   * Mapped plugin id's with related methods.
   *
   * @var array
   */
  protected $socialsPlugins = [
    self::GOOGLE_PLUGIN_ID => 'googleAuthManager',
    self::LINKEDIN_PLUGIN_ID => 'linkedInAuthManager',
    self::FB_PLUGIN_ID => 'fbAuthManager',
  ];

  /**
   * OpenidealSocialAuthSubscriber constructor.
   *
   * @param \Drupal\social_auth_google\GoogleAuthManager $social_auth_google
   *   Google auth manager.
   * @param \Drupal\social_auth_linkedin\LinkedInAuthManager $social_auth_linkedin
   *   LinkedIn auth manager.
   * @param \Drupal\social_auth_facebook\FacebookAuthManager $social_auth_facebook
   *   FB auth manager.
   */
  public function __construct(GoogleAuthManager $social_auth_google, LinkedInAuthManager $social_auth_linkedin, FacebookAuthManager $social_auth_facebook) {
    $this->googleAuthManager = $social_auth_google;
    $this->linkedInAuthManager = $social_auth_linkedin;
    $this->fbAuthManager = $social_auth_facebook;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      SocialAuthEvents::USER_CREATED => 'socialAuthUserLogin',
      SocialAuthEvents::USER_FIELDS => 'socialAuthUserFields',
    ];
  }

  /**
   * This method is called when the USER_CREATED event is dispatched.
   *
   * @param \Drupal\social_auth\Event\UserEvent $event
   *   The dispatched event.
   */
  public function socialAuthUserLogin(UserEvent $event) {
    $this->messenger()->addMessage($this->t('Please fill your profile.'));
  }

  /**
   * This method is called when the USER_FIELDS event is dispatched.
   *
   * @param \Drupal\social_auth\Event\UserFieldsEvent $event
   *   The dispatched event.
   */
  public function socialAuthUserFields(UserFieldsEvent $event) {
    $plugin_id = $event->getPluginId();

    // Get data from socials and set it in user fields.
    if (array_key_exists($plugin_id, $this->socialsPlugins)) {
      /** @var \Drupal\social_api\AuthManager\OAuth2ManagerInterface $social_manager */
      $social_manager = $this->{$this->socialsPlugins[$plugin_id]};
      $resource_owner = $social_manager->getUserInfo();
      $user_fields = $event->getUserFields();
      $user_fields += [
        'field_first_name' => $resource_owner->getFirstName() ?? '',
        'field_last_name' => $resource_owner->getLastName() ?? '',
      ];
      if ($plugin_id == self::FB_PLUGIN_ID) {
        // @Todo: check in what format FB give gender.
        $user_fields += [
          'field_gender' => $resource_owner->getGender() ?? '',
        ];
      }
      $event->setUserFields($user_fields);
    }
  }

}
