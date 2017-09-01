<?php
/**
 * Template Name: MJC - Page Actualités
 */

get_header();
require_once( ABSPATH . 'wp-includes/mjc/class/MjcParagraphe.class.php');

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
					<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $actu->jour_heure ?></p>
					<p><i class="fa fa-map-marker fa-1x" aria-hidden="true">&nbsp;</i><?php echo MjcParagraphe::epure($actu->lieu) ?></p>
                    <?php if ($actu->descriptif) { ?>
					<p class="descriptif"><i class="fa fa-quote-left fa-2x" aria-hidden="true">&nbsp;</i><?php echo MjcParagraphe::epure($actu->descriptif) ?></p>
                    <?php } ?>
					<?php if (!empty($actu->tarif)) { ?>
					<p>Prix : <?php echo MjcParagraphe::epure($actu->tarif) ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
	</article>
	<?php
}

// On n'a rien cherché
else {


?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title">Toutes nos actus</h1>
		</header>
		<div class="entry-content">
			<?php
			$actusFutur = $wpdb->get_results( "SELECT * FROM mjc_actus WHERE date(now()) >= date_debut_publication AND date(now()) <= date_fin_publication ORDER BY date_actu" );

			if ($actusFutur) {
				?>
				<h4>Ça va se passer !</h4>
				<div class="listes liste_actus">
					<?php
					foreach ($actusFutur as $actu) {
						$lien = get_permalink($post->ID);
						?>
						<a href='<?php echo $lien; ?>&actu=<?php echo $actu->id; ?>'>
							<div>
								<div class='cover'>
									<div style='background-image: url(wp-content/uploads/actus/<?php echo $actu->photo ?>)'></div>
								</div>
								<div class='contenu'>
									<h2><?php echo MjcParagraphe::epure($actu->nom) ?></h2>
									<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $actu->jour_heure ?></p>
									<p><i class="fa fa-map-marker fa-1x" aria-hidden="true">&nbsp;</i><?php echo MjcParagraphe::epure($actu->lieu) ?></p>
								</div>
							</div>
						</a>
					<?php } ?>
				</div>
				<h4>&nbsp;</h4>
			<hr>
			<?php } ?>
			<h4>Ça s'est passé...</h4>
			<div class="listes liste_actus">
				<?php
				$actus_passees = $wpdb->get_results( "SELECT * FROM mjc_actus WHERE date(now()) > date_actu AND date(now()) > date_fin_publication ORDER BY date_actu DESC" );
				foreach ($actus_passees as $actu) {
					$lien = get_permalink($post->ID);
					?>
					<a href='<?php echo $lien; ?>&actu=<?php echo $actu->id; ?>'>
						<div>
							<div class='cover'>
								<div style='background-image: url(wp-content/uploads/actus/<?php echo $actu->photo ?>)'></div>
							</div>
							<div class='contenu'>
								<h2><?php echo MjcParagraphe::epure($actu->nom) ?></h2>
								<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $actu->jour_heure ?></p>
								<p><i class="fa fa-map-marker fa-1x" aria-hidden="true">&nbsp;</i><?php echo MjcParagraphe::epure($actu->lieu) ?></p>
							</div>
						</div>
					</a>
				<?php } ?>
			</div>
		</div>
	</article>

<?php
}
?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>