// JavaScript Document
$(document).ready(function() {
	$("#form").submit(function() {
		var username = $("#username").val();
		var email    = $("#email").val();
		var avatar   = $("#avatar").val();
		var reply    = $("#reply").val();
		if(username == '') {	
			$("#username").next().next(".error").text('Please enter username');
			return false;	
		}
		if(email == '') {	
			$("#email").next().next(".error").text('Please enter email');
			return false;	
		}
		if(avatar == '') {	
			$("#avatar").next().next(".error").text('Please choose avatar');
			return false;	
		}
		if(reply == '') {	
			$("#reply").next().next(".error").text('Please enter reply');
			return false;	
		}		
	});
});