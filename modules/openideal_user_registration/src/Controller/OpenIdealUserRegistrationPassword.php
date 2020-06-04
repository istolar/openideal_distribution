<?php

namespace Drupal\openideal_user_registration\Controller;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Url;
use Drupal\user\UserStorageInterface;
use Drupal\user_registrationpassword\Controller\UserRegistrationPassword;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * User registration password controller class.
 *
 * @Todo: remove when user_registrationpassword module implement:
 *   - ability to change after registration route.
 *      https://www.drupal.org/project/user_registrationpassword/issues/3067228
 *      https://www.drupal.org/project/user_registrationpassword/issues/2868703
 *   - ability to set registration message.
 */
class OpenIdealUserRegistrationPassword extends UserRegistrationPassword {

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * {@inheritDoc}
   */
  public function __construct(DateFormatterInterface $date_formatter, UserStorageInterface $user_storage, QueryInterface $entity_query) {
    parent::__construct($date_formatter, $user_storage, $entity_query);
    $this->messenger();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->time = $container->get('datetime.time');
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function confirmAccount($uid, $timestamp, $hash) {
    $route_name = '<front>';
    $route_options = [];
    $current_user = $this->currentUser();

    // Verify that the user exists.
    if ($current_user === NULL) {
      throw new AccessDeniedHttpException();
    }

    // When processing the one-time login link, we have to make sure that a user
    // isn't already logged in.
    if ($current_user->isAuthenticated()) {
      // The existing user is already logged in.
      if ($current_user->id() == $uid) {
        $this->messenger->addMessage($this->t('You are currently authenticated as user %user.', ['%user' => $current_user->getAccountName()]));

        // Redirect to user page.
        $route_name = 'entity.user.canonical';
        $route_options = ['user' => $current_user->id()];
      }
      // A different user is already logged in on the computer.
      else {
        $reset_link_account = $this->userStorage->load($uid);
        if (!empty($reset_link_account)) {
          $this->messenger->addMessage($this->t('Another user (%other_user) is already logged into the site on this computer, but you tried to use a one-time link for user %resetting_user. Please <a href=":logout">log out</a> and try using the link again.',
            [
              '%other_user' => $current_user->getDisplayName(),
              '%resetting_user' => $reset_link_account->getDisplayName(),
              ':logout' => Url::fromRoute('user.logout'),
            ]), 'warning');
        }
        else {
          // Invalid one-time link specifies an unknown user.
          $route_name = user_registrationpassword_set_message('linkerror', TRUE);
        }
      }
    }
    else {
      // Time out, in seconds, until login URL expires. 24 hours = 86400
      // seconds.
      $timeout = $this->config('user_registrationpassword.settings')->get('registration_ftll_timeout');
      $current = $this->time->getRequestTime();
      $timestamp_created = $timestamp - $timeout;

      // Some redundant checks for extra security ?
      $users = $this->entityQuery
        ->condition('uid', $uid)
        ->condition('status', 0)
        ->condition('access', 0)
        ->execute();

      // Timestamp can not be larger then current.
      /** @var \Drupal\user\UserInterface $account */
      if ($timestamp_created <= $current && !empty($users) && $account = $this->userStorage->load(reset($users))) {
        // Check if we have to enforce expiration for activation links.
        if ($this->config('user_registrationpassword.settings')->get('registration_ftll_expire') && !$account->getLastLoginTime() && $current - $timestamp > $timeout) {
          $route_name = user_registrationpassword_set_message('linkerror', TRUE);
        }
        // Else try to activate the account.
        // Password = user's password - timestamp = current request - login =
        // username.
        elseif ($account->id() && $timestamp >= $account->getCreatedTime() && !$account->getLastLoginTime() && $hash == user_pass_rehash($account, $timestamp)) {
          // Format the date, so the logs are a bit more readable.
          $date = $this->dateFormatter->format($timestamp);
          $this->getLogger('user')->notice('User %name used one-time login link at time %timestamp.', ['%name' => $account->getAccountName(), '%timestamp' => $date]);
          // Activate the user and update the access and login time to $current.
          $account
            ->activate()
            ->setLastAccessTime($current)
            ->setLastLoginTime($current)
            ->save();

          // user_login_finalize() also updates the login timestamp of the
          // user, which invalidates further use of the one-time login link.
          user_login_finalize($account);

          // Display default welcome message.
          $this->messenger->addMessage($this->t('Please fill your profile.'));
          // Redirect to user.
          $route_name = 'entity.user.edit_form';
          $route_options = ['user' => $account->id()];
        }
        // Something else is wrong, redirect to the password
        // reset form to request a new activation email.
        else {
          $route_name = user_registrationpassword_set_message('linkerror', TRUE);
        }
      }
      else {
        // Deny access, no more clues.
        // Everything will be in the watchdog's
        // URL for the administrator to check.
        $route_name = user_registrationpassword_set_message('linkerror', TRUE);
      }
    }

    return $this->redirect($route_name, $route_options);
  }

}
