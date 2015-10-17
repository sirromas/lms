
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

});
