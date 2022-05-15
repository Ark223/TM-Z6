<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
 $user=htmlentities($_POST['user'], ENT_QUOTES, "UTF-8"); // login z formularza
 $pass=htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8"); // hasło z formularza
 $pass2=htmlentities ($_POST['pass2'], ENT_QUOTES, "UTF-8"); // potwórzone hasło z formularza
 $dbhost="xxx"; $dbuser="xxx"; $dbpassword="xxx"; $dbname="xxx";
 $link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
 if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
 mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
 $zapytanie = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";
 if ($pass == $pass2 && mysqli_query($link, $zapytanie))
 {
 echo "Dodano nowego usera";
 mkdir($user, 0777);
 header("Location: index.php");
 }
 else
 {
 echo "Błąd przy rejestracji";
 header("Location: rejestruj.php");
 }
?>
</BODY>
</HTML>
