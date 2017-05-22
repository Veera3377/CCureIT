<?php
$conn=mysqli_connect('localhost','root','') or die(mysqli_error($conn));
 mysqli_select_db($conn,'security') or die(mysqli_error($conn));

$title = $_POST['project_title'];
$date = $_POST['upload_date'];
$desc = $_POST['Desc'];
//echo "$date";
if($_FILES["fileToUpload"]["name"]) {
        $file = $_FILES["fileToUpload"];
    $filename = $file["name"];
    $tmp_name = $file["tmp_name"];
    $type = $file["type"];
      
    $name = explode(".", $filename);
    $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
  
    if(in_array($type,$accepted_types)) { //If it is Zipped/compressed File
        $okay = true;
    } 
      
    $continue = strtolower($name[1]) == 'zip' ? true : false; //Checking the file Extension
  
    if(!$continue) {
        $message = "The file you are trying to upload is not a .zip file. Please try again.";
    }
    
    
  /* here it is really happening */
        $ran = $name[0]."-".time()."-".rand(1,time());
        $targetdir = "zipped/".$ran;
        $targetzip = "zipped/".$ran.".zip";
  
    if(move_uploaded_file($tmp_name, $targetzip)) { //Uploading the Zip File
          
        /* Extracting Zip File */
  
        $zip = new ZipArchive();
        $x = $zip->open($targetzip);  // open the zip file to extract
        if ($x === true) {
            $zip->extractTo($targetdir); // place in the directory with same name  
            $zip->close();
      
            unlink($targetzip); //Deleting the Zipped file
        }
        $message = "Your <strong style='color:white'>{$name[0]}.zip</strong> file was uploaded and unpacked.";
  
    } 
    else {    
        $message = "There was a problem with the upload. Please try again.";
    }
}
echo $message;

// From here walkthrough through the directory starts..
$dir = scandir($targetdir);  // dir is 1d array which holds ., .. , actual folder
$td = $targetdir . '/' . $dir[2];
//has path /zipped/dir/actual folder
$dii = scandir($td);
//dii - 1d array that holds . , .. , filenmame1,filename2,filename3.....
//--array that contains inidividual file name.
$j=0; 
$xmixsql = array(array()); 
$dosreg = "/(&quot;)(.*)[(][\[][a]\-[z][\]][)][+*](.*)(&quot;)|(&quot;)(.*)[(][\[]([a]\-[z][A]\-[Z][0]\-[9][\]][+*])[)](.*)(&quot;)|(&quot;)(.*)[\[][0]\-[9][\]][+*](.*)(&quot;)/i"; 
for ($len=2;$len<count($dii);$len++)
{
    $value = $dii[$len];   // takes file by file
    //var_dump($value);
   $imageFileType = pathinfo($value,PATHINFO_EXTENSION);
    if($imageFileType == 'html')
    {
        $f = fopen($td.'\\'.$value,"r") or die("Couldn't open $target_file");
          $tf = $td.'\\'.$value;
            $tf = str_replace('/','\\',$tf);         
            $content = file($tf);
            $numLines = count($content);
            
            // process each line
            $s1 = "/(&quot;)(.*)\?(.*)=(.*)(&quot;)[ +](.*)/i";
            $xssreghtml = "/(&quot;)(http|https)(.*)\?(.*)=(&quot;)[ +](.*)/i";
            $xssreghtml2 = "/document[.]write(.*)[+](.*)/i";
            //Ex: hide.src = "http://localhost/Web2.0/LAB/lab1/info.php?value="+x.value
            for ($i = 0; $i < $numLines; $i++) 
            {
                // use trim to remove the carriage return and/or line feed character 
                // at the end of line 
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);

                if(preg_match($s1,$s))
                {
                    $xmixsql[$j][0] = $value;     //filename
                    $xmixsql[$j][1] = ($i+1);     //line no
                    $xmixsql[$j][2] = "-";        // no db
                    $xmixsql[$j][3] = $s;         //error line
                    $xmixsql[$j][4] = "SQL INJECTION";
                    $xmixsql[$j][5] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
                if(preg_match($xssreghtml,$s) or preg_match($xssreghtml2, $s))
                {
                    $xmixsql[$j][0] = $value;     //filename
                    $xmixsql[$j][1] = ($i+1);     //line no
                    $xmixsql[$j][2] = "-";        // no db
                    $xmixsql[$j][3] = $s;         //error line
                    $xmixsql[$j][4] = "XSS";
                    $xmixsql[$j][5] = "HIGH";
                    $j = $j + 1;
                }
                if(preg_match($dosreg,$s))
                {
                    $xmixsql[$j][0] = $value;     //filename
                    $xmixsql[$j][1] = ($i+1);     //line no
                    $xmixsql[$j][2] = "-";        // no db
                    $xmixsql[$j][3] = $s;         //error line
                    $xmixsql[$j][4] = "DOS";
                    $xmixsql[$j][5] = "HIGH";
                    $j = $j + 1;
                }
            }

    }

   if($imageFileType == 'php')
    {
            $f = fopen($td.'\\'.$value,"r") or die("Couldn't open $target_file");
            $tf = $td.'\\'.$value;
            $tf = str_replace('/','\\',$tf);
            $flag = 0;
            $content = file($tf);
            $numLines = count($content);
            //var_dump($numLines);
            // process each line
        $s1 = "/&quot;(SELECT|ALTER|DROP|DELETE|INSERT|UPDATE)(.*)where(.*)[=](IN|NOT IN)*(.*)[$](.*)(AND|OR)*(.*)&quot;/i";
            $sdbsel = "/mysqli_connect()/i"; 
            //Ex: &quot;SELECT * from user where Username=\\&quot;$name\\&quot; AND Password=\\&quot;$pwd\\&quot;&quot;
            $sdbmongo = "/MongoClient()/i";
            $s2 = "/(.*)&quot;(.*)[=][>]([$][_]GET(.*)|[$](.*))/i";
            $sdcass = "/Cassandra/i";
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
                    $xmixsql[$j][0] = $value;
                    $xmixsql[$j][1] = ($i+1);
                    $xmixsql[$j][2] = "MYSQL";
                    $xmixsql[$j][3] = $s;
                    $xmixsql[$j][4] = "SQL INJECTION";
                    $xmixsql[$j][5] = "MODERATE TO HIGH";
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
                    $xmixsql[$j][0] = $value;
                    $xmixsql[$j][1] = ($i+1);
                    $xmixsql[$j][2] = "MongoDB";
                    $xmixsql[$j][3] = $s;
                    $xmixsql[$j][4] = "SQL INJECTION";
                    $xmixsql[$j][5] = "MODERATE TO HIGH";
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
                    $xmixsql[$j][0] = $value;
                    $xmixsql[$j][1] = ($i+1);
                    $xmixsql[$j][2] = "Cassandra";
                    $xmixsql[$j][3] = $s;
                    $xmixsql[$j][4] = "SQL INJECTION";
                    $xmixsql[$j][5] = "MODERATE TO HIGH";
                    $j = $j + 1;
                }
   
            }
          }
          $xssreg = "/require[ ]*[(][$](.*)&quot;(.*)&quot;/i";
          $xssreg2 = "/location[.]redirect(.*)[$](.*)/i";
          for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($xssreg,$s) or preg_match($xssreg2, $s))
                {
                    $xmixsql[$j][0] = $value;
                    $xmixsql[$j][1] = ($i+1);
                    $xmixsql[$j][2] = "-";
                    $xmixsql[$j][3] = $s;
                    $xmixsql[$j][4] = "XSS";
                    $xmixsql[$j][5] = "HIGH";
                    $j = $j + 1;
                }
   
            }
             for ($i = 0; $i < $numLines; $i++) 
            {
                $line = (string)(trim($content[$i]));
                $s = htmlspecialchars($line);
                if(preg_match($dosreg,$s))
                {
                    $xmixsql[$j][0] = $value;
                    $xmixsql[$j][1] = ($i+1);
                    $xmixsql[$j][2] = "-";
                    $xmixsql[$j][3] = $s;
                    $xmixsql[$j][4] = "DOS";
                    $xmixsql[$j][5] = "HIGH";
                    $j = $j + 1;
                }
   
            }
    }
   
 //var_dump($xmixsql);   
}
$r = count($xmixsql);
if($r == 0 and $r == 1)
{
    $rating = 5;
}
else if($r >1 and $r<=5)
{
    $rating = 4; 
}
else if($r>5 and $r<=10)
{
    $rating = 3;
}
else if($r>10 and $r<=15)
{
    $rating = 2;
}
else if($r>15 and $r<=20)
{
    $rating = 1;
}
else
{
    $rating = 0;
}
$sql = "INSERT INTO Repository(Title,Scanned,Rating,Description,UploadDate) VALUES ('$title','yes','$rating','$desc','$date');";
$retval = mysqli_query($conn,$sql);
    if(! $retval ) 
        {
            die('Could not enter data: ' . mysqli_error($conn));
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

tr:nth-child(even){background-color: #f2f2f2}

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

<br>
<br>
<br>
<br>
<br>          
<h1 id="prefect"></h1>
<div  style="overflow-x:auto;">
<table id="sqltable" border="3">
<tr>
<th>Filename</th>
<th>Line No.</th>
<th>Database</th>
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
<li><h4><a href="#" id="Xssclick"> <span style="color: #eaaec4;"><u>XSS</u> </span>Error Details </a></h4></li> 
<li><h4><a href="#" id="dosclick"> <span style="color: #eaaec4;"><u>DOS</u> </span>Error Details </a></h4></li> 
</ul>
</div>
</div>
<div style="background-image: url(images/parcherr.jpg)" id="sug" class="box effect2">
<h3><u>SUGESSTIONS</u></h3>
<br>
<ul>
<li><h4><a href="#" id="SQLsugclick" style="color: blue;"><u> SQL injection </u></a> Defense Measaures</h4></li>
<li><h4><a href="#" id="Xsssugclick"> <span style="color: blue;"><u>XSS</u> </span>Defense Measaures </a></h4></li> 
<li><h4><a href="#" id="dossugclick"> <span style="color: blue;"><u>DOS</u> </span>Defense Measaures </a></h4></li>  
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
var xsqlall =<?php echo json_encode($xmixsql)?>;
//alert(typeof(xsqlhtm[0][0]))
if(xsqlall.length>0 && typeof(xsqlall[0][0]) != "undefined")
{

    for(var i=0;i<xsqlall.length;i++)
    {
         var t = document.getElementById("sqltable");
         var rowCount = t.rows.length;
         var row = t.insertRow(rowCount);
         var cell1 = row.insertCell(0)
         var cell2 = row.insertCell(1);
         var cell3 = row.insertCell(2);
         var cell4 = row.insertCell(3);
         var cell5 = row.insertCell(4);
         var cell6 = row.insertCell(5);
         cell1.innerHTML = xsqlall[i][0];
         cell2.innerHTML = xsqlall[i][1];
         cell3.innerHTML = xsqlall[i][2];
         cell4.innerHTML = xsqlall[i][3];
         cell5.innerHTML = xsqlall[i][4];
         cell6.innerHTML = xsqlall[i][5];
         cell4.style.color = "RED";
         cell5.style.color = "RED";
         row.style.backgroundColor = "rgb(242,242,242)";

    }
}
else
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
<p style='color:black'>This link helps have a better understanding of details mentioned above: <a href='http://www.programmerinterview.com/index.php/database-sql/sql-injection-example/' style='color: blue;font-weight: bold;'>SQL INJECTION EXAMPLE</a>\
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
//Modal for sql injection suggestion display
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