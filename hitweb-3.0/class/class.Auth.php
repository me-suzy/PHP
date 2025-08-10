<? # $Id: class.Auth.php,v 1.1 2001/07/19 15:41:39 hitweb Exp $

//########################################################################################
//# Class d'authentification et gestion des permissions
//########################################################################################

/*
Cette class doit permettre d'authentifier les utilisateurs dans la partie administration
de l'annuaire.. Et d'authentifier les webmaster qui me proposent un ou plusieurs sites web.

Dans la partie administration :
Il y aura l'administrateur ou les administrateurs
        - Ils ont tous les droits (création des modérateurs et gestion des webmasters)
	- Ils peuvent tout faire

Il y aura des modérateurs par catégorie 
        - Les modérateurs peuvent seulement modérer les catégories ou ils ont la permission
	- Lancer des analyses de validation des liens sur cette catégories
	- Administrer les webmasters enregistrés dans les catégories

Il y a les webmasters
        - Ils ont seulement les droits de moficiation concernant leurs sites.
	- Plus des fonctionnalités de l'annuaire (gestion de leurs topliste et autre.)

Je vois une modification dans la structure de la base de données...
- Il faut changer le nom est certaines informations de la tables webmaster

WEBMASTER devient USER le WEBMASTER_LIENS_ID et supprimer de la tables user
et tous les champ sont renommés..

Voici les infos de la table USER
 */




class Auth {

  /*
    La fontion login doit me permettre de connecter l'utilisateur par rapport à ses permissions.
    Administrateur, modérateur, user...
    En fait un affichage par redirection ou template pourra être fait par rapport aux permissions
    de l'utilisateur. Mais attention un test doit être fait sur chaque page pour ne pas avoir
    de pb.

    J'aimerai qu'il soit possible qu'un utilisateur soit aussi un modérateur.
    Et pour la gestion des modérateurs il ne faut bien séparer les modérateurs par
    rubrique... Donc la modération est un groupe qui contient plusieurs
   */

  function authenticate($login="", $passwd="")
    {
      global $DBHOST, $DBUSER, $DBPASS, $DBNAME ;
      global $CookieHitweb; 

	  // Si le cookie n'est pas présent
      if(!$CookieHitweb)
	    {
	      $res = mysql_connect("$DBHOST", "$DBUSER", "$DBPASS") or die ("<p><b>ERREUR DE CONNECTION  A LA BASE</b><p>");
	      mysql_select_db("$DBNAME") or die ("<p><b>PROBLEME SELECTION DE LA BASE</b><p>");
	  
	      $sql = "SELECT login, password FROM user WHERE login='$login' AND password='$passwd'";
	  
	      $result = mysql_query($sql);
	  
	      $num = mysql_num_rows($result) ;
	   
	      if (!$num)
	        {
			  // L'utilisateur n'a pas été authentifiée
	          // Il est possible de passer un id pour afficher un message différent
	          header("Location: login.php");
	        } else {
	          // L'utilisateur est authentifié, création du cookie.
	          $this->login($login, $passwd);     
	        }
	    }
    }
  
    
  function login($login, $passwd)
    {

      // La génération de la valeur md5pass doit être réaliser aléatoirement
	  // sur tous les sites qui utilisent hitweb.. Pour ne pas mettre un trou de
	  // sécurité dans l'application.. Donc elle sera généré à partir du login,
	  // du password et surtout d'un chaine de caractère définie par l'utilisateur
	  // dans la partie administration du site.....

      
	  // Exemple :
	  $chainemd5 = "L'annuaire hitweb classe tout un tas de chose, liens, sites, etc...";
      
	  $loginHitweb = "$login";
      $md5pass = md5($login.$passwd.$chainemd5);
      
      $NameCookie = "CookieHitweb";
      $ValueCookie = "$loginHitweb,$md5pass";
      $ExpireCookie = time()+3600 ; //expire dans 1 heure
      $PathCookie = "/";
      $DomainCookie = "";
      $SecureCookie = "";
      
      setcookie($NameCookie, $ValueCookie, $ExpireCookie, $PathCookie, $DomainCookie, $SecureCookie);
    }
  


  function identification()
    {
      global $DBHOST, $DBUSER, $DBPASS, $DBNAME ;
      global $CookieHitweb;
      
      $user_info = split( ",", $CookieHitweb, 2 );
      
      $loginCookie = $user_info[0]; 
      $md5Cookie = $user_info[1];

      $res = mysql_connect("$DBHOST", "$DBUSER", "$DBPASS") or die ("<p><b>ERREUR DE CONNECTION  A LA BASE</b><p>");
      mysql_select_db("$DBNAME") or die ("<p><b>PROBLEME SELECTION DE LA BASE</b><p>");
      
      $sql = "SELECT user_id, user_privilege_id, login, firstname, name, password, md5pass FROM user WHERE login='$loginCookie' AND md5pass='$md5Cookie' ";
      
      $result = mysql_query($sql);
      
	  $num = mysql_num_rows($result) ;
	   
	  if (!$num)
	    {
		  // Ces informations ne sont pas dans la base 
		    header("Location: login.php");
	    } else {
	        

          while (list ( $user_id, 
		                $user_privilege_id, 
		                $login, 
		                $firstname, 
		                $name, 
		                $password, 
		                $md5pass ) = mysql_fetch_row($result))
				{
		          $user["user_id"] = $user_id;
		          $user["user_privilege_id"] = $user_privilege_id;
		          $user["login"] = $login;
		          $user["firstname"] = $firstname;
		          $user["name"] = $name;
		          $user["password"] = $password;
		          $user["md5pass"] = $md5pass;
	            }

		  return $user;
        }
    }


  function permission($user_privilege_id)
  {
      global $DBHOST, $DBUSER, $DBPASS, $DBNAME ;

      $res = mysql_connect("$DBHOST", "$DBUSER", "$DBPASS") or die ("<p><b>ERREUR DE CONNECTION  A LA BASE</b><p>");
      mysql_select_db("$DBNAME") or die ("<p><b>PROBLEME SELECTION DE LA BASE</b><p>");
      
      $sql = "SELECT privilege_name FROM privilege WHERE privilege_id='$user_privilege_id' ";

	  $result = mysql_query($sql);
      
	  $permission =  mysql_result($result, 0, 0);

	  return $permission;
  
  }


  function logout()
    {
      // Suppréssion du cookie
      $NameCookie = "CookieHitweb";
      $ValueCookie = "";
      $ExpireCookie = time()+3600 ; //expire dans 1 heure
      $PathCookie = "/";
      $DomainCookie = "";
      $SecureCookie = "";

      setcookie($NameCookie, $ValueCookie, $ExpireCookie, $PathCookie, $DomainCookie, $SecureCookie);

      // Redirection vers le formulaire d'authentification
      header("Location: login.php");
    }




} // End class Auth
?>
