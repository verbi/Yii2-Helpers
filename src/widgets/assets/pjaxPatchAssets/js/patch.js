// Pjax hack for JQuery 3
(function($) {
    if ( $.event.props && $.inArray('state', $.event.props) < 0 ) {
      $.event.props.push('state');
    } else if ( ! ('state' in $.Event.prototype) ) {
      $.event.addProp('state');
    }
})(jQuery);