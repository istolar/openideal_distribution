<?php

namespace Drupal\openideal_user_registration\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class OpenIdealRouteSubscriber.
 *
 * Listens to the dynamic route events.
 */
class OpenIdealRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('user_registrationpassword.confirm')) {
      $route->setDefaults([
        '_controller' => '\Drupal\openideal_user_registration\Controller\OpenIdealUserRegistrationPassword::confirmAccount',
      ]);
    }
  }

}
