(function($) {

  $(function() {

    var Utils = require('./utils'),
        Team = require('./team');

    /* Utils */
    Utils.addClasses();
    Utils.addBorderOnScroll();
    Utils.removeMailText();
    Utils.toggleRows();
    Utils.responsifyTables();

    /* Team */
    Team.reorderNameAndPosition();

  });

})(jQuery);