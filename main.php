<?php
print "Hello";

//connect database
include("account.php") ;
include("myFunctions.php") ;


error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors' , 1);

$db = mysqli_connect($hostname, $username, $password, $project) ;
if (mysqli_connect_errno())
  {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	  exit();
  }
print "<br>Successfully connected to MySQL.<br><br><br>";

mysqli_select_db($db, $project);

$warning = "";
$dataOK = true;
$state=-2;

$ucid = get("ucid", $dataOK);

$p = get("p", $dataOK);

$account = get("account", $dataOK);

$choice = get("choice", $dataOK);

$amount = get("amount", $dataOK);

if($state == 1){
  $pin = $_GET["pin"];
}

if(!$dataOK){ $state = -1; }

if($dataOK && super_auth($ucid, $p, $state, $newpin))
{
	 if ($choice == "see")       { see($ucid, $account);}
   if ($choice == "transact")  { transact($ucid, $account, $amount);}
	 exit();
}

?>

<?php if($state == -1)   { print "<b><h2>Bad input data.</b>$warning<br>"  ;} ?>
<?php if($state ==  0)   { print "<br><h2>Authentication Failed.<br>" ;}?>
<?php if($state ==  1)   { print "<br><h2> Your new pin is: $newpin<br>";}?>

<!DOCTYPE html><meta charset="utf-8">
<style> div {border: 2px solid red; width: 50%; padding: 10px;}</style>
<style> div {display: none;} </style>

<form action = "main.php">
<input type = text name = "ucid" value ="<?php print $ucid; ?>" placeholder = "Required" autocomplete = "off"> 	     Enter ucid <br><br>
<input type = text name = "p" value ="<?php print $p; ?>" placeholder = "Required" autocomplete = "off">			       Enter password <br><br>
<input type = text name = "account" value ="<?php print $account; ?>"> Enter account<br><br>
<input type = text name = "pin">                                       Enter pin <br><br>

<div id="amount">
<br>Enter transaction amount <input type = text name = "amount" value = "<?php print $amount; ?>"><br><br>
</div>

<select id="choice" name="choice">
  <option value=""   >       Choose   </option>
  <option value="see" >      See      </option>
  <option value="transact" > Transact </option>
</select>

<br><br><input type = submit >

</form>

<script>

  var ptrAmnt = document.getElementById("amount")
  var ptrChoice = document.getElementById("choice")
      ptrChoice.addEventListener('change', H)

  function H(){
      if(ptrChoice.value != "transact"){ptrAmnt.style.display = "none" }
      else {ptrAmnt.style.display = "block"}
  }

</script>
