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
   * @Todo: Do the logic in the backend.
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
        $label.text(Drupal.t('Like idea'))
      }
      else if ($label.parents('.site-footer').length) {
        $label.text(Drupal.t('Like'))
      }
    }
  }

  /**
   * Add logic to copy url button.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Implement ability to copy current url into clipboard.
   */
  Drupal.behaviors.openidealThemeCopyUrlButton = {
    attach: function (context, settings) {
      $('.share-buttons--buttons__copy_url', context).once('openideal_theme_copy_url_button').each(function () {
        var $this = $(this);
        // Enable tooltip for element.
        $this.tooltip();

        $this.on('click', function () {
          $(this).tooltip()
          var $temp = $('<input>'),
          url = window.location.href;
          $('body').append($temp);
          $temp.val(url).select();
          document.execCommand('copy');
          $temp.remove();
          $this.tooltip('hide')
          .attr('data-original-title', 'Copied!')
          .tooltip('show');
        });
      })
    }
  }

  /**
   * Attach behaviours on mobile share block.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Implement ability to show/hide mobile share block.
   */
  Drupal.behaviors.openidealThemeMobileShareBlock = {
    attach: function (context, settings) {
      $('.site-footer-open-share-btn', context).once('openideal_theme_mobile_share_block').on('click', function () {
        $('.mobile-share-footer').toggle(400);
      })
    }
  }

  /**
   * Add the behaviour to comment reply link.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Add the count of replies in from of reply link,
   *   and logic to hide/show replies.
   */
  Drupal.behaviors.openidealThemeCommentsReply = {
    attach: function (context, settings) {
      $('.comments--thread', context).once('openideal_theme_comments_last_child').each(function () {
        var $this = $(this);

        var $comments = $this.find('.single-comment').toArray();

        $this.find('.indented').each(function () {
          $(this).hide()
        })

        for (var $i = $comments.length - 1; $i >= 0; $i--) {
          // If the comment has not children then don't need to show border.
          var $current = $($comments[$i]);
          if ($current.is(':visible')) {
            $($current).addClass('comments--thread__border-none')
            break;
          }
          else {
            $current.addClass('comments--thread__border-none')
          }
        }
      });

      $('.comment-show .single-comment--open-replies', context).once('openideal_theme_comments_reply').each(function () {
        var $this = $(this);
        var $currentComment = $this.closest('.single-comment');
        var main = $currentComment.siblings('.indented');
        var replies = 0;
        if (main.length > 0) {
          replies = main.find('.single-comment').length
        }
        $this.after('<span>' + replies + Drupal.t(' replies') + '</span>');

        if (replies > 0) {
          $this.closest('.comment-show').on('click', function () {
            main.toggle('slow');
            $currentComment.toggleClass('comments--thread__border-none');
          });
        }
      })
    }
  }

}
)(jQuery, Drupal);
