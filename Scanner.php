<!DOCTYPE HTML>
<html>
	<head>
		<title>Cyber SECURITY</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
	<style>
body {font-family: "Lato", sans-serif;}

ul.tabulate {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Float the list items side by side */
ul.tabulate li {float: left;}

/* Style the links inside the list items */
ul.tabulate li a {
    display: inline-block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of links on hover */
ul.tabulate li a:hover {
    background-color: #4aa3df;
}

/* Create an active/current tablink class */
ul.tabulate li a:focus, .active {
    background-color: #db3467;
}

/* Style the tab content */
.tabulatecontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}
</style>
	</head>
	<body class="subpage">

		<!-- Header -->
			<header id="header">
				<div class="logo"><a href="index.html">Cyber <span>SECURITY</span></a></div>
				<a href="#menu">Menu</a>
			</header>

		<!-- Nav -->
			<nav id="menu">
				<ul class="links">
					<li><a href="index.html">Home</a></li>
					<li><a href="Scanner.php">Scanner</a></li>
					<li><a href="elements.php">PES repository</a></li>
				</ul>
			</nav>
		<!-- Banner -->
			<section id="banner" style="background-image:url('images/scanner.png');">
				<div class="inner">
					<header>
						<h1 style="font-family:algerian;"><span style="color:yellow;">SEC_RITY</span> is incomplete without <span style="color:yellow;"> 'U'</span></h1>
						<p>“In God we trust. All others, we virus scan.”<br/> Prevent network security and other leaks.Have a free vulnerability scan.</p>
					</header>
				</div>
			</section>
		<!-- Main -->
		    <div>
				<ul class="tabulate">
  					<li>
  					<a href="javascript:void(0)" class="tabulatelinks" onclick="openCity(event, 'singlefile')" id="defaultOpen">One file</a>
  					</li>
  					<li>
  					<a href="javascript:void(0)" class="tabulatelinks" onclick="openCity(event, 'Project')">Project(.zip)</a>
  					</li>
				</ul>

				<div id="singlefile" class="tabulatecontent">
  					<h3>One File</h3>
  					<p>If you want to check a single .php or .html or .js file please upload the file here.</p>
  					<form action="fileupload.php" method="post" enctype="multipart/form-data">
    					<span style="color:black;font-weight: bold;font-size: 15px">Select a file to Scan:</span>
    					<br/>
    					<br/>
    					<input type="file" name="fileToUpload" id="fileToUpload">
    					<br/>
    					<br/>
    					<input type="submit" value="SCAN File" name="submit">
					</form>
				</div>

				<div class="tabulatecontent" id="Project">
  					<h3>Entire Project</h3>
 				    <p>If you want to upload enitre project files please convert it to .zip folder and upload here.</p>
 				    <form action="extract.php" method="post" enctype="multipart/form-data">
    					<span style="color:black;font-weight:bold;font-size:15px;">Project Title:</span> 
    					<input type="text" name="project_title" id="project_title">
    					<br/>
    					<br/>
    					<span style="color:black;font-weight:bold;font-size:15px;">Description(ReadMe):</span><textarea rows="10" cols="10" name="Desc" id="Desc"></textarea>
    					<br/>
    					<br/>
    					<span style="color:black;font-weight:bold;font-size:15px;">Upload Date:</span> <input type="date" name="upload_date" value='<?php echo date("Y-m-d") ?>' readonly>
    					<br/>
    					<br/>
    					<span style="color:black;font-weight: bold;font-size: 15px">Select a zip file to Upload and Scan:</span>
    					<br/>
    					<br/>
    					<input type="file" name="fileToUpload" id="fileToUpload">
    					<br/>
    					<br/>
    					<input type="submit" value="Upload File" name="submit">
					</form>
				</div>
			</div>
			
		<!-- Footer -->
			<footer id="footer">
			   <div class="logo"><a href="index.html">Cyber <span>SECURITY</span></a></div>
				<div class="copyright">
					<ul class="icons">
						<li><a href="#" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
						<li><a href="#" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
						<li><a href="#" class="icon fa-instagram"><span class="label">Instagram</span></a></li>
						<li><a href="#" class="icon fa-snapchat"><span class="label">Snapchat</span></a></li>
					</ul>
						<p>&copy;Sushma S,Veerabhadran. All rights reserved. Design: <a href="">TEMPLATED</a>. Images: <a href="">C CureIt</a>.</p>
				</div>
			</footer>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script>
			function openCity(evt, cityName) 
			{
    			var i, tabcontent, tablinks;
    			tabcontent = document.getElementsByClassName("tabulatecontent");
    			for (i = 0; i < tabcontent.length; i++) 
    			{
        			tabcontent[i].style.display = "none";
   				}
    			tablinks = document.getElementsByClassName("tabulatelinks");
    			for (i = 0; i < tablinks.length; i++) 
    			{
        			tablinks[i].className = tablinks[i].className.replace(" active", "");
    			}
    			document.getElementById(cityName).style.display = "block";
    			evt.currentTarget.className += " active";
			}

				// Get the element with id="defaultOpen" and click on it
				document.getElementById("defaultOpen").click();
			</script>
	</body>
</html>