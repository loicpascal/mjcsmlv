<?php
/**
 * Template Name: MJC - Page Recherche Activités
 */

get_header();

if (isset($_GET['acti'])) {
	$id_activite = $_GET['acti'];
	$activite = $wpdb->get_row( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id WHERE id=$id_activite" );
	$url_photo = ($activite->photo == "") ? "activite_neutre_trans$activite->id_tranche_age.jpg" : $activite->photo;
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title">Activité : <?php echo stripslashes($activite->nom) ?></h1>
		</header>
		<div class="entry-content">
			<div class="presentation">
				<p><?php echo stripslashes($activite->descriptif) ?></p>
				<div class='photo'>
					<img src='wp-content/uploads/activites/<?php echo $url_photo ?>' />
				</div>
				<div class='contenu'>
					<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $activite->jour_heure ?></p>
					<p><i class="fa fa-map-marker fa-1x" aria-hidden="true">&nbsp;</i>Lieu : <?php echo $activite->lieu_nom ?></p>
					<p><?php echo $activite->age ?></p>
					<p>Places : <?php echo $activite->nb_places ?></p>
					<p>Domaine : <?php echo $activite->domaine_nom ?></p>
					<p><i class="fa fa-user fa-1x" aria-hidden="true">&nbsp;</i>Intervenant : <?php echo $activite->intervenant_prenom ?>&nbsp;<?php echo $activite->intervenant_nom ?></p>
					<p>Tarif : <?php echo $activite->tarif ?> (selon QF)</p>
					<ul>
						<li>T1 : <?php echo $activite->t1 ?> €</li>
						<li>T2 : <?php echo $activite->t2 ?> €</li>
						<li>T3 : <?php echo $activite->t3 ?> €</li>
						<li>T4 : <?php echo $activite->t4 ?> €</li>
					</ul>
					<p><a href="<?php echo get_permalink(226); ?>"" title="MJC - Contact">Inscription à la MJC</a></p>
				</div>
			</div>
		</div>
	</article>
	<?php
}

else {

	// Si on recherche via un mot
	if (isset($_POST['recherche_activites_mots']) && !empty($_POST['recherche_activites_mots'])) {
		$rech = $_POST['recherche_activites_mots'];

		$row = $wpdb->get_row("SELECT * FROM mjc_activites_recherches WHERE recherche like '%$rech' AND recherche like '$rech%'");

		if (is_null($row)) {
			$wpdb->insert("mjc_activites_recherches", array(
	            'recherche' => $rech,
	            'nombre' => 1
	        ));
	    } else {
	    	$wpdb->update("mjc_activites_recherches", array(
                'nombre' => ($row->nombre+1)
                ),
                array( 'id' => $row->id)
            );
	    }

		$activites = $wpdb->get_results( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id
			WHERE nom LIKE '%$rech%'
			OR tranche_age_nom LIKE '%$rech%'
			OR intervenant_nom LIKE '%$rech%'" );

		$nb_activites = sizeof($activites);
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php echo $nb_activites; ?> activités trouvées pour "<?php echo $rech ?>"</h1>
			</header>
			<div class="entry-content page_recherche_activites">
				<form action="<?php echo get_permalink(20); ?>" method="post">
					<input type="text" name="recherche_activites_mots" placeholder="Tango, Photo, Boxe, Aqua Gym" />
					<input type="submit" value="Trouver !" />
				</form>
				<div class="listes liste_activites">
				<?php
				$i = "droite";
				foreach ($activites as $activite) {
					$i = ($i == "gauche") ? "doite" : "gauche";
					$lien = get_permalink($post->ID);
					$carac_url = (strstr($lien, "?")) ? "&" : "?";
					$url_photo = ($activite->photo == "") ? "activite_neutre_trans$activite->id_tranche_age.jpg" : $activite->photo;
					?>
					<a href="<?php echo $lien . $carac_url ?>acti=<?php echo $activite->id ?>">
						<div class="<?php echo $i ?>">
							<div class="cover"><div style="background-image: url(wp-content/uploads/activites/<?php echo $url_photo ?>)"></div></div>
							<div class="contenu">
								<h2><?php echo stripslashes($activite->nom) ?></h2>
								<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $activite->jour_heure ?></p>
								<p><?php echo $activite->tarif ?></p>
								<p><?php echo $activite->domaine_nom ?></p>
							</div>
						</div>
					</a>
				<?php } ?>

				</div>
			</div>
		</article><!-- #post -->

	<?php

	}
	// Si on recherche via un âge et un domaine
	elseif ((isset($_POST['mjc_recherche_activite_age']) && !empty($_POST['mjc_recherche_activite_age'])) && (isset($_POST['mjc_recherche_activite_domaine']) && !empty($_POST['mjc_recherche_activite_domaine']))) {

		$rech_age     = $_POST['mjc_recherche_activite_age'];
		$rech_domaine = $_POST['mjc_recherche_activite_domaine'];

		$activites = $wpdb->get_results( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id
			WHERE ((age_min <= $rech_age) AND (age_max >= $rech_age) OR (age_min = 0 AND age_max = 0)) AND (id_domaine = $rech_domaine)" );

		$nb_activites = sizeof($activites);

		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php echo $nb_activites ?> activité(s) trouvée(s) pour une personne de <span><?php echo $rech_age ?> ans</span> dans le domaine <?php echo $activites[0]->domaine_nom ?></h1>
			</header>
			<div class="entry-content page_recherche_activites">
				<form action="<?php echo get_permalink(20); ?>" method="post">
					<input type="text" name="recherche_activites_mots" placeholder="Tango, Photo, Boxe, Aqua Gym" />
					<input type="submit" value="Trouver !" />
				</form>
				<div class="listes liste_activites">
				<?php
				$i = "droite";
				foreach ($activites as $activite) {
					$i = ($i == "gauche") ? "doite" : "gauche";
					$lien = get_permalink($post->ID);
					$carac_url = (strstr($lien, "?")) ? "&" : "?";
					$url_photo = ($activite->photo == "") ? "activite_neutre_trans$activite->id_tranche_age.jpg" : $activite->photo;
					?>
					<a href="<?php echo $lien . $carac_url ?>acti=<?php echo $activite->id ?>">
						<div class="<?php echo $i ?>">
							<div class="cover"><div style="background-image: url(wp-content/uploads/activites/<?php echo $url_photo ?>)"></div></div>
							<div class="contenu">
								<h2><?php echo stripslashes($activite->nom) ?></h2>
								<p><i class="fa fa-calendar fa-1x" aria-hidden="true">&nbsp;</i><?php echo $activite->jour_heure ?></p>
								<p><?php echo $activite->tarif ?></p>
								<p><?php echo $activite->domaine_nom ?></p>
							</div>
						</div>
					</a>
				<?php } ?>

				</div>
			</div>
		</article><!-- #post -->

	<?php

	}
	// On n'a rien cherche
	else {

	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title">Trouvez votre activité</h1>
			</header>
			<div class="entry-content page_recherche_activites">
				<form action="<?php echo get_permalink(20); ?>" method="post">
					<input type="text" name="recherche_activites_mots" placeholder="Tango, photo, boxe, aquagym …" />
					<input type="submit" value="Trouver !" />
				</form>
			</div>


		</article><!-- #post -->

	<?php
	}
}
?>
<?php get_sidebar(); ?>
<?php get_footer(); ?>