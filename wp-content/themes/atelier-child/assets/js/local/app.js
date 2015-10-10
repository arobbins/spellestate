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
    // Utils.addClassWhenSubCheckout();

    /* Team */
    Team.reorderNameAndPosition();

  });

})(jQuery);