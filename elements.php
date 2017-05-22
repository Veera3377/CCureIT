<!DOCTYPE HTML>
<html>
<?php
$conn=mysqli_connect('localhost','root','') or die(mysqli_error($conn));
 mysqli_select_db($conn,'security') or die(mysqli_error($conn));
 $query = mysqli_query($conn,'SELECT * FROM repository');
 $xdata = array(array());
 $a = 0;
 while($res = mysqli_fetch_array($query,1))
 {
 	$xdata[$a][0] = $res['Title'];
 	$xdata[$a][1] = $res['Description'];
 	$xdata[$a][2] = $res['UploadDate'];
 	$xdata[$a][3] = $res['Rating'];
 	$a++;
 }
?>
	<head>
		<title>Cyber SECURITY</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
			<style>
.tbstyle {
    border-collapse: collapse;
    width: 100%;
}
.tbstyle th, td {
    text-align: left;
    padding: 8px;
    font-weight: bold;
    color: black;
}
.tbstyle tr:nth-child(even){background-color: #f2f2f2}
.tbstyle th {
    background-color: #4CAF50;
    color: white;
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
			<section id="banner" style="background-image:url('images/repo.jpg');background-repeat: no-repeat;">
				<div class="inner">
					<header>
						<h1 style="font-family:algerian;">PES REPOSITORY</h1>
						<p><br/> </p>
					</header>
				</div>
			</section>
		<!-- Main -->
		    <div  style="overflow-x:auto;">
				<table id="sqltable" border="3" class="tbstyle">
				<tr>
				<th>TITLE</th>
				<th>DESCRIPTION</th>
				<th>UPLOAD DATE</th>
				<th>RATING</th>
				</tr>
				</table>
			</div>
		<!-- Footer -->
			<footer id="footer">
			   <div class="logo"><a href="index.html">Cyber <span>SECURITY</span></a></div>
				<div class="copyright" >
					<ul class="icons" >
						<li><a href="#" class="icon fa-twitter" style="color:black;"><span class="label">Twitter</span></a></li>
						<li><a href="#" class="icon fa-facebook" style="color:black;"><span class="label">Facebook</span></a></li>
						<li><a href="#" class="icon fa-instagram" style="color:black;"><span class="label">Instagram</span></a></li>
						<li><a href="#" class="icon fa-snapchat" style="color:black;"><span class="label">Snapchat</span></a></li>
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
			var xsqlhtm =<?php echo json_encode($xdata)?>;
			if(xsqlhtm.length>0 && typeof(xsqlhtm[0][0]) != "undefined")
			{
				for(var i=0;i<xsqlhtm.length;i++)
    			{
         			var t = document.getElementById("sqltable");
         			var rowCount = t.rows.length;
         			var row = t.insertRow(rowCount);
         			var cell1 = row.insertCell(0);
         			var cell2 = row.insertCell(1);
         			var cell3 = row.insertCell(2);
         			var cell4 = row.insertCell(3);
         			cell1.innerHTML = xsqlhtm[i][0];
         			cell2.innerHTML = xsqlhtm[i][1];
         			cell3.innerHTML = xsqlhtm[i][2];
         			cell4.innerHTML = xsqlhtm[i][3];
    			}
			}
			</script>
	</body>
</html>