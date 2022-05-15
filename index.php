<?php declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin']))
{
header('Location: logowanie.php');
exit();
}
$ip = $_SERVER["REMOTE_ADDR"];
$user = $_SESSION['username'];
$root = $_SERVER['DOCUMENT_ROOT'] . '/s6/z6/' . $user . '/';
$path = $_SERVER['DOCUMENT_ROOT'] . '/s6/z6/' . $user . '/';
echo ('<form action="czyszczenie.php" method="POST">');
echo (' <input type="submit" value="Wyloguj" /></form>');
echo "Użytkownik: " . $_SESSION['username'] . ".<br>";

$dbhost="xxx"; $dbuser="xxx"; $dbpassword="xxx"; $dbname="xxx";
$link = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
$result = mysqli_query($link, "SELECT datagodzina FROM blokady WHERE adres_ip = '$ip' AND ogloszono = 0 ORDER BY idb DESC LIMIT 1;");
while ($wiersz = mysqli_fetch_array ($result)) {
 echo '<span style="color:red;">Ostatnie błędne logowanie: ' . $wiersz[0] . '</span><br>';
}
mysqli_query($link, "UPDATE blokady SET ogloszono = 1 WHERE adres_ip = '$ip';");
mysqli_close($link);

function usun_folder($dirPath) {
 if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
  $dirPath .= '/';
 }
 $files = glob($dirPath . '*', GLOB_MARK);
 foreach ($files as $file) {
  if (is_dir($file)) {
   usun_folder($file);
  } else {
   unlink($file);
  }
 }
 rmdir($dirPath);
}

if (isset($_GET['katalog'])) {
  $path = $path . $_GET['katalog'] . "/";
}

if (isset($_GET['usun'])) {
  $sciezka = $path . "/" . $_GET['usun'];
  $sciezka = preg_replace('/([^:])(\/{2,})/', '$1/', $sciezka);
  if (is_dir($sciezka)) usun_folder($sciezka);
  else unlink($sciezka);
  header('Location: ?katalog=');
}
?>

<form method="POST" enctype="multipart/form-data">
 <br>Wybierz plik do wysłania:<br>
 <input type="file" name="fileToUpload" id="fileToUpload">
 <input type="submit" name="submit" name="submit">
</form>

<form method="POST">
 Stwórz nowy folder:<br>
 Nazwa: <input type="text" name="folder" maxlength="16"><br>
 <input type="submit" value="Stwórz">
</form>

<?php
if (isset($_POST['folder'])) {
 $sciezka = $path . "/" . $_POST['folder'];
 $sciezka = preg_replace('/([^:])(\/{2,})/', '$1/', $sciezka) . "/";
 mkdir($sciezka, 0777);
}

if (isset($_POST['submit'])) {
  if ($_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
    $sciezka = $path . "/" . basename($_FILES["fileToUpload"]["name"]);
    $sciezka = preg_replace('/([^:])(\/{2,})/', '$1/', $sciezka);
	move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $sciezka);
  }
}

echo 'Drzewo plików:<br>';
$files = scandir($path);
foreach ($files as $file){
 if (strcmp($file, '.') == 0) continue;
 $zrodlo = realpath($_SERVER['DOCUMENT_ROOT']. "/s6/z6/" . $user . "/" .
   (isset($_GET['katalog']) ? $_GET['katalog'] . "/" : "") . $file);
 //$rozmiar = ceil(filesize($zrodlo) / 1024);
 //$datagodz = date('Y-m-d H:i:s', filemtime($zrodlo));
 $relat1 = substr($zrodlo, strlen($root));
 if (strcmp($file, '..') == 0) { $relat1 = ''; }
 $relat2 = $user . '/' . $relat1;
 $format = end(explode('.', $file));
 $ikona = 'tekst.png';
 if ($format == 'txt' || $format == 'pdf') { $ikona = 'graf.png'; }
 if ($format == 'mp3' || $format == 'wav') { $ikona = 'sound.png'; }
 if (is_dir($zrodlo)) {
  echo '<a href=?katalog=' . $relat1 . '>' . $file . '<img src="skok.png"></a>';
  if ($relat1 != '') echo '<a href=?usun=' . $relat1 . '><img src="kosz.png"></a><br>';
  else echo '<br>';
 } else {
  echo '<a href="' . $relat2 . '" download>' . $file . '<img src="' . $ikona . '"></a>';
  echo '<a href=?usun=' . $relat1 . '><img src="kosz.png"></a><br>';
 }
}
?>
