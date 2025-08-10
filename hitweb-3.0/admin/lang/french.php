<? # $Id: french.php,v 1.8 2001/08/28 10:47:41 hitweb Exp $

// Titre des pages ou des parties 
$title_admin = "ADMINISTRATION DE L'ANNUAIRE HITWEB" ;
$title_som_admin = "SOMMAIRE" ;
$title_conf = "Gestion du fichier de configuration" ;
$title_categories = "Gestion des CATEGORIES" ;
$title_categories_liens = "Gestion de la catégorie";
$title_links = "Gestion des liens" ;
$title_update_cat = "Modification d'une categorie" ;
$title_add_cat = "Ajouter une nouvelle catégorie :";

// Liens hypertexte
$link_conf_db = "Configuration de la base de données";
$link_conf_file = "Fichier de configuration" ;
$link_application = "Gestion de l'application" ;
$link_valid_url = "Liens à valider" ;
$link_polls = "Gestion de la bannière de pub" ;
$link_check_url = "Analyser la validité des liens" ;
$link_international_admin = "Internationalisation de la partie administration" ; 
$licence = "../gpl.txt" ;

$link_update = "Modifier" ;
$link_delete = "Supprimer" ;
$link_categories = "Catégorie" ;
$link_add_categories = "Ajouter une categorie" ;
$link_add_links = "Ajouter un site" ;


// Libellé
$lib_dbhost = "Serveur name" ;
$lib_dbname = "Database name" ;
$lib_dbuser = "User name" ;
$lib_dbpass = "Password" ;
$lib_type_db = "Type base de données" ;
$lib_repclass = "Répertoire class" ;
$lib_replangadmin = "Répertoire lang admin" ;
$lib_langadmin = "Language for admin" ;
$lib_ext_php = "Extension PHP" ;
$lib_ext_tpl = "Extension TPL" ;
$lib_rep_tpl = "Template";
$lib_use_mail = "Utilisation du mail";
$lib_func_mail = "function mail chez votre hébergeur";
$lib_mail_moderator = "Mail modérateur" ;
$lib_address_site = "Adresse du site" ;

$lib_id = "Id :" ;
$lib_name = "Nom :";
$lib_lastname = "Prénom :";
$lib_mail = "Email :";
$lib_address = "Adresse :";
$lib_keyword = "Mots clés :";
$lib_subject = "Sujet :";
$lib_description = "Description :";

// Message
$mes_select_categorie = "Vous devez selectionner une categorie pour ensuite proposer un site.";
$mes_fields_empty = "Vous devez remplir tous les champs !!!";
$mes_link_in_hitweb = "Ce site est déjà présent dans HITWEB !!!";
$mes_link_not_valid = "<b><font color='#FF0000'>ATTENTION !</font><br>Le site n'est pas valide. Si le problème persiste, contactez moi par mail</b>";
$mes_enre_hitweb = "
 Merci d'avoir rempli ce formulaire.<p>

          Votre site est présent actuellement dans la partie liens à valider. Il sera 
          réellement validé après la visite du modérateur de HITWEB.<p>

          Le classement des liens dans HITWEB se fait par nombre de point. Plus un
          site a de point, meilleur sera sa position dans l'ensemble du site :<br>
          - Dans sa catégorie.<br>
          - Dans le top 50.<br>
          - Lors d'une recherche.<br>
          - etc...<p>

          Pour gagner des points, il suffit de faire connaître HITWEB autour de vous :<br>
          - Mettre un lien sur votre site vers le site HITWEB<br>
          - Présenter le site à un ou plusieurs amis<br>
          - Enregistrer vos meilleurs liens dans HITWEB.<p>
";
$mes_pb_meta = "
<p>
		  Je viens d'analyser votre URL pour regarder vos balises META, mais malheureusement,
          je n'ai pas trouvé d'information sur la description de votre page et sur les mots clés
          correspondant à votre site... Ces informations ne sont pas obligatoires mais vivement 
          recommandés si vous voulez être placé correctement dans les moteurs de recherche.
          <p>
          Donc je vous conseil de regarder le code source du site <a href='http://www.hitweb.org'>HITWEB</a> et de recopier toutes les balises
          se trouvant entre &lt;HEAD> ... &lt;/HEAD> en adaptant les informations à votre propre site.
          <p>&nbsp;
";
$mes_update_link = "Vos modifications viennent d'être enregistrées.";


// Boutton formulaire
$bt_enre = "Enregistrer" ;
$bt_reset = "Annuler" ;

?>
