(function($) {

  $(function() {

    var Utils = require('./utils'),
        Team = require('./team');

    /* Utils */
    Utils.addClasses();

    /* Team */
    Team.reorderNameAndPosition();

  });

})(jQuery);