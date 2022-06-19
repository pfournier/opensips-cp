<script language="JavaScript">
  


function addWidget(grid, content, sizeX, sizeY, col = null, row = null) {
    var widgetElement = content;
    var widget = [widgetElement, sizeX, sizeY, col, row];
    gridster.add_widget.apply(gridster, widget);
}
function move(oldID, newID) {
    var newParent = document.getElementById(newID);
    var oldParent = document.getElementById(oldID);
	while (oldParent.childNodes.length > 0) {
		newParent.appendChild(oldParent.childNodes[0]);
	}
}

function store_dashboard(arg) {
  const Http = new XMLHttpRequest();
  const url = 'store_dashboard.php';
  Http.open("POST", url);
  Http.setRequestHeader('Content-type', 'application/json');
  Http.send(JSON.stringify(arg));
  Http.onreadystatechange =(e) => {
    //console.log(Http.responseText);
  } 
}

function getChartHtml() {
  const Http = new XMLHttpRequest();
  //const url = 'dashboard3.php';
  Http.open("GET", url);
  Http.setRequestHeader('Content-type', 'text/html');
  Http.send(null);
  Http.onreadystatechange =(e) => {
   // console.log(Http.responseText);
  }
}

function lockPanel() {
  if (gridster.drag_api.disabled) {
    gridster.enable();
	const menus = document.getElementsByClassName('dashboard_menu');
	for(const menu of menus) {
	  menu.style.display = 'block';
	}
  }
  else {
    gridster.disable();
	const menus = document.getElementsByClassName('dashboard_menu');
	for(const menu of menus) {
	  menu.style.display = 'none';
	}
  }
}


function show_widget(widget_path){
	url = "dashboard.import_details.php?widget_dir="+widget_path;
	
	var http = getHTTPObject();
	
	http.open("GET", url, false);
	http.onreadystatechange = handleHttpResponse(http);
	http.send(null);
	result = http.responseText;
	
	var body = document.body,
	  html = document.documentElement;
  
	var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
	var width = Math.max( body.scrollWidth, body.offsetWidth, html.clientWidth, html.scrollwidth, html.offsetwidth );
  
  
	document.getElementById('overlay').style.height = height;
	document.getElementById('overlay').style.width = width;
	document.getElementById('overlay').style.display = 'block';
	document.getElementById('widget_overlay').innerHTML = result;
	centerMe('widget_overlay')
	document.getElementById('overlay').onclick = function () {closeOverlay();};
	document.getElementById('widget_overlay').style.display = 'block';
	window.location.hash = '#tab1';
	location.hash = "tab1";
  
  }
  
  function centerMe(element) {
	//pass element name to be centered on screen
	  var pWidth = window.innerWidth;
	  var pTop =  window.scrollTop;
	  var eWidth = document.getElementById(element).style.width
	  var height = document.getElementById(element).style.height
	  document.getElementById(element).style.top = '150px';
	  //$(element).css('top',pTop+100+'px')
	  document.getElementById(element).style.left = parseInt((pWidth / 2) - 170) + 'px';
	}
	
	
	
	function closeOverlay() {
	  document.getElementById('overlay').style.display = 'none';
	  document.getElementById('widget_overlay').style.display = 'none';
	  document.getElementById('widget_overlay').innerHTML = '';
	  location.hash = "";
	}
	
	
  function handleHttpResponse(http) {
  
	if (http.readyState == 4) {
		  if(http.status==200) {
			  ok = true;
			  //return results;
		  }
  
	}
  
  }
  
  
  
  function getHTTPObject() {
  
	var request = false;
	try {
	request = new XMLHttpRequest();
	} catch (trymicrosoft) {
	try {
	request = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (othermicrosoft) {
	try {
	  request = new ActiveXObject("Microsoft.XMLHTTP");
	} catch (failed) {
	  request = false;
	}
	}
  }
  
  if (!request)
  alert("Error initializing XMLHttpRequest!");
  
  
  return request;
  }
    
</script>