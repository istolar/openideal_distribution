/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  /**
   * Builds a div element with the aria-live attribute and add it to the DOM.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior for drupalAnnounce.
   */
  Drupal.behaviors.bootstrap_barrio_subtheme = {
    attach: function (context, settings) {
      var position = $(window).scrollTop();
      $(window).scroll(function () {
        var $body = $('body');
        if ($(this).scrollTop() > 50) {
          $body.addClass("scrolled");
        } else {
          $body.removeClass("scrolled");
        }
        var scroll = $(window).scrollTop();
        if (scroll > position) {
          $body.addClass("scrolldown");
          $body.removeClass("scrollup");
        } else {
          $body.addClass("scrollup");
          $body.removeClass("scrolldown");
        }
        position = scroll;
      });

    }
  };

  /**
   * Toggle the
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior for drupalAnnounce.
   */
  Drupal.behaviors.openidealThemeNavigationToggle = {
    attach: function (context, settings) {
      $('.site-navigation--dismiss, .overlay').once('openideal-theme-navigation-toggle-overlay').on('click', function () {
        // hide sidebar
        $('#site-navigation').removeClass('active');
        // hide overlay
        $('.overlay').removeClass('active');
      });

      $('#sidebar-collapse').once('openideal-theme-navigation-toggle').on('click', function () {
        // open sidebar
        $('#site-navigation').addClass('active');
        // fade in the overlay
        $('.overlay').addClass('active');
      });
    }
  }

}
)(jQuery, Drupal);
