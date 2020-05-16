<?php

namespace Drupal\openideal_idea\Plugin\RabbitHoleBehaviorPlugin;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPlugin\PageRedirect;
use Drupal\rabbit_hole\Exception\InvalidRedirectResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects to another page.
 *
 * @RabbitHoleBehaviorPlugin(
 *   id = "duplicate_idea_page_redirect",
 *   label = @Translation("Duplicate idea page redirect")
 * )
 */
class DuplicateIdeaPageRedirect extends PageRedirect {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\rabbit_hole\Exception\InvalidRedirectResponseException
   */
  public function performAction(EntityInterface $entity, Response $current_response = NULL) {
    if (
      $entity->bundle() !== 'idea' ||
      empty($entity->field_duplicate_of->entity)
    ) {
      return;
    }

    $target = $entity->field_duplicate_of->entity->toUrl()->toString();
    $response_code = NULL;

    $bundle_entity_type = $entity->getEntityType()->getBundleEntityType();
    $bundle_settings = $this->rhBehaviorSettingsManager
      ->loadBehaviorSettingsAsConfig(
        $bundle_entity_type ?: $entity->getEntityType()->id(),
        $bundle_entity_type ? $entity->bundle() : NULL);

    if (empty($target)) {
      $target = $bundle_settings->get('redirect');
      $response_code = $bundle_settings->get('redirect_code');
    }
    else {
      $response_code = $entity->get('rh_redirect_response')->value;
    }

    // Replace any tokens if applicable.
    $langcode = $entity->language()->getId();

    if ($langcode == LanguageInterface::LANGCODE_NOT_APPLICABLE) {
      $langcode = LanguageInterface::LANGCODE_NOT_SPECIFIED;
    }

    if ($this->moduleHandler->moduleExists('token')) {
      $target = $this->token->replace($target,
        [
          $entity->getEntityTypeId() => $entity,
        ],
        [
          'clear' => TRUE,
          'langcode' => $langcode,
        ], new BubbleableMetadata()
      );
    }

    switch ($response_code) {
      case self::REDIRECT_MOVED_PERMANENTLY:
      case self::REDIRECT_FOUND:
      case self::REDIRECT_SEE_OTHER:
      case self::REDIRECT_TEMPORARY_REDIRECT:
        if ($current_response === NULL) {
          return new TrustedRedirectResponse($target, $response_code);
        }
        else {
          // If a response already exists we don't need to do anything with it.
          return $current_response;
        }
      case self::REDIRECT_NOT_MODIFIED:
        if ($current_response === NULL) {
          $not_modified_response = new Response();
          $not_modified_response->setStatusCode(self::REDIRECT_NOT_MODIFIED);
          $not_modified_response->headers->set('Location', $target);
          return $not_modified_response;
        }
        else {
          // If a response already exists we don't need to do anything with it.
          return $current_response;
        }
      case self::REDIRECT_USE_PROXY:
        if ($current_response === NULL) {
          $use_proxy_response = new Response();
          $use_proxy_response->setStatusCode(self::REDIRECT_USE_PROXY);
          $use_proxy_response->headers->set('Location', $target);
          return $use_proxy_response;
        }
        else {
          // If a response already exists we don't need to do anything with it.
          return $current_response;
        }
      default:
        throw new InvalidRedirectResponseException();
    }
  }

}
