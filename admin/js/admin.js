function setControls() {
}

$(document).ready(function(){
	$('#hmenu ul li').hover(
		function() {
			$(this).find('ul:first').fadeIn("fast");
			$(this).addClass("hilite");
		},
		function() {            
			$(this).find('ul:first').fadeOut("fast");
			$(this).removeClass("hilite");						
		}
	);    
	//$('#hmenu li:has(ul)').find('a:first').append(' &raquo;');
	$('#hmenu ul li ul li').find('ul:first').addClass("fleft");
	
	$('.jclock').jclock();
	
	$('input[@type=button]').addClass('button');
	$('input[@type=submit]').addClass('button');
	
	setControls();			
});

$(window).resize(function() {
    setControls();
});

function getKeywords(field_id){
	var text = $('#'+field_id).val();	
	alert(text);
}