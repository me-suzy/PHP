<? # $Id: arbo.php,v 1.2 2001/04/12 08:01:33 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "conf/hitweb.conf" ;


//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "$REP_CLASS/class.db_$BASE".$EXT_PHP ;




//Connection
$base = new class_db ;
//$base->debug = 1; 
$base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


// selection des messages
$sql = "SELECT * from CATEGORIES";
$aryResultatRequete = $base->query("$sql");


// fonction récursive qui affiche les sous messages d'un message
function affiche_sous_msg($argMessagesFils,$argMessageId)
{
  if($aryMessages = $argMessagesFils[$argMessageId])
    {
      echo "<ul>";
      while(list(,$aryMessage) = each($aryMessages))
	{
	  echo "<li>$aryMessage[CATEGORIES_NOM]";
	  affiche_sous_msg($argMessagesFils,$aryMessage[CATEGORIES_ID]);
	}
      echo "</ul>";
    }
}

// rangement des messages
while($aryMessage = mysql_fetch_array($aryResultatRequete))
{
  $id_parent = $aryMessage[CATEGORIES_PARENTS];
  // c'est le premier message de la discussion on l'ajoute dans le
  //tableau des sujets
  if ($id_parent=="0") {
    $aryMessagesSujets[] = $aryMessage; 
    // sinon on l'ajoute dans la teableau des messages fils
  } else {
    $aryMessagesFils[$id_parent][] = $aryMessage;
  }
}
 

 
// affichage des messages
echo "<ul>";
while(list(,$arySujet) = each($aryMessagesSujets))
{
  echo "<li>$arySujet[CATEGORIES_NOM]";
  // appel de la fonction récursive qui va afficher tous les sous
  //messages
  affiche_sous_msg($aryMessagesFils,$arySujet[CATEGORIES_ID]);
}
echo "</ul>";



?>
