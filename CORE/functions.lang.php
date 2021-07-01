<?php

function _user1($id)
{
    global $db;

    $query = $db->query("SELECT * FROM  " . $portal_users . "  WHERE user_id='$id'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}

function _decrypt1($q)
{

    $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(ENC_KEY), base64_decode($q), MCRYPT_MODE_CBC, md5(md5(ENC_KEY))), "\0");
    return ($decoded);

}

function _lang($id)
{
    global $db;

    $query = $db->query("SELECT * FROM  " . $portal_languages . "  WHERE lang_id='$id'");
    if ($query->rowCount() < 0) {

        $row = $query->fetch();
        return $row;

    }

}


function _getLocale()
{
    global $_defaultLocale;

    if (!isset($_SESSION['SESSION_USER']) || (trim($_SESSION['SESSION_USER']) == '')) {

        return $_defaultLocale;


    } else {
        $_user = _user1(_decrypt1($_SESSION['SESSION_USER']));
        $_userLang = _lang($_user['lang']);
        return $_userLang['lang_code'];

    }

}


//$locale = _getLocale();
$locale = $_defaultLocale;
//echo $locale.'denis';

/* include_once $root.'/vendor/gettext/gettext/src/autoloader.php';
//include_once "libs/cldr-to-gettext-plural-rules/src/autoloader.php";

use Gettext\Translator;
//use Gettext\GettextTranslator;
  // Traslation support
  $locale = _getLocale().'.UTF-8';
   $locale = _getLocale();
  echo $locale;
  //putenv("LANG=".$locale);
 //putenv('LANGUAGE');
// $globalna = putenv("LANGUAGE=".$locale);

 //echo $globalna.'jel se postavila';

//putenv("LANG=".$locale); 
 putenv("LANG=".$locale);
 setlocale(LC_ALL, $locale);
setlocale(LC_TIME, $locale);
$domain = 'messages';

   $t = new GettextTranslator();

    //Set the language and load the domain
    $t->setLanguage($locale);
    $t->loadDomain($domain, 'Locale');
 

 // $denis1 =  bindtextdomain('messages', $root."/locale");
 // $denis2 = textdomain('messages');
 
  echo getenv("LANG").'jel zapiso';
  
  print __('Godina');
  
  if( ('safe_mode') ){
   echo 'saaaafe';
}else{
  echo 'nooooooooooooot';
} */

include_once $root . '/vendor/gettext/gettext/src/autoloader.php';
/* use Gettext\Translations;

//import from a .po file:
$translations = Translations::fromPoFile('locale/bs_CI/LC_MESSAGES/messages.po');

//edit some translations:
$translation = $translations->find(null, 'Pozdrav!');

if ($translation) {
	$translation->setTranslation('Pozdrav!');
}

//export to a php array:
$translations->toPhpArrayFile('locale/bs_CI/LC_MESSAGES/bs_CI.php'); */

if (EKSTRAKCIJA) {
//Extract messages from a php code file
    $translations = Gettext\Translations::fromPhpArrayFile($root . '/locale/bs_CI/LC_MESSAGES/bs_CI.php');
//Export to a csv file
    $translations->toCsvFile($root . '/locale/bs_CI/LC_MESSAGES/bs_CI.csv');
}

if (IMPORT) {
//Extract messages from a .csv file:
    $translations = Gettext\Translations::fromCsvFile($root . '/locale/' . $localeImport . '/LC_MESSAGES/' . $localeImport . '.csv');
//export to a php array:
    $translations->toPhpArrayFile($root . '/locale/' . $localeImport . '/LC_MESSAGES/' . $localeImport . '.php');
}


use Gettext\Translator;

//Create the translator instance
$t = new Translator();

//Load your translations (exported as PhpArray):
//echo 'locale/'.$locale.'/LC_MESSAGES/'.$locale.'.php';
$t->loadTranslations($root . '/locale/' . $locale . '/LC_MESSAGES/' . $locale . '.php');
$t->register();

?>
