/* Blog Archive Component
===================================================================================================================== */

import $ from 'jquery';

$(function() {
  
  // Handle Collapsable Blog Archive Components:
  
  $('.blogarchivecomponent.can-collapse').each(function() {
    
    var $self  = $(this);
    var $years = $self.find('a.year');
    
    $years.on('click', function(e) {
      e.preventDefault();
      $(this).toggleClass('opened');
    });
    
  });
  
});
