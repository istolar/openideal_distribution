/**
 * @file
 * Attaches group_widget front-end behaviours.
 */

(function ($, Drupal) {
  Drupal.behaviors.openidealUserGroupWidget = {
    attach: function (context, settings) {
      $('#edit-group-roles .form-item-group-roles-idea-author').addClass('visually-hidden')
    }
  };
})(jQuery, Drupal);
