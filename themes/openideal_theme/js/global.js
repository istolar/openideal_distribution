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
      $('.comments--header__add-comment-btn, .site-footer-open-comments-btn', context).once('openideal-theme-comments-form-animation').on('click', function () {
        var commentsBottom = $('.comments--bottom');
        // hide sidebar
        commentsBottom.toggle('slow');

        // Scroll to comments body.
        $([document.documentElement, document.body]).animate({
          scrollTop: commentsBottom.offset().top
        }, 1000);
      });

      $('.comment-form--cancel-btn', context).once('openideal-theme-comments-form-animation-reply').on('click', function () {
        // hide sidebar
        var $form = $(this).closest('form');
        if ($form.hasClass('ajax-comments-form-reply')) {
          $form.toggle('slow');
        }
        else {
          $('.comments--bottom').toggle('slow');
        }
      });

      $('.ajax-comments-form-edit .comment-form--cancel-btn').once('openideal-theme-comments-form-animation-edit').addClass('d-none');
    }
  }

  /**
   * Change votingapi_reaction like label.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Change label.
   */
  Drupal.behaviors.openidealThemeLikeWidgetLabel = {
    attach: function (context, settings) {
      var $label = $('.votingapi-reaction-label', context);
      if ($label.parents('.region-sidebar').length) {
        $label.text('Like idea')
      }
      else if ($label.parents('.site-footer').length) {
        $label.text('Like')
      }
    }
  }

}
)(jQuery, Drupal);
