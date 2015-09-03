(function($) {

  var Utils = (function() {

    var $contentIntro = $('.content-intro'),
        $headerSticky = $('.is-sticky'),
        $mailNavItem = $('.mailing-list span'),
        $reviewRows = $('.accordion-heading'),
        $tables = $('.shop_table, .shop_table_responsive');

    var addClasses = function addClasses() {
      $contentIntro.closest('.container').addClass('content-intro-wrap');
    };

    var addBorderOnScroll = function addBorderOnScroll() {
      $headerSticky.parent().parent().css('border-bottom', '2px solid #e4e4e4 !important');
    };

    var removeMailText = function removeMailText() {
      $mailNavItem.empty();
      $mailNavItem.addClass('icon-envelope');
    };

    var toggleRows = function toggleRows() {

      $reviewRows.click(function() {

        var $content = $(this).next();
        var $icon = $(this).find('i');

        $content.slideToggle();

        $icon.toggleClass("fa-chevron-right fa-chevron-down");

      });

    }

    var responsifyTables = function responsifyTables() {
      $tables.stacktable();
    }

    return {
      addClasses: addClasses,
      addBorderOnScroll: addBorderOnScroll,
      removeMailText: removeMailText,
      toggleRows: toggleRows,
      responsifyTables: responsifyTables
    }

  })();

  /* Exposing our functions to the rest of the application */
  module.exports = Utils;

})(jQuery);