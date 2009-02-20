function addLoadListener(fn)
{
	if (typeof window.addEventListener != 'undefined')
	{
		window.addEventListener('load',fn,false);
	}
	else if (typeof document.addEventListener != 'undefined')
	{
		document.addEventListener('load',fn,false);
	}
	else if (typeof window.attachEvent != 'undefined')
	{
		window.attachEvent('onload',fn);
	}
	else
	{
		var oldfn = window.onload;
		if(typeof window.onload != 'function')
		{
			window.onload = fn;
		}
		else
		{
			window.onload = function()
			{
				oldfn();
				fn();
			}
		}
	}
}
function validiateBookingForm()
{
	var form = this;
	var reason_box = form['reason'];
	
	// clear out the old errors
	var errorDiv = document.getElementById('errorMsg1');
	while (errorDiv.childNodes.length > 0)
	{
		errorDiv.removeChild(errorDiv.firstChild);
	}
	
	if (typeof reason_box != 'undefined')
	{
		// if reasonbox is active
		var reasonBoxValid = reason_box.value != null && reason_box.value != '' && reason_box.value >= 0;
	}
	else
	{
		// if reasonBox is not in the current page
		var reasonBoxValid = true;
	}
	
	var theclass = form['class'];
	var classValid = true;
	if (theclass.value == '')
	{
		addErrorMsg('Need to select class.');
		classValid = false;
	}
	var subject = form['subject'];
	var subjectValid = true;
	if (subject.value == '')
	{
		addErrorMsg('Need to select subject.');
		subjectValid = false;
	}
	
	if (!reasonBoxValid)
	{
		addErrorMsg('Need to select reason for this booking.');
	}
	
	var teacherSelection = form['teacher'];
	var bookingFormPerm = form['perm'];
	var adminValid = false;
	if (teacherSelection != null)
	{
		if(bookingFormPerm.checked == true)
		{
			if (teacherSelection.value == '')
			{
				// teacher box is on default selection
				addErrorMsg('Need to select teacher.');
			}
			else
			{
				// teacher box has had a teacher selected
				adminValid = true;
			}
		}
		else
		{
			// always true as no need for teacher to be selected if not a permanent booking
			adminValid = true;
		}
	}
	else
	{
		// if there is no teacher selection box, then user is not an administrator so always valid
		adminValid = true;
	}
	
	if (reasonBoxValid && adminValid && classValid && subjectValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function addErrorMsg(text)
{
	var errorDiv = document.getElementById('errorMsg1');

	if (errorDiv.childNodes.length == 0)
	{
		var errorIntro = document.createTextNode("Error(s) encountered: ");
		var errorIntroContain = document.createElement("span");
		errorIntroContain.appendChild(errorIntro);
		errorDiv.appendChild(errorIntroContain);
	}
	
	var errorSpan = document.createElement("span");
	var errorMsg = document.createTextNode(text+' ');
	
	errorSpan.appendChild(errorMsg);
	
	errorDiv.appendChild(errorSpan);
}
function permanentChange()
{
	// user has clicked the permanent booking check box
	var form = document.forms['bookingForm'];
	var teacherSelection = form['teacher'];
	var permanentCheck = form['perm'];
	
	var checked = permanentCheck.checked;
	// if the checkbox is now checked then enable the teacher selection box
	if (checked)
	{
		teacherSelection.setAttribute("disabled","");
		teacherSelection.disabled = "";
	}
	else
	{
		// otherwise disable the teacher selection box
		teacherSelection.setAttribute("disabled","");
		teacherSelection.disabled = "true";
	}
	teacherSelection.value = '';
}
function bFValidStarter ()
{
	var form = document.forms['bookingForm'];

	if (typeof form != 'undefined')
	{
		form.onsubmit = validiateBookingForm;
		var permanentCheck = form['perm'];
		var teacherSelection = form['teacher'];
		if (typeof permanentCheck != 'undefined' & typeof teacherSelection != 'undefined')
		{
			// if permanent check box exists and teacher box exists
			teacherSelection.setAttribute("disabled","");
			teacherSelection.disabled = "true";
	
			permanentCheck.onclick = permanentChange;
		}
	}

}
addLoadListener(bFValidStarter);