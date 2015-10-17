$("#a_student").click(function() {
	console.log("Student signup clicked");
});

$("#a_tutor").click(function() {
	console.log("Tutor signup clicked");
});

function getSignupForm(user) {

	$.post("signup_form.php", {
		user : user
	}).done(function(data) {
		$("#signup_content").html(data);
	});
}