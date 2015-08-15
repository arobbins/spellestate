(function($) {

  var Utils = (function() {

    var $contentIntro = $('.content-intro');

    var addClasses = function addClasses() {
      $contentIntro.closest('.container').addClass('content-intro-wrap');
    };

    return {
      addClasses: addClasses
    }

  })();

  /* Exposing our functions to the rest of the application */
  module.exports = Utils;

})(jQuery);