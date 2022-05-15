<?php session_start(); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
 $ip = $_SERVER["REMOTE_ADDR"];
 $user=htmlentities($_POST['user'], ENT_QUOTES, "UTF-8"); // login z formularza
 $pass=htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8"); // hasło z formularza
 $dbhost="xxx"; $dbuser="xxx"; $dbpassword="xxx"; $dbname="xxx";
 $link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
 if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
 mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
 $result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'"); // wiersza, w którym login=login z formularza
 $rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD 
 mysqli_query($link, "INSERT INTO logi (adres_ip) VALUES ('$ip');");
 $liczniki = mysqli_query($link, "SELECT COUNT(*) as licznik FROM logi WHERE " .
  "adres_ip = '$ip' AND TIMESTAMPDIFF(SECOND, datagodzina, CURRENT_TIMESTAMP()) < 30");
 while ($wiersz = mysqli_fetch_array ($liczniki)) { $licznik = $wiersz[0]; }
 if ($licznik >= 3) {
  mysqli_query($link, "INSERT INTO blokady (adres_ip, ogloszono) VALUES ('$ip', 0);");
  mysqli_close($link); // zamknięcie połączenia z BD
  header("Location: logowanie.php");
  exit();
 }
 if(!$rekord) //Jeśli brak, to nie ma użytkownika o podanym loginie
 {
 mysqli_close($link); // zamknięcie połączenia z BD
 echo "Brak użytkownika o takim loginie !"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
 header("Location: logowanie.php");
 exit();
 }
 else
 { // jeśli $rekord istnieje
 if($rekord['password']==$pass) // czy hasło zgadza się z BD
 {
 mysqli_query($link, "DELETE FROM logi WHERE adres_ip = '$ip';");
 mysqli_close($link);
 echo "Logowanie Ok. User: {$rekord['username']}. Hasło: {$rekord['password']}";
 $_SESSION['loggedin'] = true;
 $_SESSION['username'] = "{$rekord['username']}";
 header("Location: index.php");
 exit();
 }
 else 
 {
 mysqli_close($link);
 echo "Błąd w haśle !"; // UWAGA nie wyświetlamy takich podpowiedzi dla hakerów
 header("Location: logowanie.php");
 exit();
 }
 }
?>
</BODY>
</HTML>
