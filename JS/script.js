$(document).ready(function() {

	//fade in logo for homepage
	$('.logo').fadeIn(2000);
	$('#new_item').click(function() {
		$('#item_form').show(1000);
	});

	$('.x').click(function() {
		$('#item_form').hide(1000);
	});

	//show menu on click
	$(".menu").click(function(){
			
		$(".menu-container").addClass("menu-active");
		$("body").addClass("no-scroll");
	});
	
	//hide menu on click
	$(".x").click(function(){

		$("body").removeClass("no-scroll");
  		$(".menu-container").removeClass("menu-active");
	});

	//for ingredients
	$(".ing-square").click(function(){
		
      	$("#intro-desc").addClass("text-inactive");
      	
		var name = $(this).attr('id');
		var desc = ".for[id='" + name + "-text']";
		$(".for").removeClass("text-active");
		$(".ing-square").removeClass("ing-active");
		$("#" + name).addClass("ing-active");
		$(desc).addClass("text-active");
	});


});