<?php
$prod = $_POST['product'];
$feed = $_POST['feedback'];

$conn=mysqli_connect('localhost','root','') or die(mysqli_error($conn));
 mysqli_select_db($conn,'prodreview') or die(mysqli_error($conn));
//$prod = htmlspecialchars($prod);  //uncomment these after showing attack while showing protection
//$feed = htmlspecialchars($feed);
$query = "INSERT INTO review(prodname,review) VALUES ('$prod','$feed');";
mysqli_query($conn,$query);

$query1 = "SELECT * FROM review Where prodname = \"$prod\"";
$retval = mysqli_query($conn,$query1);
echo "Reviews  : ";
echo "<br/>";
while($row = mysqli_fetch_array($retval, 1))
{
	echo $row ["review"];
	echo "<br/>";
}
?>















