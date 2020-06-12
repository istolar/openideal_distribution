/**
 * @file
 * Attaches is openideal_idea_useful rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.openidealIdeaUsefulRating = {
    attach: function (context, settings) {
      $('body').find('.openideal-idea-useful').each(function () {
        var $this = $(this);
        $(this).find('select').once('processed').each(function () {
          $this.find('[type=submit]').hide();
          var $select = $(this);
          $select.after('<div class="openideal-useful-rating"><a href="#"><i class="fa fa-thumbs-up"></a></i></div>').hide();
          $this.find('.openideal-useful-rating a').eq(0).each(function () {
            $(this).bind('click',function (e) {
              e.preventDefault();
              $select.get(0).selectedIndex = 0;
              $this.find('[type=submit]').trigger('click');
              $this.find('a').addClass('disabled');
            })
          })
          $this.find('.openideal-useful-rating a').eq(1).each(function () {
            $(this).bind('click',function (e) {
              e.preventDefault();
              $select.get(0).selectedIndex = 1;
              $this.find('[type=submit]').trigger('click');
              $this.find('a').addClass('disabled');
            })
          })
        })
      });
    }
  };
})(jQuery, Drupal);
