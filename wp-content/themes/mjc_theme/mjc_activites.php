<?php
/**
 * Template Name: MJC - Page Activités
 */

$tranche = get_post_meta($post->ID, 'meta_tranche_age', true);
$activites = $wpdb->get_results( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id WHERE id_tranche_age=$tranche ORDER BY mjc_activites.nom, mjc_activites.jour_heure" );

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title">Activités <?php echo $activites[0]->tranche_age_nom ?></h1>
	</header>
	<div class="entry-content">
		<div class="listes liste_activites">
		<?php
		$i = "droite";
		foreach ($activites as $activite) {
			$i = ($i == "gauche") ? "doite" : "gauche";
			$lien = get_permalink(20);
			$carac_url = (strstr($lien, "?")) ? "&" : "?";
			$url_photo = ($activite->photo == "") ? "activite_neutre_trans$tranche.jpg" : $activite->photo;
			// $carac_url = "&";
					
			echo "<a href='" . $lien . $carac_url . "acti=$activite->id'>
				<div class='$i'>
					<div class='cover'><div style='background-image: url(wp-content/uploads/activites/$url_photo)'></div></div>
					<div class='contenu'>
						<h2>" . stripslashes($activite->nom) . "</h2>
						<p>$activite->jour_heure</p>
						<p>$activite->tarif</p>
						<p>$activite->domaine_nom</p>
					</div>
				</div>
			</a>";
		}
		?>
			
		</div>
	</div>


</article><!-- #post -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>