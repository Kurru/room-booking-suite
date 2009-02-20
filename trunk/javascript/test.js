// JavaScript Document
function addLoadListener(fn)
{
//	alert("hi");
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
		var olffn = window.onload;
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

//alert("hi");

function starter()
{
//var selectBox = document.forms["form1"]["classlist"];
var selectBox = document.getElementById('classlist');
selectBox.onchange = changeBox;
changeBox();
}

function changeBox ()
{
//alert ("box changed");
//var classList = document.forms["form1"]["classlist"];
var classList = document.getElementById('classlist');
var textBox = document.getElementById('classDATA');
textBox.value = classList.value; // [classList.selectedIndex]
}

addLoadListener(starter);

var x=0;
function changeTitle()
{
	x++;
	var title = document.getElementsByTagName('TITLE')[0];
	var textnode = document.createTextNode("random".x);
	title.appendChild(textnode);
}
function starting()
{
	var button = document.getElementById('button');
	//button.onmouseup = changeTitle;
	button.onmouseup = clicked;
}
addLoadListener(starting);

function clicked()
{
	var anchorText = document.createTextNode("monoceros");
	var spaceText = document.createTextNode(" ");
	var newAnchor = document.createElement("a");
	newAnchor.appendChild(anchorText);
	
	newAnchor.setAttribute("href","index.html");
	
	var parent = document.getElementById("starlinks");
	var newChild = parent.appendChild(newAnchor);
	parent.appendChild(spaceText);
}