(function($) {

  var Team = (function() {

    var $contentIntro = $('.content-intro'),
        $teamMember = $('.team-member');

    var reorderNameAndPosition = function reorderNameAndPosition() {
      $teamMember.each(function() {
        var $teamName = $(this).find('.team-member-name');
        var $teamPosition = $(this).find('.team-member-position');

        $teamName.before($teamPosition);
      });
    };

    return {
      reorderNameAndPosition: reorderNameAndPosition
    }

  })();

  /* Exposing our functions to the rest of the application */
  module.exports = Team;

})(jQuery);