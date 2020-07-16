/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  /**
   * Default bootstrap barrio subtheme behaviour.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Add scroll event to body.
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
   * Main navigation behaviour.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Toggle the main navigation.
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

  /**
   * Comments form behaviour.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Hide/show comments add form.
   */
  Drupal.behaviors.openidealThemeCommentsFormAnimation = {
    attach: function (context, settings) {
      $('.comments--header__add-comment-btn, .comment-form--cancel-btn', context).once('openideal-themec-omments-form-animation').on('click', function () {
        // hide sidebar
        $('.comments--bottom').toggle('slow');
      });
    }
  }

}
)(jQuery, Drupal);
