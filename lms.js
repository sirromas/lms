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
			console.log('UserStatus: '+data);
			return data;
	}).fail(function(data) {
	    console.log('Error occured:'+data);
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
		var fn=$('#firstname').val();
		if (fn.length==0) {
			$("#fn_err").html('Please provide firstname');
			return false;
		}
		var ln=$('#lastname').val();
		if (ln.length==0) {
			$("#ln_err").html('Please provide lastname');
			return false;
		}		
		var username = $('#username').val();
		if (username.length==0 || username.length<=5) {
			$("#username_err").html('Please provide correct username');
			return false;
		}
		
		$.post("getGroups.php", {
			username : username
		}).done(function(data) {
			console.log('UserStatus: '+data);
			if (data !=0) {
				$("#username_err").html('Username already in use');
				return false;
			}
			
		})
		
		/*
		if (username.search(/[^a-zA-Z]+/) === -1) {
			$("#username_err").html('Please provide alphabets username');
			return false;
        }
        */
		
		var email = $('#email').val();
		if (email.length==0) {
			$("#email_err").html('Please provide email');
			return false;
		}
		
		$.post("getGroups.php", {
			email : email
		}).done(function(data) {
			console.log ('Email status: '+data);
			if (data!=0) {
				$("#email_err").html('Provided email already in use');
				return false;
			}
			if (validateEmail(email)!=true) {
				$("#email_err").html('Provided email is incorrect');
				return false;
			}
	   })		
				
		var password = $('#password').val();
		if (password.length==0 || password.length<=5) {
			$("#pwd_err").html('Please provide password at least 5 symbols');
			return false;
		}
		
		if ($("#user_type").val()=='student') {
		var school = $('#school').val();
		console.log('School: ' + school);
		}
		
		var address = $('#address').val();
		
		var courses=$('#courses').val();
		if ($('#courses').val()==0) {
			$("#course_err").html('Please select course');
			return false;
		}
		
		var groups = $('#groups').val();
		if (groups==0) {
			$("#course_err").html('Please select group');
			return false;
		}	
		
		var user_type=$("#user_type").val();
		var url='http://mycodebusters.com/lms/moodle/login/signup.php';
		if  (user_type=='student') {
			query= {user_type:user_type,
			      firstname:fn,
				  lastname:ln,
				  username:username,
				  password:password,
				  email:email,
				  school:school,
				  course:courses,
				  group:groups,
				  address:address};			
		}
		else {
			query= {user_type:user_type, 
				  firstname:fn,					  
			      lastname:ln,
				  username:username,
				  password:password,
			      email:email,					  
				  course:courses,
				  group:groups,
				  address:address};		 
		}
		$.post(url, query).done(function(data) {
			$("#signup_content").html(data);
	}) 
	        //e.preventDefault();
		    return false;
	});		
	     
})

/*
 * $.mockjax({ url : "emails.action", response : function(settings) { var email =
 * settings.data.email, emails = [ "glen@marketo.com", "george@bush.gov",
 * "me@god.com", "aboutface@cooper.com", "steam@valve.com", "bill@gates.com" ];
 * this.responseText = "true"; if ($.inArray(email, emails) !== -1) {
 * this.responseText = "false"; } }, responseTime : 500 }); /* });
 */