<?php
//error_reporting(0);
$conn=mysqli_connect('localhost','root','') or die(mysqli_error($conn));
 mysqli_select_db($conn,'prodreview') or die(mysqli_error($conn));
$xdata = array(array());
$value = $_GET['userinput'];
$value = trim($value);
echo "$value";
if(isset($value))
{
	$query="SELECT * FROM users";
$retval = mysqli_query( $conn, $query);
$a = 0;
	       			while ($row = mysqli_fetch_array($retval, 1))
	       				{
					       	$xdata[$a][0] = $row['uid'];
					       	$xdata[$a][1] = $row['uname'];
                            $xdata[$a][2] = $row['upass'];
					       	$a++;
	     				}	
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
<style>
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

</style>
</head>
<body>
<table id="sqlinjec" border="3">
<th>Product</th>
<th>Description</th>
<th>Contact</th>
<th>Number of users</th>
</table>
<script>
var xdata1 =<?php echo json_encode($xdata)?>;
if(typeof(xdata1[0][0])!= "undefined" )
{
	for(var i=0;i<xdata1.length;i++)
{
 	var t = document.getElementById("sqlinjec");
    var rowCount = t.rows.length;
    var row = t.insertRow(rowCount);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    cell1.innerHTML = xdata1[i][0];
    cell2.innerHTML = xdata1[i][1];
    cell3.innerHTML = xdata1[i][2];
}
}
</script>
</body>
</html>