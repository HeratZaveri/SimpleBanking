<?php

function see( $ucid , $account){

	  global $db;
	  global $t;
    global $t2;
    
    $s = "select * from accounts where ucid = '$ucid' and account = '$account'";
    
	  $s2 = "select * from  transactions where ucid= '$ucid' and account = '$account' " ;
     
	  print "SQL for accounts: $s<br><br>" ;
    print "SQL for transactions: $s2<br><br>";
    
    ($t = mysqli_query($db, $s)) or die (mysqli_error($db));
    
    ($t2 = mysqli_query($db, $s2)) or die (mysqli_error($db));
    echo "<hr>";
    $num = mysqli_num_rows( $t );
    print "<br>Num rows from accounts: $num<br>"  ;
    
    $num2 = mysqli_num_rows( $t2 );
    print "<br>Num rows from transactions: $num2<br>"  ;
    
	  //retrieve and print transaction amount, timestamp, and mail of user
  echo "<hr>";
  while($r = mysqli_fetch_array($t,MYSQLI_ASSOC)){
		$account    = $r[ "account" ];
		$balance  = $r[ "balance" ];
    $recent = $r["recent"];
		print " account is $account  ||  balance is $balance || recent is $recent<br>";
    echo "<hr>";
	};
 
	while($r2 = mysqli_fetch_array($t2,MYSQLI_ASSOC)){
		$amount     = $r2[ "amount" ];
		$timestamp  = $r2[ "timestamp" ];
		print "amount is $amount  ||  timestamp is $timestamp<br>";

	};
}

function transact($ucid, $account, $amount){

	global $db;
	global $t;

	 $s = "update accounts set recent=NOW(),  balance = balance + '$amount' where ucid = '$ucid' and balance + '$amount' >= 0 ";
   print "SQL is: $s<br><br>";
	($t = mysqli_query($db, $s)) or die (mysqli_error($db));
	$num = mysqli_affected_rows( $db );
	if($num == 0){echo "<br> Either Overdraft or Invalid Account"; return;}

	$s = "insert into transactions values ('$ucid', '$account', '$amount', NOW(), 'N')";
	($t = mysqli_query($db, $s) or die (mysqli_error($db)));
  see($ucid, $account);


}


function get($fieldname, &$dataOK){
	global $db, $warning;
	$v = $_GET[$fieldname];
	$v = trim($v);
	$v = mysqli_real_escape_string($db, $v);

	if(($v == "")&& ($fieldname == "ucid")){$warning .= "<br>Empty ucid"; $dataOK = false;}

	if(($v == "")&& ($fieldname == "p")){$warning .= "<br>Empty pass"; $dataOK = false;}

	if(($v == "")&& ($fieldname == "account")){$warning .= "<br>Empty account"; $dataOK = false;}

	if(($v == "")&& ($fieldname == "choice")){$warning .= "<br>No Choice"; $dataOK = false;}
 
	if(($v != "")&& ($fieldname == "amount") && (!is_numeric($v)))
				{$warning .= "<br>Non-numeric amount"; $dataOK = false;}

	echo "The value of $fieldname is $v. <br>";
	return $v;
}


function authenticate($ucid, $p, &$DBpin){

    global $db, $t;
     
    $s= "select * from users where ucid = '$ucid' and pass='$p'";
    
    echo "<hr>";
		print "<br>SQL for authenticate: $s";
   
		($t = mysqli_query($db, $s)) or die (mysqli_error($db));
		$num = mysqli_num_rows( $t );
		if($num == 0){ return false;}

		$r = mysqli_fetch_array($t, MYSQLI_ASSOC);
		$DBpin = $r[ "pin" ];

		return true;
}

function randomPIN(){
	global $db, $ucid;
	$newpin = mt_rand( 1000, 9999);
	$s = "update users set pin = '$newpin' where ucid = '$ucid'";
	($t = mysqli_query($db, $s)) or die (mysqli_error($db));

	return $newpin;
}

function mymail( $newpin ) {
		$to = "hz383@njit.edu";
		$subj = "PIN";
 	  $msg = $newpin ;

		mail ($to, $subj, $msg ) ;
}

function super_auth ($ucid, $p, &$state, &$newpin){

	global $db;

  if( !authenticate( $ucid , $p, $DBpin))
  {
		$state=0;
		return false;
  }
  if(!isset( $_GET["pin"] ) || ($_GET["pin"] == 0)|| ($_GET["pin"] != $DBpin))
  {
        $newpin = randomPIN();
        mymail($newpin);
        $state=1;
		    return false;
	}
    
	$s = "update users set pin = '0' where ucid= '$ucid' and pass='$p'";
  print "<br><br>SQL pin reset to 0 statement is: $s<br><br>";
 	($t = mysqli_query($db, $s) or die (mysqli_error($db)));
  return true;
 
}



?>
