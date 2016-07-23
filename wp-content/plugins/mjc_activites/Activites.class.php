<?php

class Activites
{
    public $list_table;

    public function __construct()
    {
        // add_action( 'widgets_init' , function() {register_widget( 'NewsletterWidget' );});
        $actions = array('insert_activity','update_activity','delete_activity','action_intervenants','action_domaines','action_lieux');

        foreach ($actions as $action) {
            add_action('wp_loaded', array($this, $action));
        }

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_menu', array($this, 'add_admin_sub_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        

        if (!isset($_POST['mjc_activites_modifier_activite']) && (isset($_GET['id_activite']) && !empty($_GET['id_activite']))) {
            $_POST['mjc_activites_modifier_activite'] = $_GET['id_activite'];
        }
    }

    /**
     * Créé la table 'mjc_activites'
     * Elle est appelée lors de l'activation de l'extension avec la méthode register_activation_hook()
     * @return [type] [description]
     */
    public static function install()
    {
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS mjc_activites (
            id int(11) NOT NULL AUTO_INCREMENT,
            nom varchar(60) NOT NULL,
            nb_places int(11) NOT NULL,
            id_tranche_age varchar(60) NOT NULL,
            id_intervenant int(10) NOT NULL,
            id_domaine int(10) NOT NULL,
            age varchar(60) NOT NULL,
            age_min int(11) NOT NULL,
            age_max int(11) NOT NULL,
            tarif varchar(60) NOT NULL,
            t1 int(11) NOT NULL,
            t2 int(11) NOT NULL,
            t3 int(11) NOT NULL,
            t4 int(11) NOT NULL,
            jour_heure varchar(60) NOT NULL,
            id_lieu int(10) NOT NULL,
            photo varchar(60) NOT NULL,
            descriptif text NOT NULL,
            lien text NOT NULL,
            PRIMARY KEY (id)
        );");
    }

    /**
     * Supprime la table 'mjc_activites'
     * Elle est appelée lors de la désinstallation de l'extension
     * @return [type] [description]
     */
    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS mjc_activites;");
    }

    public function add_admin_menu()
    {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        $hook = add_menu_page('Activités', 'Activités', 'manage_options', 'mjc_activites', array($this, 'mjc_activites_liste_html'));
        add_action('load-'.$hook, array($this, 'process_action'));
    }

    public function add_admin_sub_menu()
    {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_submenu_page('mjc_activites', 'Ajouter', 'Ajouter', 'manage_options', 'mjc_activites_ajouter', array($this, 'mjc_activites_ajouter_html'));
        add_submenu_page('mjc_activites', 'Modifier', 'Modifier', 'manage_options', 'mjc_activites_modifier', array($this, 'mjc_activites_modifier_html'));
        add_submenu_page('mjc_activites', 'Supprimer', 'Supprimer', 'manage_options', 'mjc_activites_supprimer', array($this, 'mjc_activites_supprimer_html'));
        add_submenu_page('mjc_activites', 'Eléments', 'Eléments', 'manage_options', 'mjc_activites_elements', array($this, 'mjc_activites_elements_html'));
    }

    private function addJavascript() {
        echo '
        <script type="text/javascript" src="' . includes_url() . '/js/jquery/jquery-3.0.0.min.js"></script>
        <script type="text/javascript" src="' . plugins_url('',__FILE__) . '/js/main.js"></script>';
    }

    /**
     * Affichage du formulaire d'ajout d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_ajouter_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_activites_form_ajouter']) && !empty($_POST['mjc_activites_form_ajouter'])) {
            echo '<p style="color:#2ecc71">Activité ajoutée avec succès</p>';
        }
        echo '<hr/>';
        ?>
        <form method="post" action="" enctype="multipart/form-data">
        <?php settings_fields('mjc_activites_ajouter_settings') ?>
        <?php do_settings_sections('mjc_activites_ajouter_settings') ?>
        <input type="hidden" name="mjc_activites_form_ajouter" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        $this->addJavascript();
    }

    /**
     * Affichage du formulaire de modification d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_modifier_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_activites_form_modifier']) && !empty($_POST['mjc_activites_form_modifier'])) {
            echo '<p style="color:#27ae60">Activité modifiée avec succès</p>';
        }
        echo '<hr/>';
        echo "<p>Choisissez l'activité à modifier</p>";
        ?>
        <form method="post" action="">
            <?php
            $activites = $this->getAllActivitesOrderBy('nom');
            ?>
            <select name="mjc_activites_modifier_activite" onchange="this.form.submit()">
                <?php
                foreach ( $activites as $activite )
                {
                    echo '<option value="' . $activite->id . '"' . ((( isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite']) ) && ($_POST['mjc_activites_modifier_activite'] == $activite->id)) ? 'selected="selected"' : '') . '>' . trim(stripslashes($activite->nom . ' / ' . $activite->jour_heure . ' / ' . $activite->tranche_age_nom)) . '</option>';
                }
                ?>
            </select>
            <?php submit_button('Voir l\'activité'); ?>
        </form>

        <?php 
        if ( isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite']) ) {
        ?>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('mjc_activites_modifier_settings') ?>
            <?php do_settings_sections('mjc_activites_modifier_settings') ?>
            <input type="hidden" name="mjc_activites_form_modifier" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        }
        $this->addJavascript();
    }

    /**
     * Affichage du formulaire de suppression d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_supprimer_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_activites_form_supprimer']) && !empty($_POST['mjc_activites_form_supprimer'])) {
            echo '<p style="color:#2ecc71">Activité suprimée avec succès</p>';
        }
        echo '<hr/>';
        echo '<p>Choisissez l\'activité à supprimer</p>';
        ?>
        <form method="post" action="">
            <?php
            $activites = $this->getAllActivitesOrderBy('nom');
            ?>
            <select name="mjc_activites_supprimer_activite">
                <?php
                foreach ( $activites as $activite )
                {
                    echo '<option value="' . $activite->id . '"' . ((( isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite']) ) && ($_POST['mjc_activites_modifier_activite'] == $activite->id)) ? 'selected="selected"' : '') . '>' . trim(stripslashes($activite->nom . ' / ' . $activite->jour_heure . ' / ' . $activite->tranche_age_nom)) . '</option>';
                }
                ?>
            </select>
            <input type="hidden" name="mjc_activites_form_supprimer" value="1"/>
            <?php submit_button('Supprimer l\'activité'); ?>
        </form>

        <?php
    }

    /**
     * Affichage du formulaire de suppression d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_elements_html()
    {
        $intervenants = $this->getAllFromOrderBy('mjc_activites_intervenants', 'intervenant_nom');
        $domaines      = $this->getAllFromOrderBy('mjc_activites_domaines', 'domaine_nom');
        $lieux  = $this->getAllFromOrderBy('mjc_activites_lieux', 'lieu_nom');

        echo '<h1>'.get_admin_page_title().' : Modification / Suppression</h1>';
        if (isset($_POST['mjc_activites_form_elements_intervenants_supprimer']) && !empty($_POST['mjc_activites_form_elements_intervenants_supprimer'])) {
            echo '<p style="color:#2ecc71">Intervenant suprimé avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_domaines_supprimer']) && !empty($_POST['mjc_activites_form_elements_domaines_supprimer'])) {
            echo '<p style="color:#2ecc71">Domaine suprimé avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_lieux_supprimer']) && !empty($_POST['mjc_activites_form_elements_lieux_supprimer'])) {
            echo '<p style="color:#2ecc71">Lieu suprimé avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_intervenants_modifier']) && !empty($_POST['mjc_activites_form_elements_intervenants_modifier'])) {
            echo '<p style="color:#2ecc71">Intervenant modifié avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_domaines_modifier']) && !empty($_POST['mjc_activites_form_elements_domaines_modifier'])) {
            echo '<p style="color:#2ecc71">Domaine modifié avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_lieux_modifier']) && !empty($_POST['mjc_activites_form_elements_lieux_modifier'])) {
            echo '<p style="color:#2ecc71">Lieu modifié avec succès</p>';
        }
        echo '<hr/>';
        echo "<h3>Intervenants</h3>";
        echo "<p>Choisissez un intervenant à modifier</p>";

        ?>
        <form method="post" action="">
            <select name="mjc_activites_activite_elements_intervenants">
                <?php
                foreach ( $intervenants as $intervenant )
                {
                    echo '<option value="' . $intervenant->intervenant_id . '">' . $intervenant->intervenant_nom . ' ' . $intervenant->intervenant_prenom . '</option>';
                }
                ?>
            </select>
            <input type="text" name="mjc_activites_form_elements_intervenants_modif_nom"/>
            <input type="text" name="mjc_activites_form_elements_intervenants_modif_prenom"/>
            <input type="submit" name="button_modifier" value="Modifier"/>
            <input type="submit" value="Supprimer"/>
        </form>

        <h3>Domaines</h3>

        <form method="post" action="">
            <p>
                <select name="mjc_activites_activite_elements_domaines">
                    <?php
                    foreach ( $domaines as $domaine )
                    {
                        echo '<option value="' . $domaine->domaine_id . '">' . $domaine->domaine_nom . '</option>';
                    }
                    ?>
                </select>
                <input type="text" name="mjc_activites_form_elements_domaines_modif"/>
                <input type="submit" name="button_modifier" value="Modifier"/>
                <input type="submit" value="Supprimer"/>
            </p>
        </form>

        <h3>Lieux</h3>

        <form method="post" action="">
            <p>
                <select name="mjc_activites_activite_elements_lieux">
                    <?php
                    foreach ( $lieux as $lieu )
                    {
                        echo '<option value="' . $lieu->lieu_id . '">' . $lieu->lieu_nom . '</option>';
                    }
                    ?>
                </select>
                <input type="text" name="mjc_activites_form_elements_lieux_modif_nom"/>
                <input type="submit" name="button_modifier" value="Modifier"/>
                <input type="submit" value="Supprimer"/>
            </p>
        </form>

        <?php
    }

    /**
     * Affichage de la liste des activités (sous forme de tableau)
     * Correspond à la première page du plugin
     * @return [type] [description]
     */
    public function mjc_activites_liste_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Toutes les activités</p>';
        echo '<hr/>';
        $activites = $this->getAllActivitesOrderBy('nom');
        // print_r($activites);
        $entetes = array('N°','Nom','Nbre places','Tranche d\'âge','Intervenant','Domaine','Age','Tarif','Jour/Heure','Lieu');
        ?>
        <table class="widefat page fixed" cellspacing="0">
            <thead>
                <tr>
                <?php
                    foreach ($entetes as $entete) {
                        echo '<th scope="col" id="date" class="manage-column" style="">' . $entete . '</th>';
                    }
                ?>
                </tr>
            </thead>

            <tfoot>
                <tr>
                <?php
                    foreach ($entetes as $entete) {
                        echo '<th scope="col" id="date" class="manage-column" style="">' . $entete . '</th>';
                    }
                ?>
                </tr>
            </tfoot>
            <tbody>
                <?php
                foreach ( $activites as $activite )
                {
                ?>
                    <tr id="voy_">
                        <td >
                            <form method="get" action="">
                                <input type="hidden" name="page" value="mjc_activites_modifier" />
                                <input type="hidden" name="id_activite" value="<?php echo $activite->id; ?>" />
                                <input type="submit" value="<?php echo $activite->id; ?>" />
                            </form>
                        </td>
                        <td ><?php echo trim(stripslashes($activite->nom)); ?></td>
                        <td ><?php echo trim(stripslashes($activite->nb_places)); ?></td>
                        <td ><?php echo trim(stripslashes($activite->tranche_age_nom)); ?></td>
                        <td ><?php echo $activite->intervenant_nom; ?></td>
                        <td ><?php echo $activite->domaine_nom; ?></td>
                        <td ><?php echo trim(stripslashes($activite->age)); ?></td>
                        <td ><?php echo $activite->tarif; ?></td>
                        <td ><?php echo trim(stripslashes($activite->jour_heure)); ?></td>
                        <td ><?php echo $activite->lieu_nom; ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Enregistrement des paramètres pour les formulaires
     * @return [type] [description]
     */
    public function register_settings()
    {
        register_setting('mjc_activites_ajouter_settings', 'mjc_activites_ajouter_settings');
        register_setting('mjc_activites_modifier_settings', 'mjc_activites_modifier_settings');

        add_settings_section('mjc_activites_ajouter_section', 'Ajouter une activité', array($this, 'section_ajouter_html'), 'mjc_activites_ajouter_settings');
        add_settings_section('mjc_activites_modifier_section', 'Modifier une activité', array($this, 'section_modifier_html'), 'mjc_activites_modifier_settings');

        /** add_settings_field( $id, $title, $callback, $page, $section, $args ); **/
        add_settings_field('mjc_activites_id', 'N°', array($this, 'id_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_nom', 'Nom', array($this, 'nom_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_nb_places', 'Nombre de places', array($this, 'nb_places_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_tranche_age', 'Tranche d\'âge', array($this, 'tranche_age_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_intervenant', 'Intervenant', array($this, 'intervenant_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_domaine', 'Domaine', array($this, 'domaine_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_age_min', 'Age min.', array($this, 'age_min_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_age_max', 'Age max.', array($this, 'age_max_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_age', 'Age', array($this, 'age_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_t1', 'Tarif 1', array($this, 't1_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_t2', 'Tarif 2', array($this, 't2_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_t3', 'Tarif 3', array($this, 't3_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_t4', 'Tarif 4', array($this, 't4_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_tarif', 'Tarif', array($this, 'tarif_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_jour_heure', 'Jour/Heure', array($this, 'jour_heure_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_lieu', 'Lieu', array($this, 'lieu_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_photo', 'Photo', array($this, 'photo_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        add_settings_field('mjc_activites_descriptif', 'Descriptif', array($this, 'descriptif_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');


        add_settings_field('mjc_activites_id', 'N°', array($this, 'id_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_nom', 'Nom', array($this, 'nom_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_nb_places', 'Nombre de places', array($this, 'nb_places_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_tranche_age', 'Tranche d\'âge', array($this, 'tranche_age_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_intervenant', 'Intervenant', array($this, 'intervenant_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_domaine', 'Domaine', array($this, 'domaine_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_age_min', 'Age min.', array($this, 'age_min_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_age_max', 'Age max.', array($this, 'age_max_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_age', 'Age', array($this, 'age_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_t1', 'Tarif 1', array($this, 't1_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_t2', 'Tarif 2', array($this, 't2_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_t3', 'Tarif 3', array($this, 't3_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_t4', 'Tarif 4', array($this, 't4_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_tarif', 'Tarif', array($this, 'tarif_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_jour_heure', 'Jour/Heure', array($this, 'jour_heure_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_lieu', 'Lieu', array($this, 'lieu_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_photo', 'Photo', array($this, 'photo_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        add_settings_field('mjc_activites_descriptif', 'Descriptif', array($this, 'descriptif_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
    }

    public function section_ajouter_html() {
        echo 'Renseignez les informations sur l\'activité.';
    }

    public function section_modifier_html() {
        echo 'Modifiez les informations sur l\'activité.';  
    }

    /********************************************************************************************
     *** Affichage des champs du formulaire (avec valeur par défaut pour la modification)
     *******************************************************************************************/

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function id_html() {
        $default_value = $this->getDefaultValueFromById('id', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_id_temp" value="<?php echo $default_value ?>" disabled/>
        <input type="hidden" name="mjc_activites_id" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function nom_html() {
        $default_value = $this->getDefaultValueFromById('nom', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_nom" value="<?php echo trim(stripslashes($default_value)) ?>" required/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function nb_places_html() {
        $default_value = $this->getDefaultValueFromById('nb_places', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_nb_places" value="<?php echo trim(stripslashes($default_value)) ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function tranche_age_html() {
        $tranche_ages  = $this->getAllFromOrderBy('mjc_activites_tranches_ages', 'tranche_age_nom');
        $default_value = $this->getDefaultValueFromById('id_tranche_age', 'mjc_activites');
        ?>
        <select name="mjc_activites_tranche_age">
            <?php
            foreach ( $tranche_ages as $tranche_age ) 
            {
                echo '<option value="' . $tranche_age->tranche_age_id . '" ' . (($default_value == $tranche_age->tranche_age_id) ? 'selected="selected"' : '') . ' >' . $tranche_age->tranche_age_nom . '</option>';
            }
            ?>
        </select>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function intervenant_html()
    {
        $intervenants  = $this->getAllFromOrderBy('mjc_activites_intervenants', 'intervenant_prenom');
        $default_value = $this->getDefaultValueFromById('id_intervenant', 'mjc_activites');
        ?>
        <select name="mjc_activites_intervenant">
            <?php
            foreach ( $intervenants as $intervenant ) 
            {
                echo '<option value="' . $intervenant->intervenant_id . '" ' . (($default_value == $intervenant->intervenant_id) ? 'selected="selected"' : '') . ' >' . $intervenant->intervenant_prenom . " " . $intervenant->intervenant_nom . '</option>';
            }
            ?>
        </select>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function domaine_html()
    {
        $domaines      = $this->getAllFromOrderBy('mjc_activites_domaines', 'domaine_nom');
        $default_value = $this->getDefaultValueFromById('id_domaine', 'mjc_activites');
        ?>
        <select name="mjc_activites_domaine">
            <?php
            foreach ( $domaines as $domaine )
            {
                echo '<option value="' . $domaine->domaine_id . '" ' . (($default_value == $domaine->domaine_id) ? 'selected="selected"' : '') . ' >' . $domaine->domaine_nom . '</option>';
            }
            ?>
        </select>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function age_min_html() {
        $default_value = $this->getDefaultValueFromById('age_min', 'mjc_activites');
        ?>
        <input type="number" min="0" max="100" name="mjc_activites_age_min" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function age_max_html() {
        $default_value = $this->getDefaultValueFromById('age_max', 'mjc_activites');
        ?>
        <input type="number" min="0" max="100" name="mjc_activites_age_max" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function age_html() {
        $default_value = $this->getDefaultValueFromById('age', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_age" value="<?php echo trim(stripslashes($default_value)) ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t1_html() {
        $default_value = $this->getDefaultValueFromById('t1', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t1" value="<?php echo $default_value ?>"/>€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t2_html() {
        $default_value = $this->getDefaultValueFromById('t2', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t2" value="<?php echo $default_value ?>"/>€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t3_html() {
        $default_value = $this->getDefaultValueFromById('t3', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t3" value="<?php echo $default_value ?>"/>€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t4_html() {
        $default_value = $this->getDefaultValueFromById('t4', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t4" value="<?php echo $default_value ?>"/>€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function tarif_html() {
        $default_value = $this->getDefaultValueFromById('tarif', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_tarif" value="<?php echo trim(stripslashes($default_value)) ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function jour_heure_html() {
        $default_value = $this->getDefaultValueFromById('jour_heure', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_jour_heure" value="<?php echo trim(stripslashes($default_value)) ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function lieu_html() {
        $lieux  = $this->getAllFromOrderBy('mjc_activites_lieux', 'lieu_nom');
        $default_value = $this->getDefaultValueFromById('id_lieu', 'mjc_activites');
        ?>
        <select name="mjc_activites_lieu">
            <?php
            foreach ( $lieux as $lieu ) 
            {
                echo '<option value="' . $lieu->lieu_id . '" ' . (($default_value == $lieu->lieu_id) ? 'selected="selected"' : '') . ' >' . $lieu->lieu_nom . '</option>';
            }
            ?>
        </select>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function photo_html() {
        $default_value = $this->getDefaultValueFromById('photo', 'mjc_activites');
        echo "<p><img width='200px' alt='Photo' src='../wp-content/uploads/activites/" . $default_value . "' /></p>";
        ?>
        <input type="file" name="mjc_activites_photo"/>
        <input type="hidden" name="mjc_activites_photo_bak" value="<?php echo $default_value ?>"/>
        <?php
        $upload_dir = wp_upload_dir();
        $user_dirname = $upload_dir['basedir'];
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function descriptif_html() {
        $default_value = $this->getDefaultValueFromById('descriptif', 'mjc_activites');
        ?>
        <textarea name="mjc_activites_descriptif" cols="50" rows="8"><?php echo trim(stripslashes($default_value)) ?></textarea>
        <?php
    }

    /**
     * On vérifie si un des formulaires a été envoyé
     * @return [type] [description]
     */
    public function process_action()
    {
        if (isset($_POST['mjc_activites_form_ajouter'])) {
            $this->insert_activity();
        }
        if (isset($_POST['mjc_activites_form_modifier'])) {
            $this->update_activity();
        }
        if (isset($_POST['mjc_activites_form_supprimer'])) {
            $this->delete_activity();
        }

        // Eléments
        if (isset($_POST['mjc_activites_activite_elements_intervenants'])) {
            $this->action_intervenants();
        }
        if (isset($_POST['mjc_activites_activite_elements_domaines'])) {
            $this->action_domaines();
        }
        if (isset($_POST['mjc_activites_activite_elements_lieux'])) {
            $this->action_lieux();
        }
    }

    /**
     * Enregistre le mail du visiteur dans la table 'mjc_newsletter'
     * @return [type] [description]
     */
    public function insert_activity()
    {
        if (isset($_POST['mjc_activites_form_ajouter']) && !empty($_POST['mjc_activites_form_ajouter'])) {
            global $wpdb;

            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $nom = $_POST['mjc_activites_nom'];
            $nb_places = $_POST['mjc_activites_nb_places'];
            $tranche_age = $_POST['mjc_activites_tranche_age'];
            $intervenant = $_POST['mjc_activites_intervenant'];
            $domaine = $_POST['mjc_activites_domaine'];
            $age_min = $_POST['mjc_activites_age_min'];
            $age_max = $_POST['mjc_activites_age_max'];
            $age = $_POST['mjc_activites_age'];
            $t1 = $_POST['mjc_activites_t1'];
            $t2 = $_POST['mjc_activites_t2'];
            $t3 = $_POST['mjc_activites_t3'];
            $t4 = $_POST['mjc_activites_t4'];
            $tarif = $_POST['mjc_activites_tarif'];
            $jour_heure = $_POST['mjc_activites_jour_heure'];
            $lieu = $_POST['mjc_activites_lieu'];
            $descriptif = $_POST['mjc_activites_descriptif'];
            $photo = "";

            
            

            $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = '$nom' AND id_domaine = '$domaine' AND id_tranche_age='$tranche_age' AND age='$age' AND tarif='$tarif'");
            if (is_null($row)) {
                $wpdb->insert("mjc_activites", array(
                    'nom' => $nom,
                    'nb_places' => $nb_places,
                    'id_tranche_age' => $tranche_age,
                    'id_intervenant' => $intervenant,
                    'id_domaine' => $domaine,
                    'age_min' => $age_min,
                    'age_max' => $age_max,
                    'age' => $age,
                    't1' => $t1,
                    't2' => $t2,
                    't3' => $t3,
                    't4' => $t4,
                    'tarif' => $tarif,
                    'jour_heure' => $jour_heure,
                    'id_lieu' => $lieu,
                    'descriptif' => $descriptif,
                ));

                $id = $wpdb->insert_id;
            }

            if (isset($_FILES['mjc_activites_photo']) AND $_FILES['mjc_activites_photo']['error'] == 0) {
                require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
                $mjcFolderFile = new MjcFolderFile();
                $cheminComplet = $mjcFolderFile->createActiviteFolder() . "/";
                // $movefile = wp_handle_upload($_FILES['mjc_activites_photo'], $upload_overrides);

                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // Testons si le fichier n'est pas trop gros
                if ($_FILES['mjc_activites_photo']['size'] <= 5242880)
                {
                    // Testons si l'extension est autorisée
                    $infosfichier = pathinfo($_FILES['mjc_activites_photo']['name']);
                    $extension_upload = $infosfichier['extension'];
                    $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG');
                    if (in_array($extension_upload, $extensions_autorisees))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        require_once( ABSPATH . 'wp-includes/mjc/class/MjcString.class.php' );
                        $mjcString = new MjcString();
                        $nomFichier = $mjcString->getNomFichierPhoto($nom . $jour_heure . "_" . $id) . "." . strtolower($extension_upload);
                        $photo = date("Y") . "/" . date("m") . "/" . $nomFichier;

                        move_uploaded_file($_FILES['mjc_activites_photo']['tmp_name'], $cheminComplet . $nomFichier);
                        $wpdb->update("mjc_activites", array(
                            'photo' => $photo
                            ),
                            array( 'id' => $id)
                        );
                    }
                }
            }
        }
    }

    /**
     * Modifie l'activité
     * @return [type] [description]
     */
    public function update_activity()
    {
        if (isset($_POST['mjc_activites_form_modifier']) && !empty($_POST['mjc_activites_form_modifier'])) {
            global $wpdb;

            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $id = $_POST['mjc_activites_id'];
            $nom = $_POST['mjc_activites_nom'];
            $nb_places = $_POST['mjc_activites_nb_places'];
            $tranche_age = $_POST['mjc_activites_tranche_age'];
            $intervenant = $_POST['mjc_activites_intervenant'];
            $domaine = $_POST['mjc_activites_domaine'];
            $age_min = $_POST['mjc_activites_age_min'];
            $age_max = $_POST['mjc_activites_age_max'];
            $age = $_POST['mjc_activites_age'];
            $t1 = $_POST['mjc_activites_t1'];
            $t2 = $_POST['mjc_activites_t2'];
            $t3 = $_POST['mjc_activites_t3'];
            $t4 = $_POST['mjc_activites_t4'];
            $tarif = $_POST['mjc_activites_tarif'];
            $jour_heure = $_POST['mjc_activites_jour_heure'];
            $lieu = $_POST['mjc_activites_lieu'];
            $descriptif = $_POST['mjc_activites_descriptif'];
            $photo = "";

            // $upload_overrides = array( 'test_form' => false );

            if (isset($_FILES['mjc_activites_photo']) AND $_FILES['mjc_activites_photo']['error'] == 0) {
                require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
                $mjcFolderFile = new MjcFolderFile();
                $cheminComplet = $mjcFolderFile->createActiviteFolder() . "/";
                // $movefile = wp_handle_upload($_FILES['mjc_activites_photo'], $upload_overrides);

                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // Testons si le fichier n'est pas trop gros
                if ($_FILES['mjc_activites_photo']['size'] <= 5242880)
                {
                    // Testons si l'extension est autorisée
                    $infosfichier = pathinfo($_FILES['mjc_activites_photo']['name']);
                    $extension_upload = $infosfichier['extension'];
                    $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG');
                    if (in_array($extension_upload, $extensions_autorisees))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        require_once( ABSPATH . 'wp-includes/mjc/class/MjcString.class.php' );
                        $mjcString = new MjcString();
                        $nomFichier = $mjcString->getNomFichierPhoto($nom . $jour_heure . "_" . $id) . "." . strtolower($extension_upload);
                        $photo = date("Y") . "/" . date("m") . "/" . $nomFichier;

                        move_uploaded_file($_FILES['mjc_activites_photo']['tmp_name'], $cheminComplet . $nomFichier);
                    }
                }
            }
            else {
                $photo = $_POST['mjc_activites_photo_bak'];
            }
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = '$nom' AND id_domaine = '$domaine'");
            // print_r($row);
            // if (!is_null($row)) {
                $wpdb->update("mjc_activites", array(
                    'nom' => $nom,
                    'nb_places' => $nb_places,
                    'id_tranche_age' => $tranche_age,
                    'id_intervenant' => $intervenant,
                    'id_domaine' => $domaine,
                    'age_min' => $age_min,
                    'age_max' => $age_max,
                    'age' => $age,
                    't1' => $t1,
                    't2' => $t2,
                    't3' => $t3,
                    't4' => $t4,
                    'tarif' => $tarif,
                    'jour_heure' => $jour_heure,
                    'id_lieu' => $lieu,
                    'descriptif' => $descriptif,
                    'photo' => $photo,
                    ),
                    array( 'id' => $id)
                );
            // }
        }
    }

    /**
     * Supprime l'activité
     * @return [type] [description]
     */
    public function delete_activity()
    {
        if (isset($_POST['mjc_activites_form_supprimer']) && !empty($_POST['mjc_activites_form_supprimer'])) {
            global $wpdb;

            $id = $_POST['mjc_activites_supprimer_activite'];
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                $wpdb->delete("mjc_activites", array( 'id' => $id));
            // }
        }
    }















    /**
     * Modifie l'activité
     * @return [type] [description]
     */
    public function action_intervenants()
    {
        global $wpdb;
        if (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_form_elements_intervenants_modif_nom']) && !empty($_POST['mjc_activites_form_elements_intervenants_modif_nom']) && isset($_POST['mjc_activites_form_elements_intervenants_modif_prenom']) && !empty($_POST['mjc_activites_form_elements_intervenants_modif_prenom'])) {

            $id = $_POST['mjc_activites_activite_elements_intervenants'];
            $nom = $_POST['mjc_activites_form_elements_intervenants_modif_nom'];
            $prenom = $_POST['mjc_activites_form_elements_intervenants_modif_prenom'];

            $wpdb->update("mjc_activites_intervenants", array(
                    'intervenant_nom' => $nom,
                    'intervenant_prenom' => $prenom,
                    ),
                    array( 'intervenant_id' => $id)
                );

        } elseif (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_activite_elements_intervenants'])) {
            $id = $_POST['mjc_activites_activite_elements_intervenants'];
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                $wpdb->delete("mjc_activites_intervenants", array( 'intervenant_id' => $id));
            // }
        }
    }

    /**
     * Modifie l'activité
     * @return [type] [description]
     */
    public function action_domaines()
    {
        global $wpdb;
        if (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_form_elements_domaines_modif']) && !empty($_POST['mjc_activites_form_elements_domaines_modif'])) {

            $id = $_POST['mjc_activites_activite_elements_domaines'];
            $nom = $_POST['mjc_activites_form_elements_domaines_modif'];

            $wpdb->update("mjc_activites_domaines", array(
                    'domaine_nom' => $nom,
                    ),
                    array( 'domaine_id' => $id)
                );

        } elseif (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_activite_elements_domaines'])) {
            $id = $_POST['mjc_activites_activite_elements_domaines'];
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                $wpdb->delete("mjc_activites_domaines", array( 'domaine_id' => $id));
            // }
        }
    }

    /**
     * Modifie l'activité
     * @return [type] [description]
     */
    public function action_lieux()
    {
        global $wpdb;
        if (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_form_elements_lieux_modif_nom']) && !empty($_POST['mjc_activites_form_elements_lieux_modif_nom'])) {

            $id = $_POST['mjc_activites_activite_elements_lieux'];
            $nom = $_POST['mjc_activites_form_elements_lieux_modif_nom'];

            $wpdb->update("mjc_activites_lieux", array(
                    'lieu_nom' => $nom,
                    ),
                    array( 'lieu_id' => $id)
                );

        } elseif (isset($_POST['button_modifier']) && isset($_POST['mjc_activites_activite_elements_lieux'])) {
            $id = $_POST['mjc_activites_activite_elements_lieux'];
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                $wpdb->delete("mjc_activites_lieux", array( 'lieu_id' => $id));
            // }
        }
    }

    public function getAllFromOrderBy( $table , $order ) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY $order" );
    }

    public function getAllActivites() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id" );
    }

    public function getAllActivitesOrderBy($order) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_activites INNER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id INNER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id INNER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id INNER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id ORDER BY $order" );
    }

    public function getAllFromOrderByWhere( $table , $where ) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table WHERE $where" );
    }

    public function getFromById( $select , $table , $where ) {
        global $wpdb;
        return $wpdb->get_var( "SELECT $select FROM $table WHERE $where" );
    }

    public function getDefaultValueFromById( $name , $table ) {
        $res = "";
        if (isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite'])) {
            $res = $this->getFromById($name, $table, 'id=' . $_POST['mjc_activites_modifier_activite']);
        }
        return $res;
    }
}