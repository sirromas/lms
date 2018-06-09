var _counter = 0;
var _request;
var fiftyone_degrees_update_location = "51Degrees_Update.php";

function fiftyone_degrees_on_abort() {
	document.getElementById('update_message').innerHTML = "Update Aborted";
}

function fiftyone_degrees_on_progress() {
	var messages = _request.responseText.replace(/^\s+|\s+$/g,"").split("\r\n");
	if (messages) {
		var ctrl = document.getElementById('update_message');
		if (messages.length > _counter) {
			var html = '';
			for(var i = _counter; i < messages.length; i++) {
				html += messages[i] + '<br/>';
			}
			ctrl.innerHTML += html;
			_counter = messages.length;
		}
	}
}

function fiftyone_degrees_start_updates()
{
    var ctrl = document.getElementById('update_message');
    if (ctrl != null) {
        ctrl.innerHTML = '';
    }
	try
	{
		// Will only work for IE. Used to access partial
		// HTTP responses.
		_request = new XDomainRequest();
	}
	catch (e) {
		// Will come here for all other browsers, and use
		// XmlHttpRequest which will support partial response
		// in the progress event.
		_request = new XMLHttpRequest();
		_request.onabort = fiftyone_degrees_on_abort;
	}
	_request.onprogress = fiftyone_degrees_on_progress;
	_request.open("GET", fiftyone_degrees_update_location, true);
	_request.send(null);
}