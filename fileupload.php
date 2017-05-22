<?php
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "php" && $imageFileType != "html" && $imageFileType != "js") 
 {
    echo "Sorry, only php,html & javascript files are allowed.Zip files can be uploaded in the next tab.";
    $uploadOk = 0;
 }

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) 
{
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} 
else 
{
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) 
    {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        echo "<br>";
        //echo "Please wait while your file is being scanned.";
        echo "<br>";
        echo "<br>";
        echo "<b><u>Your file contents:</u></b>";
        echo "<br>";
        //for($i=0;$i<5;$i++);
        // this is for scan of the code for sql injection
        $DOSreg = "/(&quot;)(.*)[(][\[][a]\-[z][\]][)][+*](.*)(&quot;)|(&quot;)(.*)[(][\[]([a]\-[z][A]\-[Z][0]\-[9][\]][+*])[)](.*)(&quot;)|(&quot;)(.*)[\[][0]\-[9][\]][+*](.*)(&quot;)/i";
        $doshtml = array(array());
        if ($imageFileType == "html")
        {
            $f = fopen($target_file,"r") or die("Couldn't open $target_file");
            $l = 0;
            //Ex: hide.src = "http://localhost/Web2.0/LAB/lab1/info.php?value="+x.value
            $tf = str_replace('/','\\',$target_file);
            
            $content = file($tf);
            $numLines = count($content);
            $xhtmlsql = array(array());
            $xsshtml = array(array());
            // process each line
            $s1 = "/(&quot;)(.*)\?(.*)=(.*)(&quot;)[ ]*[+](.*)/i";
            $xssreghtml = "/(&quot;)(http|https)(.*)\?(.*)=(&quot;)[ +](.*)/i";
            $xssreghtml2 = "/document[.]write(.*)[+](.*)/i"; 
            // example :<script>document.write(variable)</script> . Here variabble can hold any malicious script which can be written on to screen
            $j = 0;
            $k = 0;
            for ($i = 0; $i < $numLines; $i++) 
            {
                // use trim to remove the carriage return and/or line feed character 
                // at the end of line 
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);

                if(preg_match($s1,$s))
                {
                    $xhtmlsql[$j][0] = ($i+1);
                    $xhtmlsql[$j][1] = $s;
                    $xhtmlsql[$j][2] = "SQL INJECTION";
                    $xhtmlsql[$j][3] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
                if(preg_match($xssreghtml,$s) or preg_match($xssreghtml2,$s))
                {
                    $xsshtml[$k][0] = ($i+1);
                    $xsshtml[$k][1] = $s;
                    $xsshtml[$k][2] = "XSS";
                    $xsshtml[$k][3] = "HIGH";
                    $k = $k + 1;
                }
                if(preg_match($DOSreg,$s))
                {
                    $doshtml[$l][0] = ($i+1);
                    $doshtml[$l][1] = $s;
                    $doshtml[$l][2] = "DOS";
                    $doshtml[$l][3] = "HIGH";
                    $l = $l + 1;
                }   
            }
        }
        $j = 0; 
        $k = 0;
        $l = 0;
        $dosphp = array(array());
        if ($imageFileType == "php")
        {
            $xphpsql = array(array());
            $f = fopen($target_file,"r") or die("Couldn't open $target_file");
            $tf = str_replace('/','\\',$target_file);
            $flag = 0;
            $content = file($tf);
            $numLines = count($content);
            //var_dump($numLines);
            // process each line
        $s1 = "/&quot;(SELECT|ALTER|DROP|DELETE|INSERT|UPDATE)(.*)where(.*)[=](IN|NOT IN)*(.*)[$](.*)(AND|OR)*(.*)&quot;/i";
         //Ex: &quot;SELECT * from user where Username=\\&quot;$name\\&quot; AND Password=\\&quot;$pwd\\&quot;&quot;
            $sdbsel = "/mysqli_connect()/i"; 
            $sdbmongo = "/MongoClient()/i";
            $s2 = "/(.*)&quot;(.*)[=][>][ ]*([$][_]GET(.*)|[$](.*))/i";
            $sdcass = "/Cassandra[:][:]/i";
            $xssreg = "/require[ ]*[(][$](.*)&quot;(.*)&quot;/i";
            $xssreg2 = "/location[.]redirect(.*)[$](.*)/i";
            for ($i = 0; $i < $numLines; $i++) 
            {
                // use trim to remove the carriage return and/or line feed character 
                // at the end of line 
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($sdbsel,$s))
                {
                    $flag = 1;
                    break;
                }
                if(preg_match($sdbmongo, $s))
                {
                    $flag = 2;
                    break;
                }
                if(preg_match($sdcass, $s))
                {
                    $flag = 3;
                    break;
                }

            }
            if($flag==1)
          {
            //var_dump($flag);
           
            for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($s1,$s))
                {
                    $xphpsql[$j][0] = ($i+1);
                    $xphpsql[$j][1] = $s;
                    $xphpsql[$j][2] = "MYSQL";
                    $xphpsql[$j][3] = "SQL INJECTION";
                    $xphpsql[$j][4] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
   
            }
          }
          if($flag == 2)
          {
            
           
            for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($s2,$s))
                {
                    $xphpsql[$j][0] = ($i+1);
                    $xphpsql[$j][1] = $s;
                    $xphpsql[$j][2] = "MongoDB";
                    $xphpsql[$j][3] = "SQL INJECTION";
                    $xphpsql[$j][4] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
   
            }
          }
          if($flag == 3)
          {
            
            
            for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($s1,$s))
                {
                    $xphpsql[$j][0] = ($i+1);
                    $xphpsql[$j][1] = $s;
                    $xphpsql[$j][2] = "Cassandra";
                    $xphpsql[$j][3] = "SQL INJECTION";
                    $xphpsql[$j][4] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
   
            }
          }

          $xssphp = array(array());
            for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($xssreg,$s) or preg_match($xssreg2, $s))
                {
                    $xssphp[$k][0] = ($i+1);
                    $xssphp[$k][1] = $s;
                    $xssphp[$k][2] = "-";
                    $xssphp[$k][3] = "XSS";
                    $xssphp[$k][4] = "HIGH";
                    $k = $k + 1;
                }
   
            }
             $dosphp = array(array());
            for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($DOSreg,$s))
                {
                    $dosphp[$l][0] = ($i+1);
                    $dosphp[$l][1] = $s;
                    $dosphp[$l][2] = "-";
                    $dosphp[$l][3] = "DOS";
                    $dosphp[$l][4] = "HIGH";
                    $l = $l + 1;
                }
   
            }
        }
        
    } 
    else 
    {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
<html>
<head>
<title>Cyber SECURITY</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="assets/css/main.css" />
<style>
/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top:-300px; opacity:0} 
    to {top:0; opacity:1}
}

@keyframes animatetop {
    from {top:-300px; opacity:0}
    to {top:0; opacity:1}
}

/* The Close Button */
.close {
    color: white;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-header {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}

.modal-body {padding: 2px 16px;}

.modal-footer {
    padding: 2px 16px;
    background-color: #5cb85c;
    color: white;
}
body
{
    color:white;
    font-weight: bold;
}
table {
    border-collapse: collapse;
    width: 100%;

}

th, td {
    text-align: left;
    padding: 8px;
    color:black;
    font-weight: bold;
}

tr {background-color: #f2f2f2}

th {
    background-color: #4CAF50;
    color: white;
}
.box h3{
    text-align:center;
    position:relative;
    top:10px;
    color: white;
}
.box p,ul,li{
     position:relative;
     text-align:justify;
     color: black;
}
.box h4{
      position:relative;
     text-align:justify;
     color: white;
}
.box {
    width:50%;
    border-radius:20%;
    height:300px;
    background-image: url(images/parcherr1.jpg);
    background-size: 100%;
    margin:40px auto;
}
.box:hover
{
         border-radius:0%;
        -webkit-transform: scale(1.1);
        -ms-transform: scale(1.1);
        transform: scale(1.1);
}
</style>
</head>
<body style="background-image: url(images/bgg2.jpg);">
<!-- Header -->
            <header id="header" class="alt">
                <div class="logo"><a href="index.html">Cyber <span style="color:red;font-weight: bold;"><i>SECURITY</i></span></a></div>
                <a href="Scanner.php" style="color:white;font-weight: bold;">Go back</a>
            </header>
<h1 id="prefect"></h1>
<div style="display: none;" id="err">
<div id="sqlhtml">
<div  style="overflow-x:auto;">
<table id="sqltable" border="3">
<tr>
<th>Line No.</th>
<th id="headerrow" style="display: none">Database</th>
<th>Line</th>
<th>Vulnerability</th>
<th>Priority</th>
</tr>
</table>
</div>
<div class="box effect2">
<h3><u>ERROR DETAILS :</u></h3>
<br>
<ul>
<li><h4><a href="#" id="SQLclick" style="color: #eaaec4;"><u> SQL injection</u> </a> Error Details</h3></li>
<li><h4><a href="#" style="display: none;" id="Xssclick"> <span style="color: #eaaec4;"><u>XSS</u> </span>Error Details </a></h4></li> 
<li><h4><a href="#" style="display: none;" id="dosclick"> <span style="color: #eaaec4;"><u>DOS</u> </span>Error Details </a></h4></li> 
</ul>
</div>
</div>
<div style="display: none;background-image: url(images/parcherr.jpg)" id="sug" class="box effect2">
<h3><u>SUGESSTIONS</u></h3>
<br>
<ul>
<li><h4><a href="#" id="SQLsugclick" style="color: blue;"><u> SQL injection </u></a> Defense Measaures</h4></li>
<li><h4><a href="#" style="display: none;" id="Xsssugclick"> <span style="color: blue;"><u>XSS</u> </span>Defense Measaures </a></h4></li>
<li><h4><a href="#" style="display: none;" id="dossugclick"> <span style="color: blue;"><u>DOS</u> </span>Defense Measaures </a></h4></li>  
</ul>
</div>
</div>
<!-- The Modal -->
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2 id="contentheader"></h2>
    </div>
    <div class="modal-body">
      <p id="modalmain"></p>
    </div>
    <div class="modal-footer">
      <h3  id="contentfooter"></h3>
    </div>
  </div>

</div>
<!-- Modal code end-->
<script type="text/javascript">
var flag = 0;
var xsqlhtm =<?php echo json_encode($xhtmlsql)?>;
var htmlxss = <?php echo json_encode($xsshtml)?>;
var htmldos = <?php echo json_encode($doshtml)?>;
//alert(typeof(xsqlhtm[0][0]))
if(xsqlhtm.length>0 && typeof(xsqlhtm[0][0]) != "undefined")
{

    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
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
         cell3.innerHTML = xsqlhtm[i][2] ;
         cell4.innerHTML = xsqlhtm[i][3];
         cell2.style.color = "RED";
         cell3.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";
    }
    flag = 1;

}
if(htmlxss.length>0 && typeof(htmlxss[0][0]) != "undefined")
{

    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
    document.getElementById("Xssclick").style.display = "block";
    document.getElementById("Xsssugclick").style.display = "block";
    for(var i=0;i<htmlxss.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0);
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         cell1.innerHTML = htmlxss[i][0];
         cell2.innerHTML = htmlxss[i][1];
         cell3.innerHTML = htmlxss[i][2] ;
         cell4.innerHTML = htmlxss[i][3];
         cell2.style.color = "RED";
         cell3.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";

    }
    flag = 1;
}
if(htmldos.length>0 && typeof(htmldos[0][0]) != "undefined")
{

    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
    document.getElementById("dosclick").style.display = "block";
    document.getElementById("dossugclick").style.display = "block";
    for(var i=0;i<htmldos.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0);
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         cell1.innerHTML = htmldos[i][0];
         cell2.innerHTML = htmldos[i][1];
         cell3.innerHTML = htmldos[i][2] ;
         cell4.innerHTML = htmldos[i][3];
         cell2.style.color = "RED";
         cell3.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";

    }
    flag = 1;
}
else if(flag == 0)
{
    document.getElementById("sqltable").style.display = "none";
    document.getElementById("prefect").innerHTML = "GOOD JOB!! YOUR CODE IS VULNERABILITY FREE!!!";
    document.getElementById("prefect").style.color = "WHITE";
}

</script>
<script type="text/javascript">
var flag = 0;
var xsqlphp =<?php echo json_encode($xphpsql)?>;
var phpxss = <?php echo json_encode($xssphp)?>;
var phpdos = <?php echo json_encode($dosphp)?>;
if(xsqlphp.length>0 && typeof(xsqlphp[0][0]) != "undefined")
{  
    var t = document.getElementById("headerrow");
    t.style.display = "table-cell";
    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
    for(var i=0;i<xsqlphp.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0);
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         var cell5 = row.insertCell(4);
         cell1.innerHTML = xsqlphp[i][0];
         cell2.innerHTML = xsqlphp[i][2];
         cell3.innerHTML = xsqlphp[i][1];
         cell4.innerHTML = xsqlphp[i][3];
         cell5.innerHTML = xsqlphp[i][4];
         cell3.style.color = "RED";
         cell4.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";
    }
    flag = 1;
}
if(phpxss.length>0 && typeof(phpxss[0][0]) != "undefined")
{  
    var t = document.getElementById("headerrow");
    t.style.display = "table-cell";
    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
    document.getElementById("Xssclick").style.display = "block";
    document.getElementById("Xsssugclick").style.display = "block";
    for(var i=0;i<phpxss.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0);
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         var cell5 = row.insertCell(4);
         cell1.innerHTML = phpxss[i][0];
         cell2.innerHTML = phpxss[i][2];
         cell3.innerHTML = phpxss[i][1];
         cell4.innerHTML = phpxss[i][3];
         cell5.innerHTML = phpxss[i][4];
         cell3.style.color = "RED";
         cell4.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";
         
    }
    flag = 1;
}
if(phpdos.length>0 && typeof(phpdos[0][0]) != "undefined")
{  
    var t = document.getElementById("headerrow");
    t.style.display = "table-cell";
    document.getElementById("sug").style.display = "block";
    document.getElementById("err").style.display = "block";
    document.getElementById("dosclick").style.display = "block";
    document.getElementById("dossugclick").style.display = "block";
    for(var i=0;i<phpdos.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0);
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         var cell5 = row.insertCell(4);
         cell1.innerHTML = phpdos[i][0];
         cell2.innerHTML = phpdos[i][2];
         cell3.innerHTML = phpdos[i][1];
         cell4.innerHTML = phpdos[i][3];
         cell5.innerHTML = phpdos[i][4];
         cell3.style.color = "RED";
         cell4.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";
         
    }
    flag = 1;
}
else if(flag == 0)
{
    document.getElementById("sqltable").style.display = "none";
    document.getElementById("prefect").innerHTML = "GOOD JOB!! YOUR CODE IS VULNERABILITY FREE!!!";
    document.getElementById("prefect").style.color = "WHITE";
}
</script>
<script>
//Modal for sql injection error display
var modal = document.getElementById('myModal');
var btn = document.getElementById("SQLclick");
var span = document.getElementsByClassName("close")[0];
 
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalmain").innerHTML = "<ul style='text-align:justify;'>\
<li> Sql injection means like introducing a wrong agent into the system similar to a injection given to a patient will affect \
      him and make him reveal some information about what is experienced.</li>\
<li> The script will work normally when the username doesn't contain any malicious characters.\ In other words, when submitting a non-malicious username (steve) the query becomes:</li>\
<p> * <span style='color:red;font-weight: bold;'>$query = 'SELECT * FROM users WHERE username = 'steve''; </span> \ However, a malicious SQL injection query will result in the following attempt: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\
      <span style='color:red;font-weight: bold;''>$query = 'SELECT * FROM users WHERE username = '' or '1=1'';</span>\ As the 'or' condition is always true, the mysql_query function returns records from the database</p>\
<li> Same in your code too if a '1=1' or 'not 1' such inputs are entered by user then all rows of database gets selected or   \
      except the one with id 1 will not be selected rest gets selected.</li>\
<li>4. In either of the cases your query will reveal of the internal database structure and values stored.</li>\
<p>This link helps have a better understanding of details mentioned above: <a href='http://www.programmerinterview.com/index.php/database-sql/sql-injection-example/' style='color: blue;font-weight: bold;'>SQL INJECTION EXAMPLE</a>\
</p>\
</ul>";
document.getElementById("contentheader").innerHTML = "SQL INJECTION";
}
// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<script>
//Modal for sql injection defense display
var modal = document.getElementById('myModal');
var btn = document.getElementById("SQLsugclick");
var span = document.getElementsByClassName("close")[0];
 
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalmain").innerHTML = "<ul>\
<li>Avoid connecting to the database as a superuser or as the database owner. Always use customized database users with the bare minimum required privileges required to perform the assigned task.</li>\
<li>If the <span style='color:red;font-weight: bold;'>PHP magic_quotes_gpc function is on</span>, then all the POST, GET, COOKIE data is escaped automatically.</li>\
<li>PHP has two functions for MySQL that sanitize user input: \
    <ol>\
    <li>add slashes (an older approach) and </li>\
    <li>mysql_real_escape_string (the recommended method). </li>\
    </ol>\
</li>\
<li>This function comes from PHP >= 4.3.0, so you should check first if this function exists and that you're running the latest version of PHP 4 or 5. MySQL_real_escape_string prepends backslashes to the following characters: '\\x00' , '\\n', '\\r', '\\', ', 'and '\\x1a'.\
</li>\
<li>PREPARED STATEMENTS: The reason that prepared statements help so much in preventing SQL injection is because of the fact that the values that will be inserted into a SQL query are sent to the SQL server after the actual query is sent to the server. In other words, the data input by a potential hacker is sent separately from the prepared query statement. This means that there is absolutely no way that the data input by a hacker can be interpreted as SQL, and there’s no way that the hacker could run his own SQL on your application. Any input that comes in is only interpreted as data, and can not be interpreted as part of your own application’s SQL code – which is exactly why prepared statements prevent SQL injection attacks.</li>\
</ul>\
<p style='color:black'>For more information on the solutions you can refer to the <a href='https://tools.ietf.org/html/rfc7481' style='color:blue;'>RFC : 7481</a> OR this link is also helpful <a href='http://www.programmerinterview.com/index.php/database-sql/example-of-prepared-statements-and-sql-injection-prevention/' style='color:blue;'> Prepared Statements Examples to prevent Sql injection</a>'";
document.getElementById("contentheader").innerHTML = "SQL INJECTION";
}
// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

</script>
<script>
//modal for XSS attack error details display
var modal = document.getElementById('myModal');
var btn = document.getElementById("Xssclick");
var span = document.getElementsByClassName("close")[0]; 
btn.onclick = function() {
    modal.style.display = "block";
        document.getElementById("modalmain").innerHTML = "<ul>\
    <li>Cross Site Scripting is generally made possible where the user's input is displayed. The following are the popular targets: \
    <ol>\
    <li>On a search engine that returns 'n' matches found for your '$_search' keyword. </li>\
    <li>Within discussion forums that allow script tags, which can lead to a permanent XSS bug. </li>\
    <li>On login pages that return an error message for an incorrect login along with the login entered.</li>\
    </ol>\
</li>\
<li>Here is a sample piece of code which is vulnerable to XSS attack:</li>\
<li style='color:red'>(form action='search.php' method='GET') Welcome!!  - Enter your name: (input type='text' name='name_1' ) (input type='submit' value='Go'  (/form) (?php 'echo (p)Your Name '; 'echo ($_GET[name_1]);' ?)</li>\
<li>In this example, the value passed to the variable 'name_1' is not sanitized before echoing it back to the user. This can be exploited to execute any arbitrary script.</li>\
<li> Here is some example exploit code:</li>\
<li style='color:red'>'http://victim_site/clean.php?name_1= (script) code\
 (script) or http://victim_site/ \
clean.php?name_1=(script) alert(document.cookie); (script)'</li>\
</ul>\
<p style='color:black'>For more information on error patterns refer : \
<a href='https://www.symantec.com/connect/articles/five-common-web-application-vulnerabilities' style='color:blue'>XSS Error patterns</a></p>";
document.getElementById("contentheader").innerHTML = "XSS Attack";
}

// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<script>
//modal for XSS attack Suggestion details display
var modal = document.getElementById('myModal');
var btn = document.getElementById("Xsssugclick");
var span = document.getElementsByClassName("close")[0]; 
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalmain").innerHTML = "<ul>\
    <li>The Example code given in error details can be edited in the following manner to avoid XSS attacks: </li>\
    <li style='color:green'>(?php $html= htmlentities($_GET['name_1'],ENT_QUOTES, 'UTF-8'); echo '<p>Your Name<br />'; echo ($html); ?) </li>\
<li>Never Insert Untrusted Data Except in Allowed Locations: Most importantly, never accept actual JavaScript code from an untrusted source and then run it. For example, a parameter named <span style='font-weight:bold'>'callback'</span> that contains a JavaScript code snippet. No amount of escaping can fix that.</li>\
<li>HTML Escape Before Inserting Untrusted Data into HTML Element Content:Escape the following characters with HTML entity encoding to prevent switching into any execution context, such as script, style, or event handlers. Using hex entities is recommended in the spec. In addition to the 5 characters significant in XML (&, <, >, ', '), the forward slash is included as it helps to end an HTML entity.\
    <span style='color:red'>Example : String safe = ESAPI.encoder().encodeForHTML( request.getParameter( 'input' ) );</span>\
</li>\
<li>Attribute Escape Before Inserting Untrusted Data into HTML Common Attributes :Except for alphanumeric characters, escape all characters with ASCII values less than 256 with the &#xHH; format (or a named entity if available) to prevent switching out of the attribute. The reason this rule is so broad is that developers frequently leave attributes unquoted. Properly quoted attributes can only be escaped with the corresponding quote. Unquoted attributes can be broken out of with many characters, including [space] % * + , - / ; < = > ^ and .\
    <span style='color:red'>Example : String safe = ESAPI.encoder().encodeForHTMLAttribute( request.getParameter( 'input' ) );</span>\
 </li>\
<li>JavaScript Escape Before Inserting Untrusted Data into JavaScript Data Values</li>\
<li>CSS Escape And Strictly Validate Before Inserting Untrusted Data into HTML Style Property Values : href='http://www.somesite.com?test=...ESCAPE UNTRUSTED DATA BEFORE PUTTING HERE...' link \
    <span style='color:red'> Example : String safe = ESAPI.encoder().encodeForURL( request.getParameter( 'input' ) );</span>\
   </li>\
</ul>\
<p style='color:black'>For more information on the solutions you can refer to this link <a href='https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet' style='color:blue'> XSS Complete Package</a>'";
document.getElementById("contentheader").innerHTML = "XSS Attack";
}

// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
<script>
//modal for DOS attack Error details display
var modal = document.getElementById('myModal');
var btn = document.getElementById("dosclick");
var span = document.getElementsByClassName("close")[0]; 
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalmain").innerHTML = "<p style='color:black'>Examples of Evil Patterns:\
<ul>\
<li>(a+)+</li>\
<li>([a-zA-Z]+)*</li>\
<li>(a|aa)+</li>\
<li>(a|a?)+</li>\
<li>(.*a){x} | for x > 10</li></ul></p>\
<p style='color:black'>The attacker might use the above knowledge to look for applications that use Regular Expressions, containing an Evil Regex, and send a well-crafted input, that will hang the system. Alternatively, if a Regex itself is affected by a user input, the attacker can inject an Evil Regex, and make the system vulnerable.</p>\
<p style='color:black'>For more information on the solutions you can refer to the <a href='https://tools.ietf.org/html/rfc7481' style='color:blue;'>RFC : 4732 also\
<a href='https://tools.ietf.org/html/rfc7481' style='color:blue;'> RFC : 2827 ";
document.getElementById("contentheader").innerHTML = "DOS Attack";
}

// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
//modal for DOS attack Defense details display
var modal = document.getElementById('myModal');
var btn = document.getElementById("dossugclick");
var span = document.getElementsByClassName("close")[0]; 
btn.onclick = function() {
    modal.style.display = "block";
    document.getElementById("modalmain").innerHTML = "<p style='color:black'>This doesnt have much solutions figured yet.Some which have been implemented are :\
    <ul>\
    <li>In PHP for input data validation we may use e.g. preg_match() function: E.g. When we expect digits as an input, then we should perform accurate input data validation <span style='color:red'>(?php\
  $clean = array();\
  if (preg_match('/^[0-9]+:[X-Z]+$/D', $_GET['var'])) {\
     $clean['var'] = $_GET['var'];\
  }\
?)</li>\
    <li>For special attention deserves modifier '/D', which additionally protects against HTTP Response Splitting type of attacks.</li>\
    <li>Avoid using of environment variables if the attacker may alter their values.</li></ul></p>";
document.getElementById("contentheader").innerHTML = "DOS Attack";
}

// When the user clicks close the modal
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
</body>
</html>

