/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  /**
   * Attach behaviours on homepage.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Implement cascading grid layout library.
   */
  Drupal.behaviors.openidealContentHomePage = {
    attach: function (context, settings) {
      $('.view-frontpage .view-content').once('openideal_content_home_page').masonry({
        itemSelector: '.views-row',
      })
    }
  }
}
)(jQuery, Drupal);