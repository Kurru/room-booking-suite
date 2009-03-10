<?php
session_start();
include_once("../classes/database.php");
include_once("../classes/settings.php");
include_once("../general.php");
$page = $_GET['page'];
$section = $_GET['section'];
$confirmation = $_GET['confirmation'];
?>
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

function starter()
{
//var selectBox = document.forms["form1"]["classlist"];
var selectBox = document.getElementById('editList');
var form = document.getElementById('selectAndEdit');
if (selectBox != null && form != null)
{
	selectBox.onchange = changeBox;
	changeBox();
	
	form.onsubmit = validateEdit;
}
}

function changeBox ()
{
var editList = document.getElementById('editList');
var textBox = document.getElementById('oldDATA');
var textBoxEdit = document.getElementById('newDATA');
var hiddenID = document.getElementById('dataid');
if (typeof editList != 'undefined' && typeof textBox != 'undefined' && typeof textBoxEdit != 'undefined')
{
	if (DataList[editList.value] == null)
	{
		textBox.value = '';
		textBoxEdit.value = '';
		hiddenID.value = 'NULL';
	}
	else
	{
		textBox.value = DataList[editList.value];
		textBoxEdit.value = DataList[editList.value];
		hiddenID.value = editList.value;
	}
}
}

function validateEdit ()
{
	// user has submitted the form for processing, check the data fields for if deletion is about to occur
	var formElement = this;

	var fieldOld = document.getElementById('oldDATA');
	var fieldNew = document.getElementById('newDATA');
    
    var secret = document.getElementById('secret');

	if (fieldNew.value == '' && fieldOld.value != '' && secret == null)
	{
		var confirmation = confirm("Do you wish to delete this entry?");
		if (confirmation)
		{
			return true;
		}
	}
	else if (fieldNew.value == '' && fieldOld.value == '' || fieldNew.value == '')
	{
		alert("No data entered.");
	}
	else
	{
		return true;
	}
	return false;
}

addLoadListener(starter);


<?PHP
// ##########################################################################################
// this section has the code for the 'edit users level' section in the administration section
// ##########################################################################################
if ($page == 'Administration' && $section == 'edituserlevel')
{
?>
function initEvent()
{
	var username = document.getElementById('username');
	if (username != null)
	{
		username.onchange = changeForm;
	}
}
addLoadListener(initEvent);

function changeForm()
{
	var form = document.forms['editForm'];
	var username = form.elements['username'].value;
	if (username != 'null')
	{
		form.elements['level'].value = dataList[username];
	}
	else
	{
		form.elements['level'].value = '0';
	}
}
var dataList = new Array();
<?php
	$query = "SELECT * FROM bookingusers ORDER BY username";
	$users = database::executeQuery($query);
	foreach ($users as $index => $user)
	{
	echo "dataList[''+'".$user['username']."'] = '".$user['level']."';\n";
	}
} // end code for the 'edit users level' section in the administration section

// ###############################################################################
// this section has the code for the 'edit periods' section in the administration section
// ##########################################################################################
if ($page == 'Administration' && $section == 'editperiods' && $confirmation != true)
{
?>
var numberOfPeriods = <?php echo settings::get_numberOfPeriods(); ?>;
function checkSubmit()
{
	var error = new Array();
	
	var form = document.forms['editForm'];
	var elements = form.elements
	for (var i=0;i<=elements.length-1;i++)
	{
		var elementName = elements[i].nodeName;
		var elementFunction = elements[i].type;
		if (elementName.toLowerCase() == 'input' && elementFunction.toLowerCase() != "submit" && elementFunction.toLowerCase() != "button")
		{
        	var id = elements[i].id;
            var indexOFName = id.indexOf("name");
            if (indexOFName >= 0)
            	continue;
        
			var pattern = /^[0-2]?[0-9]\.[0-5][0-9]$/;
			
			if (pattern.test(elements[i].value))
			{
			}
			else
			{
				error[error.length] = elements[i];
			}
		}
	}
	var oldMsg = document.getElementById("errorMsg1");
	
	if (error.length > 0)
	{
		// make a new element and add it before the save button as an error tag
		if (oldMsg == null)
		{
			var errorSpan = document.createElement("span");
			var errorMsg = document.createTextNode("Fix the times above to save. ");
			
			errorSpan.appendChild(errorMsg);
			errorSpan.className = "errorMsg";
			errorSpan.id = "errorMsg1";
			
			var submitButton = document.getElementById('submitButton');
			if (submitButton != null)
			{
				var parent = submitButton.parentNode;
				parent.insertBefore(errorSpan,submitButton);
			}
		}
		return false;
	}
	else
	{
		if (oldMsg != null)
		{
			// remove the error message
			var parent = oldMsg.parentNode;
			parent.removeChild(oldMsg);
		}	
	}
	return true;
}
function checkField()
{
	var textField = this;
	var value = textField.value;
	var pattern = /^[0-2]?[0-9]\.[0-5][0-9]$/;
	if (pattern.test(value))
	{
		textField.setAttribute("class", "");
		textField.setAttribute("className", "");
		textField.className = "greenBorder";
	}
	else
	{
		textField.setAttribute("class", "");
		textField.setAttribute("className", "");
		textField.className = "redBorder";
	}
}
function colorFields()
{
	var form = document.getElementById('editForm')
	form.onsubmit = checkSubmit;
	
	var elements = form.elements
	for (var i=0;i<=elements.length-1;i++)
	{
		var elementName = elements[i].nodeName;
		var elementFunction = elements[i].type;
		if (elementName.toLowerCase() == 'input' && elementFunction.toLowerCase() != "submit" && elementFunction.toLowerCase() != "button")
		{
			var textField = elements[i];
			var value = textField.value;
            var id = elements[i].id;
            
            var indexofName = id.indexOf("name");
            if (indexofName >=0)
            	continue;
            
            
            textField.onchange = checkField;
			
			var pattern = /^[0-2]?[0-9]\.[0-5][0-9]$/;
			
			if (pattern.test(value))
			{
				textField.setAttribute("class", "");
				textField.setAttribute("className", "");
				textField.className = "greenBorder";
			}
			else
			{
				textField.setAttribute("class", "");
				textField.setAttribute("className", "");
				textField.className = "redBorder";
			}

		}
	}
}
addLoadListener(colorFields);

<?php
} // end code for the 'edit periods' section in the administration section

// ###############################################################################
// this section has the code for the 'edit settings' section in the administration section
// ##########################################################################################
if ($page == 'Administration' && $section == 'editsettings' && $confirmation != true)
{
?>

function checkSubmit()
{
	var error = new Array();
	
	var form = document.forms['editForm'];
	var elements = form.elements
	for (var i=0;i<=elements.length-1;i++)
	{
		var elementName = elements[i].nodeName;
		var elementFunction = elements[i].type;
		if (elementName.toLowerCase() == 'input' && elementFunction.toLowerCase() != "submit" && elementFunction.toLowerCase() != "button")
		{
			if (errorCheck(elements[i]))
			{
			}
			else
			{
				error[error.length] = elements[i];
			}
		}
	}
	var oldMsg = document.getElementById("errorMsg1");
//	alert (error.length);
	if (error.length > 0)
	{
		// make a new element and add it before the save button as an error tag
		if (oldMsg == null)
		{
			var errorSpan = document.createElement("span");
			var errorMsg = document.createTextNode("The error(s) above must be fixed before saving. ");
			
			errorSpan.appendChild(errorMsg);
			errorSpan.className = "errorMsg";
			errorSpan.id = "errorMsg1";
			
			var submitButton = document.getElementById('submitButton');
			if (submitButton != null)
			{
				var parent = submitButton.parentNode;
				parent.insertBefore(errorSpan,submitButton);
			}
		}
		return false;
	}
	else
	{
		if (oldMsg != null)
		{
			// remove the error message
			var parent = oldMsg.parentNode;
			parent.removeChild(oldMsg);
		}	
	}
	return true;
}

function errorCheck(element)
{
	var value = element.value;
	if (/(^| )checkText( |$)/.test(element.className))
	{
		var type = 'text';
	}
	else if (/(^| )checkInteger( |$)/.test(element.className))
	{
		var type = 'integer';
	}
	else
	{
		var type = '';
	}
	
	
	var pattern = '';
	if (type == 'time')
	{
		pattern = /^[0-2]?[0-9]\.[0-5][0-9]$/;
	}
	else if (type == 'text')
	{
		pattern =/^(\S)+( \S+)*$/;
	}
	else if (type == 'integer')
	{
		pattern = /^\d+$/;
	}
	else
	{
		pattern = /.*/;
	}
	
	if (pattern.test(value))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function colorFields()
{
	var form = document.getElementById('editForm')
	
	var elements = form.elements
	for (var i=0;i<=elements.length-1;i++)
	{
		var elementName = elements[i].nodeName;
		var elementFunction = elements[i].type;
		if ((elementName.toLowerCase() == 'input' || elementName.toLowerCase() == 'select') && elementFunction.toLowerCase() != "submit" && elementFunction.toLowerCase() != "button")
		{
			var textField = elements[i];
			var value = textField.value;
			textField.onchange = colorFields;
			
			if (errorCheck(elements[i]))
			{
				// change the formating back to the original
				var classData = textField.className;
				var classData = classData.replace(/greenBorder(\s)*/,"");  	// strip out any green border
				var classData = classData.replace(/redBorder(\s)*/,"");		// strip out any red border
				textField.setAttribute("class", "");
				textField.setAttribute("className", "");
				//textField.className = "greenBorder " + classData;
				textField.className = classData;
			}
			else
			{
				// change the formating back to the original
				var classData = textField.className;  	
				var classData = classData.replace(/greenBorder(\s)*/,"");	// strip out any green border
				var classData = classData.replace(/redBorder(\s)*/,"");		// strip out any red border
				textField.setAttribute("class", "");
				textField.setAttribute("className", "");

				textField.className = "redBorder " + classData;
			}

		}
	}
}
addLoadListener(function()
	{
		var form = document.getElementById('editForm')
		form.onsubmit = checkSubmit;
	}
);
addLoadListener(colorFields);

<?php
} // end code for the 'edit periods' section in the administration section
if ($page == 'Administration' && $section == 'editreasons' && $confirmation != true)
{
?>

function addNewReason ()
{
	var errorSpace = document.getElementById('errorSpace');
	
	// clear old errors
	while (errorSpace.firstChild != null)
	{
		errorSpace.removeChild(errorSpace.firstChild);
	}

	var finalRow = document.getElementById("new_row_5");
	if (finalRow.style.display == '')
	{
		// make error msg
		addErrorMsg("Save before adding more.");
	}
	else
	{
		var i = 1;
		var row = document.getElementById("new_row_"+i);
		while (row.style.display != 'none' && i < 5) 
		{
			i++;
			row = document.getElementById("new_row_"+i);
		}
		
		var j=i-1; // the last displayed row;new_{$i}_text
		var previousText = document.getElementById("new_"+j+"_text");
		
		row.style.display = '';
		
	}
}
function addErrorMsg(text)
{
	// get the error space
	var errorSpace = document.getElementById('errorSpace');
	while (errorSpace.firstChild != null)
	{
		errorSpace.removeChild(errorSpace.firstChild);
	}

	var newText = document.createTextNode(text);
	var span = document.createElement("span");
	
	span.appendChild(newText);
	
	errorSpace.appendChild(span);
}
function checkReasonSubmit()
{
	var form = this;
	var errorSpace = document.getElementById('errorSpace');
	
	// clear old errors
	while (errorSpace.firstChild != null)
	{
		errorSpace.removeChild(errorSpace.firstChild);
	}
	
	var elements = form.elements;
	for (var i=0;i <= elements.length-1;i++)
	{
		var element = elements[i];
		//alert(element.nodeName);
		if (element.nodeName == 'INPUT' && element.getAttribute("type") == 'text' && /^[0-9]+_text$/.test(element.getAttribute("name")) )
		{
			var name = element.getAttribute("name");
			//alert(name);
			var bits = name.split("_");
			var number = bits[0];
			
			if (/^\s*$/.test(element.value))
			{
				var backup = form[number+"_backup"];
				element.value = backup.value;
				
				addErrorMsg ("Reason requires text, value restored.");
				return false;
			}
		}
	}
	return true;
}
function bookingReasonsAssist ()
{
	var form = document.forms['editForm'];
	form.onsubmit = checkReasonSubmit;
	
	var addNewButton = document.getElementById('add_new');
	addNewButton.onclick = addNewReason;
	
	// hide all the new reason fields and set onchange action
	for (var i=1;i <= 5;i++)
	{ 
		var row = document.getElementById("new_row_"+i);
		row.style.display = 'none';
	}
	// empty the error space
	var errorSpace = document.getElementById('errorSpace');
	while (errorSpace.firstChild != null)
	{
		errorSpace.removeChild(errorSpace.firstChild);
	}
	
}
addLoadListener (bookingReasonsAssist);
<?php
} // end code for the 'edit booking reasons' section in the administration section
if ((empty($_SESSION['SESSIONlevel']) || $_SESSION['SESSIONlevel'] == 0) || raw_param('page') == 'Failed')
{
?>
function placeFocus() 
{
	if (document.forms.length > 0) 
	{
		var field = document.forms[0];
		for (i = 0; i < field.length; i++) 
		{
			if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea") || (field.elements[i].type.toString().charAt(0) == "s")) 
			{
				document.forms[0].elements[i].focus();
				break;
			}
		}
   	}
}
addLoadListener(placeFocus);
<?php
}
?>
