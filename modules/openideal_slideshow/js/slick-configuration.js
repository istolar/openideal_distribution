(function ($, Drupal, window, document) {

  /**
   * Slideshow behaviour.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attach slick carousel to Slideshow block.
   */
  Drupal.behaviors.OpenidealSlideshowSlickConfig = {
    attach: function (context, settings) {
      $('.openideal-slideshow', context).once('openideal_slideshow_slick_configuration').slick({
        arrows: false,
        lazyLoad: 'progressive'
      });

    }
  };
}
)(jQuery, Drupal);
