<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
// if ($_SERVER['SERVER_NAME'] == "www.mjc-saintmarcellesvalence.fr") {
	/** Nom de la base de données de WordPress. */
	define('DB_NAME', 'mjcsaintzouser');

	/** Utilisateur de la base de données MySQL. */
	define('DB_USER', 'mjcsaintzouser');

	/** Mot de passe de la base de données MySQL. */
	define('DB_PASSWORD', 'RhmbW38P7u2');

	/** Adresse de l'hébergement MySQL. */
	define('DB_HOST', 'mjcsaintzouser.mysql.db');
// } else {
// 	/** Nom de la base de données de WordPress. */
// 	define('DB_NAME', 'mjcsmlv');

// 	/** Utilisateur de la base de données MySQL. */
// 	define('DB_USER', 'root');

// 	/** Mot de passe de la base de données MySQL. */
// 	define('DB_PASSWORD', '');

// 	/** Adresse de l'hébergement MySQL. */
// 	define('DB_HOST', 'localhost');
// }

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
// define('DB_CHARSET', 'utf8mb4');
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'j37o$nu-}[@3+<ZG(M2f&057?:EmqZ|]*Cr_h7-u.r+puTD6$NXN#PGvq&xD9U|f');
define('SECURE_AUTH_KEY',  'Yu- YW,}8I=L[gmvf6{zn3*}`i1=eVUMvoSv~WgoIAOJ^}Uf`*)75#@5C$`3+~E>');
define('LOGGED_IN_KEY',    'N))%-f~$=1eOZYI.R^_>+y@rr+[K]Z P&[/|>fC;Q>`4:^ldBzLT@CK$|dzvtvJn');
define('NONCE_KEY',        '6|;QhX8$%v7YGbl,U|sb^h#F#@!-?bxEg0+,Eo>HZvP@1fDz[#fu$]TBs^L+:nv.');
define('AUTH_SALT',        'kN-(D/EyGLB=I8~ ``-GM;GmqdTt&P(&#Ecb7n7Q)w#1[UEP$Q!;WB6rMsG4|%+e');
define('SECURE_AUTH_SALT', 'tjb!o{H+7qc7kM7nQ$cT#6L,eSyS|P|4(uA:vVXBVOTe,#{s;b:K4Yv@#w6|ghr)');
define('LOGGED_IN_SALT',   '(:vp]nA*>hP#z61*We0]G@<sKCyZA#i54[JWIMI_Ln4;PSR0E$K*-{#TeGmbF+HR');
define('NONCE_SALT',       'Tk|HkyY7g8[(`y#{tQ+:iqj:oC(3p7yT2 |<xaBWB9!Yct-s4|pyzghO40|YDZ-)');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', false); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');