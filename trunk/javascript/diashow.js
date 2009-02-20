// JavaScript Document
<!-- 
/* #################################
Script developed by Richard Currie on 23rd August 2006.
Email: curry123@hotmail.com - prefered email : kurru@animeequation.com
Website - http://www.animeequation.com ->> Come Visit Us!
##################################### */
function ele(eleName) {
 if(document.getElementById && document.getElementById(eleName)) {
    return document.getElementById(eleName);
  }
  else if (document.all && document.all(eleName)) {
    return document.all(eleName);
  }
  else if (document.layers && document.layers[eleName]) {
    return document.layers[eleName];
  } else {
    return false;
  }
}

function showTags(fieldName, disclose) 
	{
	var longField = ele(fieldName + "_long");
	var shortField = ele(fieldName +"_short");
	var frameField = ele(fieldName +"_frame");
	
	var hor_width = 285;	// max hori width 
	var ver_height = 25;	// max vertical height
	var time_span = 600; 	// milliseconds
	
	
	var interval = 4;
	var hor_step = interval * hor_width / time_span;
	var ver_step = interval * ver_height / time_span;
	
// change speed of change with these values
	// vertical
/*	var maximum = 500; // max size
	var step = 5; // px size of steps to get to max size [multiples of maximum please]
	var interval = 4; // x milliseconds between steps
	// horizontal
	var maximumh = 500;
	var steph = 5;
	var intervalh = 4;
*/
	var intervalh = interval;
	var maximumh = hor_width;
	var steph = hor_step;
	var maximum = ver_height;
	var step = ver_step;
	
	if (disclose == "true")
		{
		longField.style.display = "inline";
		shortField.style.display = "none";

// CODE FOR EXPANDING PAGE vertically
		var size = 0;
		var timer = 0;
		while (size < maximum)
			{
			size = size + step;
			timer = timer + interval;
			window.setTimeout("changeheightbox('" + fieldName + "'," + size + ");",timer);
			};
		var sizeh = 0;
		var timerh = 0;
		while (sizeh < maximumh)
			{
			sizeh = sizeh + steph;
			timerh = timerh + intervalh;
			window.setTimeout("changewidthbox('" + fieldName + "'," + sizeh + ");",timerh);
			};

		}
	else
		{
		var size = maximum;
		var timer = 0;
// CODE FOR SHINKING PAGE vertically
		while (size > 0)
			{
			size = size - step;
			timer = timer + interval;
			window.setTimeout("changeheightbox('" + fieldName + "'," + size + ");",timer);
			};
		var sizeh = maximumh;
		var timerh = 0;
// CODE FOR SHINKING PAGE horizontally
		while (sizeh > 0)
			{
			sizeh = sizeh - steph;
			timerh = timerh + intervalh;
			window.setTimeout("changewidthbox('" + fieldName + "'," + sizeh + ");",timerh);
			};
		
// CODE FOR HIDING FORM ALL TOGETHER
		var timermax = 0;
		if (timerh > timer) {timermax = timer;}
		else {timermax = timerh;}
		window.setTimeout("hidefield('" + fieldName + "')",timermax);
		
		}
	}
function hidefield(fieldName)
	{
	var longField = ele(fieldName + "_long");
	var shortField = ele(fieldName +"_short");
	longField.style.display = "none";
	shortField.style.display = "inline";
	}
function changeheightbox (FieldName,size)
	{
	var frameField = ele(FieldName +"_frame");
	frameField.height = size + "px";
	}
function changewidthbox (FieldName,size)
	{
	var frameField = ele(FieldName +"_frame");
	frameField.width = size + "px";
	}

// -->