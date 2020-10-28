$('#btnSidebar').click(function() {
  
  $(this).toggleClass('is-active');
  $('.sidebar').toggleClass('is-active');
  	
  if ($(window).width() > 576) {
	  if ($('.sidebar').hasClass('is-active')) {
	    setCookie({name: 'sidebar_active', value: true });
	  } else {
	    setCookie({name: 'sidebar_active', value: false });
	  }
  } else {
  	if (! $('.sidebar').hasClass('is-active')) {
  		setCookie({name: 'sidebar_active', value: false});
  	}
  }
});


$(document).on('focus', 'input, select', function() {
  $(this).removeClass('is-invalid')
    .next('.text-danger')
    .remove();
    
  $(this).parent().next('.text-danger').remove();
}); 

$(document).on('click', '.btn-today', function(e) {
  e.preventDefault();

  let date = new Date();
  let today = '';
  let month = date.getMonth() + 1;

  today += date.getDate() + '/';
  today += (month < 10) ? '0' + month : month;
  today += '/';
  today += date.getFullYear();

  $(this).parents('.input-group').find('input').val(today).focus();

});

$('[data-toggle="tooltip"]').tooltip();
