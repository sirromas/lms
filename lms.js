
/*******************************************************************************
 * 
 * Code related to signup process
 * 
 ******************************************************************************/

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
		$("#email_err").html('');
		$("#pwd_err").html('');
		$("#course_err").html('');
		$("#course_err").html('');
    });
		
	$('#signupform').live('submit', function() {
		var fn=$('#firstname').val();
		console.log('Fisrt name:'+fn);
		if (fn.length==0) {
			$("#fn_err").html('Please provide firstname');
			return false;
		}
		var ln=$('#lastname').val();
		if (ln.length==0) {
			$("#ln_err").html('Please provide lastname');
			return false;
		}		
				
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
			$("#group_err").html('Please select group');
			return false;
		}	
		
		var user_type=$("#user_type").val();
		var url='http://mycodebusters.com/lms/moodle/login/signup.php';
		if  (user_type=='student') {
			query= {user_type:user_type,
			      firstname:fn,
				  lastname:ln,
				  username:email,
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
				  username:email,
				  password:password,
			      email:email,					  
				  course:courses,
				  group:groups,
				  address:address};		 
		}
		$.post(url, query).done(function(data) {
			$("#signup_content").html(data);
	}) 
	        
		    return false;
	});		
	     
})

/*******************************************************************************
 * 
 * Code related to signing process
 * 
 ******************************************************************************/
$(document).ready(function() {

$("#user_type").on('change', function() {	
	var user_type=$("#user_type").val();
	console.log(user_type);
	if (user_type==1) {
	$("#tr_code").show();
	} else {
		$("#tr_code").hide();
	}	
	
})

$('#loginform').on('change', function() {	
	$("#email_err").html('');
	$("#pwd_err").html('');
	$("#user_err").html('');
	$("#code_err").html('');	
});
 
$('#loginform').on('submit', function() {
	var user_type=$("#user_type").val();
	console.log('User type: '+user_type);	
	var email = $('#username').val();
	console.log('Email: '+email);
	var password = $('#password').val();
	console.log('Password:' +password);
	var code = $('#code').val();
	console.log('Code: '+code);
	
	
	if (email.length==0 ) {
		$("#email_err").html('Please provide email');
		return false
	}
	
	if (password.length==0 ) {
		$("#pwd_err").html('Please provide password');
		return false
	}
	
	if (user_type==0) {
		$("#user_err").html('Please select user type');
		return false 
	}
		else { 
	
  	if (user_type==1) {
	if (code.length==0 ) {
		$("#code_err").html('Please provide enrollment key');
		return false
	}	
  }	
}
	
	
	var query= {user_type:user_type,			
			  password:password,
		      username:email,					  
			  code:code };
	
	console.log(query);
	
	$.post('login_verify.php', query).done(function(data) {				
		var user_data=$.parseJSON(data);
		console.log(user_data);
		 if (user_data.code==0) {
				$("#code_err").html('Invalid enrollment code');
				return false
			}
    });	
	
	

	return true;
	
})
})
