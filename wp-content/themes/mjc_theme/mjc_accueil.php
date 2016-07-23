<?php
/**
 * Template Name: MJC - Page d'accueil
 */
get_header(); ?>

<?php
$actu = $wpdb->get_row( "SELECT * FROM mjc_actus ORDER BY date_actu DESC LIMIT 1" );



?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content page_mjc_accueil">
		<div class="actus">
			<a href="<?php echo get_permalink(10); ?>">
				<div>Actus</div>
			</a>
			<div><b><?php echo trim(stripslashes($actu->jour_heure)); ?></b> : <?php echo trim(stripslashes($actu->nom)); ?> - <?php echo trim(stripslashes(substr($actu->descriptif, 0, 45))) . "..."; ?></div>
		</div>
		<div class="bloc_accueil">
			<div>
				<p>ACTIVITES</p>
				<p>Retouvez toutes les activités proposées par la MJC...</p>
				<?php foreach (wp_get_nav_menu_items('Menu 1') as $item) { 
					if ($item->post_parent == 6) {
				?>
					<a href="<?php echo $item->url ?>"><?php echo $item->title ?></a>
				<?php 
					}
				} 
				?>
			</div>
		</div>
		<div class="bloc_accueil">
			<div>
				<p>ENFANCE - JEUNESSE</p>
				<p>Découvrez les espaces enfance, jeunesse et multiactivités...</p>
				<?php foreach (wp_get_nav_menu_items('Menu 1') as $item) { 
					if ($item->post_parent == 8) {
				?>
					<a href="<?php echo $item->url ?>"><?php echo $item->title ?></a>
				<?php 
					}
				} 
				?>
			</div>
		</div>
		<div class="bloc_accueil">
			<div>
				<p>ACTUALITES</p>
				<p>Découvrez toutes l'actualité de la MJC. Les évènements passés et à venir...</p>
				<a href="<?php echo get_permalink(10); ?>">Voir toutes les actus</a>
			</div>
		</div>
		<a target="_blank" href="http://www.mjc-saintmarcellesvalence.fr/wp-content/uploads/2016/05/Programme-2015-2016.pdf" title="Télécharger la plaquette des activités">
			<div>
				<button>Télécharger la plaquette des activités</button>
			</div>
		</a>
	</div>


</article><!-- #post -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>