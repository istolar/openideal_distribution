/**
 * @file
 * Attaches like rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.likeRating = {
    attach: function (context, settings) {
      $('body').find('.like').each(function () {
        var $this = $(this);
        $(this).find('select').once('processed').each(function () {
          $this.find('[type=submit]').hide();
          var $select = $(this);
          var isPreview = $select.data('is-edit');
          // Depending on vote entity change the body.
          var likeBody = settings.openidealUser.comment
          ? '<i class="fa fa-thumbs-up"></i>'
          : '<span class="mr-2 text-uppercase"><i class="fa fa-thumbs-up"></i>' + Drupal.t(' Like idea') + '</span>';

          $select.after(`<div class="like-rating"><a href="#">${likeBody}</a></div>`).hide();
          $this.find('.like-rating a').eq(0).each(function () {
            $(this).bind('click',function (e) {
              if (isPreview) {
                return;
              }
              // Depending on vote status - trigger appropriate button.
              var find = settings.openidealUser.voted ? '.openideal-votes-like-delete' : '.openideal-votes-like-submit';
              e.preventDefault();
              $select.get(0).selectedIndex = 0;
              $this.find(find).trigger('click');
              $this.find('a').addClass('disabled');
            })
          })
        })
      });
    }
  };
})(jQuery, Drupal);
