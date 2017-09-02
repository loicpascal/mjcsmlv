<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <a name="top"></a>
    <div id="page" class="hfeed site">
        <header id="masthead" class="site-header" role="banner">
            <section class="home-link" rel="home">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <img id="titre_site" src="wp-content/themes/mjc_theme/images/headers/wood.png" />
                    <!-- <h1 class="site-title" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php // bloginfo( 'name' ); ?><br>
                    <span class="site-description"><?php // bloginfo( 'description' ); ?></span>
                    </h1> -->
                </a>
                <!-- <img id="logo_site" src="wp-content/themes/mjc_theme/images/headers/logo_mjc.png" /> -->
                <section id="recherche_activite">
                    <form action="<?php echo get_permalink(20); ?>" method="post">
                        <p <?php  if ($post->ID != 95) { ?> class="pages_autres" <?php } ?>>
                            <?php  if ($post->ID == 95) { // Si on est sur la page d'accueil ?>
                                <input type="text" name="recherche_activites_mots" placeholder="Trouvez votre activité..." maxlength="20" required />
                                <input type="submit" value="Trouver !" />
                            <?php } ?>
                        </p>
                    </form>
                </section>
            </section>

            <div id="navbar" class="navbar">
                <nav id="site-navigation" class="navigation main-navigation" role="navigation">
                    <button class="menu-toggle"><?php _e( 'Menu', 'twentythirteen' ); ?></button>
                    <a class="screen-reader-text skip-link" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentythirteen' ); ?>"><?php _e( 'Skip to content', 'twentythirteen' ); ?></a>
                    <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'menu_id' => 'primary-menu' ) ); ?>
                    <a href="https://www.facebook.com/MJC-Saint-Marcel-Les-Valence-1439891222933892/?fref=ts" title="Notre page Facebook" target="_blank">
                        <img class="logo" src="wp-content/themes/mjc_theme/images/headers/icon_facebook.png" />
                    </a>
                </nav><!-- #site-navigation -->
            </div><!-- #navbar -->
        </header><!-- #masthead -->

        <div id="main" class="site-main">
