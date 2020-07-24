/**
 * @file
 * Attaches behaviors for the Openideal Challenge module's schedule widget.
 */

(function ($, Drupal, window, document) {

  /**
   * Schedule behaviours.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attach local time to the schedule widget.
   */
  Drupal.behaviors.openidealChallengeSchedule = {
    attach: function (context, settings) {
      $('.challenge-schedule-local-machine-time').once('openideal_challenge_schedule').each(function () {
        $(this).text(Drupal.t('Time in your local machine: @time',
          {'@time': new Date().toLocaleTimeString()})
        )
      })
    }
  };
}
)(jQuery, Drupal);
