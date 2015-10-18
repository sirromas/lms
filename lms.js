$(document).ready(function() {
	$('#a_student').on('click', function() {
		console.log("Student signup clicked");
		getSignupForm('student');
	});

	$("#a_tutor").click(function() {
		console.log("Tutor signup clicked");
		getSignupForm('tutor');
	});

	function getSignupForm(user) {

		$.post("signup_form.php", {
			user : user
		}).done(function(data) {
			$("#signup_content").html(data);
		});
	}

	$('#courses').live('change', function() {
		var user_type = $("#user_type").val();
		console.log('User type: ' + user_type);
		if (user_type == 'student') {
			var course = $("#courses").val();
			console.log('Course selected: ' + course);
			$.post("getGroups.php", {
				course : course
			}).done(function(data) {
				$("#for_gr").html(data);
			});
		}
	});

	function validateEmail(email) {
	    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	    return re.test(email);
	}
	
   function isEmailUsed (email) {
		$.post("getGroups.php", {
			email : email
		}).done(function(data) {
			return data;
	}) 
	
	}
	
   function isUserUsed (username) {
		$.post("getGroups.php", {
			username : username
		}).done(function(data) {
			return data;
	})
	
	}
		
	
	function processEmail (email) {
			valid=validateEmail(email);
			used=isEmailUsed (email)
			if (valid==true && used==0) {
				return true;
				}
			else {
				return false;
			}
    }
		
	$('#signupform').live('change', function() {		
		$("#fn_err").html('');
		$("#ln_err").html('');
		$("#username_err").html('');
		$("#email_err").html('');
		$("#pwd_err").html('');
		$("#course_err").html('');
		$("#course_err").html('');
    });
		
	$('#signupform').live('submit', function() {
		
		if ($('#firstname').val().length==0) {
			$("#fn_err").html('Please provide firstname');
			return false;
		}		
		if ($('#lastname').val()==0) {
			$("#ln_err").html('Please provide lastname');
			return false;
		}		
		var username = $('#username').val();
		if ($('#username').val()==0) {
			$("#username_err").html('Please provide username');
			return false;
		}
		if (isUserUsed (username)!=1) {
			$("#username_err").html('Username already in use');
			return false;
		}			
		var email = $('#email').val();
		if ($('#email').val()==0) {
			$("#email_err").html('Please provide email');
			return false;
		}
		if (processEmail (email)!=true) {
			$("#email_err").html('Provided email is incorrect or already in use');
			return false;
		}		
		var password = $('#password').val();
		if ($('#password').val()==0 || $('#password').val().length<=5) {
			$("#pwd_err").html('Please provide password at least 5 symbols');
			return false;
		}		
		if ($("#user_type").val()=='student') {
		var school = $('#school').val();
		console.log('School: ' + school);
		}
		
		var address = $('#address').val();
		
		if ($('#courses').val()==0) {
			$("#course_err").html('Please select course');
			return false;
		}
		
		var groups = $('#groups').val();
		if (groups = $('#groups').val()==0) {
			$("#course_err").html('Please select group');
			return false;
		}
		
		console.log("Courses: " + courses);
		console.log("Groups: " + groups);
		console.log('Email: ' + email);
		console.log('Validate email:'+validateEmail(email));
		console.log('Username: ' + username);
		console.log('First name: ' + Fn);
		console.log('Last name: ' + Ln);
		console.log('Paswword: ' + password);		
		console.log('Address: ' + address);
		
		return true;
		
	});		
		
})

/*
 * $.mockjax({ url : "emails.action", response : function(settings) { var email =
 * settings.data.email, emails = [ "glen@marketo.com", "george@bush.gov",
 * "me@god.com", "aboutface@cooper.com", "steam@valve.com", "bill@gates.com" ];
 * this.responseText = "true"; if ($.inArray(email, emails) !== -1) {
 * this.responseText = "false"; } }, responseTime : 500 }); /* });
 */