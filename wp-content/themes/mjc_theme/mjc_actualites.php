<?php
/**
 * Template Name: MJC - Page Actualités
 */

get_header();

// Affichage d'une seule actualité
if (isset($_GET['actu'])) {
	$id_actu = $_GET['actu'];
	$actu = $wpdb->get_row( "SELECT * FROM mjc_actus WHERE id=$id_actu" );

	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title">Actu : <?php echo trim(stripslashes($actu->nom)) ?></h1>
		</header>
		<div class="entry-content">
			<div class="presentation">
				<div class='photo'>
					<img src='wp-content/uploads/actus/<?php echo $actu->photo ?>' />
				</div>
				<div class='contenu'>
					<p><?php echo $actu->jour_heure ?></p>
					<p><?php echo trim(stripslashes($actu->lieu)) ?></p>
					<p><?php echo trim(stripslashes($actu->tarif)) ?></p>
				</div>
			</div>
		</div>
	</article>
	<?php
}

// On n'a rien cherche
else {

$actus = $wpdb->get_results( "SELECT * FROM mjc_actus WHERE date(now()) >= date_debut_publication AND date(now()) <= date_fin_publication ORDER BY date_actu DESC" );

?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title">Toutes nos actus</h1>
		</header>
		<div class="entry-content">
		<div class="listes liste_actus">
			<?php
			$i = "droite";
			foreach ($actus as $actu) {
				$i = ($i == "gauche") ? "doite" : "gauche";
				$lien = get_permalink($post->ID);

				echo "<a href='$lien&actu=$actu->id'>
					<div class='$i'>
						<div class='cover'><div style='background-image: url(wp-content/uploads/actus/$actu->photo)'></div></div>
						<div class='contenu'>
							<h2>" . trim(stripslashes($actu->nom)) . "</h2>
							<p>" . $actu->jour_heure . "</p>
							<p>" . trim(stripslashes($actu->lieu)) . "</p>
						</div>
					</div>
				</a>";
			}
			?>
			
		</div>
	</div>


	</article><!-- #post -->

<?php
}
?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>