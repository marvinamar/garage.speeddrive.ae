<?php
   $dbhost = 'localhost';
   $dbuser = 'root';
   $dbpass =   '';// 'okSCg!R31HBtjY7O';'nSYn@A8t)JM;'; 
   $dbname = 'garagespeeddrive';//'florafan_flora';  
   $con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname); 
   
   if(! $con ){
     die('Could not connect: ' . mysqli_error());
   }


  // $db = new PDO('mysql:'.$dbhost.';dbname='.$dbname.','.$dbuser.','.$dbpass);
//$db = new PDO('mysql:'.$dbhost.';dbname='.$dbname.','.$dbuser.','.$dbpass);

try {
  $ndb = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  // set the PDO error mode to exception
  $ndb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 // echo "Connected successfully"; 
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
   
?>