<?php
require_once( ABSPATH . 'wp-includes/mjc/class/MjcParagraphe.class.php');

class Activites
{
    public function __construct()
    {
        $actions = array('insert_activity', 'update_activity', 'delete_activity', 'action_intervenants', 'action_domaines', 'action_lieux');

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

        $wpdb->query("CREATE TABLE IF NOT EXISTS `mjc_activites` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nom` varchar(60) NOT NULL,
            `nb_places` varchar(60) NOT NULL,
            `id_tranche_age` varchar(60) NOT NULL,
            `id_intervenant` int(60) NOT NULL,
            `id_domaine` int(60) NOT NULL,
            `age` varchar(60) NOT NULL,
            `age_min` int(11) NOT NULL,
            `age_max` int(11) NOT NULL,
            `tarif` varchar(60) NOT NULL,
            `t1` int(11) NOT NULL,
            `t2` int(11) NOT NULL,
            `t3` int(11) NOT NULL,
            `t4` int(11) NOT NULL,
            `jour_heure` varchar(60) NOT NULL,
            `id_lieu` varchar(60) NOT NULL,
            `photo` varchar(60) NOT NULL,
            `descriptif` text NOT NULL,
            PRIMARY KEY (`id`)
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
        add_submenu_page('mjc_activites', 'Eléments', 'Eléments', 'manage_options', 'mjc_activites_elements', array($this, 'mjc_activites_elements_html'));
    }

    private function addJSCSS() {
        // JS
        wp_register_script( 'jquery3', includes_url() . '/js/jquery/jquery-3.0.0.min.js');
        wp_enqueue_script('jquery3');
        wp_register_script( 'jquery_colorbox', includes_url() . '/js/jquery.colorbox-min.js');
        wp_enqueue_script('jquery_colorbox');
        wp_register_script( 'custom_jquery', plugins_url('/js/main.js', __FILE__));
        wp_enqueue_script('custom_jquery');
        // CSS
        wp_register_style( 'font-awesome', includes_url() . '/css/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('font-awesome');
        wp_register_style( 'custom_css', includes_url() . '/css/mjc/custom_css.css');
        wp_enqueue_style('custom_css');
        wp_register_style( 'colorbox', includes_url() . '/css/mjc/colorbox.css');
        wp_enqueue_style('colorbox');
    }

    /**
     * Affichage du formulaire d'ajout d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_ajouter_html()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";
        if (isset($_POST['mjc_activites_form_ajouter']) && !empty($_POST['mjc_activites_form_ajouter'])) {
            echo '<p style="color:#2ecc71">Activité ajoutée avec succès</p>';
        }
        ?>
        <hr/>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('mjc_activites_ajouter_settings') ?>
            <?php do_settings_sections('mjc_activites_ajouter_settings') ?>
        <input type="hidden" name="mjc_activites_form_ajouter" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        $this->addJSCSS();
    }

    /**
     * Affichage du formulaire de modification d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_modifier_html()
    {
        echo "<h1>" . get_admin_page_title() . "</h1>";
        ?>
        <?php if (isset($_POST['mjc_activites_form_modifier']) && !empty($_POST['mjc_activites_form_modifier'])) { ?>
            <p style="color:#27ae60">Activité modifiée avec succès</p>
        <?php } ?>
        <hr/>
        <p>Choisissez l'activité à modifier</p>
        <form method="post" action="">
            <?php
            $activites = $this->getAllActivitesOrderBy('nom');
            ?>
            <select name="mjc_activites_modifier_activite" onchange="this.form.submit()">
                <?php
                foreach ($activites as $activite) {
                    echo '<option value="' . $activite->id . '"' . ((( isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite']) ) && ($_POST['mjc_activites_modifier_activite'] == $activite->id)) ? 'selected="selected"' : '') . '>' . MjcParagraphe::epure($activite->nom . ' / ' . $activite->jour_heure . ' / ' . $activite->tranche_age_nom) . '</option>';
                }
                ?>
            </select>
            <?php submit_button('Voir l\'activité'); ?>
        </form>

        <?php 
        if (isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite'])) {
        ?>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('mjc_activites_modifier_settings') ?>
            <?php do_settings_sections('mjc_activites_modifier_settings') ?>
            <input type="hidden" name="mjc_activites_form_modifier" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        }
        $this->addJSCSS();
    }

    /**
     * Affichage du formulaire de suppression d'une activité
     * @return [type] [description]
     */
    public function mjc_activites_elements_html()
    {
        // Suppression
        if (isset($_POST['mjc_activites_form_elements_intervenants_supprimer'])) {
            echo '<p style="color:#2ecc71">Intervenant supprimé avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_domaines_supprimer'])) {
            echo '<p style="color:#2ecc71">Domaine supprimé avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_lieux_supprimer'])) {
            echo '<p style="color:#2ecc71">Lieu supprimé avec succès</p>';
        }
        // Modification
        if (isset($_POST['mjc_activites_form_elements_intervenants_modifier'])) {
            echo '<p style="color:#2ecc71">Intervenant modifié avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_domaines_modifier'])) {
            echo '<p style="color:#2ecc71">Domaine modifié avec succès</p>';
        }
        if (isset($_POST['mjc_activites_form_elements_lieux_modifier'])) {
            echo '<p style="color:#2ecc71">Lieu modifié avec succès</p>';
        }

        $intervenants = $this->getAllFromOrderBy('mjc_activites_intervenants', 'intervenant_nom');
        ?>
        <h1>Intervenants, domaines, lieux</h1>
        <hr/>

        <!-- Intervenants -->
        <h3 class="accordeon">Intervenant <span class="fa fa-chevron-up" aria-hidden="true" data-sens="up"></span></h3>
        <table class="widefat page fixed p_activites" cellspacing="0" style="width:auto;">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $intervenants as $intervenant )
                {
                ?>
                    <tr>
                        <form action="" method="post">
                            <input type="hidden" name="elements_intervenant_modif" value="1">
                            <input type="hidden" name="mjc_activites_f_intervenant_id" value="<?php echo $intervenant->intervenant_id; ?>">
                            <td><input type="text" name="mjc_activites_f_intervenant_nom" value="<?php echo MjcParagraphe::epure($intervenant->intervenant_nom); ?>"></td>
                            <td ><input type="text" name="mjc_activites_f_intervenant_prenom" value="<?php echo MjcParagraphe::epure($intervenant->intervenant_prenom); ?>"></td>
                            <td style="text-align: center;">
                                <input type="submit" value="Enregistrer">
                                <a class="js-confirm" title="Supprimer" data-confirm="supprimer l'intervenant '<?php echo MjcParagraphe::epure($intervenant->intervenant_nom) . " " . MjcParagraphe::epure($intervenant->intervenant_prenom); ?>'" href="?page=mjc_activites_elements&mjc_activites_supprimer_intervenant=<?php echo $intervenant->intervenant_id; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                                </a>
                            </td>
                        </form>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Domaines -->
        <?php
            $domaines = $this->getAllFromOrderBy('mjc_activites_domaines', 'domaine_nom');
        ?>
        <h3 class="accordeon">Domaines <span class="fa fa-chevron-up" aria-hidden="true" data-sens="up"></span></h3>
        <table class="widefat page fixed p_activites" cellspacing="0" style="width:auto;">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Nom</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($domaines as $domaine)
                {
                ?>
                    <tr>
                        <form action="" method="post">
                            <input type="hidden" name="elements_domaine_modif" value="1">
                            <input type="hidden" name="mjc_activites_f_domaine_id" value="<?php echo $domaine->domaine_id; ?>">
                            <td><input type="text" name="mjc_activites_f_domaine_nom" value="<?php echo MjcParagraphe::epure($domaine->domaine_nom); ?>"></td>
                            <td style="text-align: center;">
                                <input type="submit" value="Enregistrer">
                                <a class="js-confirm" title="Supprimer" data-confirm="supprimer le domaine '<?php echo MjcParagraphe::epure($domaine->domaine_nom); ?>'" href="?page=mjc_activites_elements&mjc_activites_supprimer_domaine=<?php echo $domaine->domaine_id; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                                </a>
                            </td>
                        </form>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        
        <!-- Lieux -->
        <?php
            $lieux  = $this->getAllFromOrderBy('mjc_activites_lieux', 'lieu_nom');
        ?>
        <h3 class="accordeon">Lieux <span class="fa fa-chevron-up" aria-hidden="true" data-sens="up"></span></h3>
        <table class="widefat page fixed p_activites" cellspacing="0" style="width:auto;">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Nom</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lieux as $lieu)
                {
                ?>
                    <tr>
                        <td style="text-align: center;">
                            <a class="js-confirm" title="Supprimer" data-confirm="supprimer l'activité '<?php echo MjcParagraphe::epure($lieu->lieu_nom); ?>'" href="?page=mjc_activites_elements&mjc_activites_form_supprimer=1&mjc_activites_supprimer_activite=<?php echo $lieu->lieu_id; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                            </a>&nbsp;&nbsp;
                            <a title="Modifier" href="?page=mjc_activites_modifier&id_activite=<?php echo $lieu->lieu_id; ?>"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td><?php echo MjcParagraphe::epure($lieu->lieu_nom); ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <!--
        <form method="post" action="">
            <p>
                <select name="mjc_activites_activite_elements_lieux">
                    <?php
                    foreach ($lieux as $lieu)
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
        -->

        <?php
        $this->addJSCSS();
    }

    /**
     * Affichage de la liste des activités (sous forme de tableau)
     * Correspond à la première page du plugin
     * @return [type] [description]
     */
    public function mjc_activites_liste_html()
    {
        ?>
        <h1><?php echo get_admin_page_title(); ?></h1>
        <?php if (isset($_GET['mjc_activites_form_supprimer']) && !empty($_GET['mjc_activites_form_supprimer'])) { ?>
            <p style="color:#2ecc71">Activité suprimée avec succès</p>
        <?php } ?>
        <?php
        $activites = $this->getAllActivitesOrderBy('nom');
        // print_r($activites);
        $entetes = array('Actions', 'Nom','Nbre places','Tranche d\'âge','Intervenant','Domaine','Age','Tarif','Jour/Heure','Lieu', 'Photo'); ?>
        <p>Toutes les activités (<?php echo sizeof($activites); ?>)</p>
        <hr/>
        <table class="widefat page fixed p_activites" cellspacing="0">
            <thead>
                <tr>
                <?php
                    foreach ($entetes as $entete) {
                        echo '<th>' . $entete . '</th>';
                    }
                ?>
                </tr>
            </thead>

            <tfoot>
                <tr>
                <?php
                    foreach ($entetes as $entete) {
                        echo '<th>' . $entete . '</th>';
                    }
                ?>
                </tr>
            </tfoot>
            <tbody>
                <?php
                foreach ($activites as $activite)
                {
                ?>
                    <tr>
                        <td style="text-align: center;">
                            <a class="js-confirm" title="Supprimer" data-confirm="supprimer l'activité '<?php echo MjcParagraphe::epure($activite->nom); ?>'" href="?page=mjc_activites&mjc_activites_form_supprimer=1&mjc_activites_supprimer_activite=<?php echo $activite->id; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                            </a>&nbsp;&nbsp;
                            <a title="Modifier" href="?page=mjc_activites_modifier&id_activite=<?php echo $activite->id; ?>"><i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td><?php echo MjcParagraphe::epure($activite->nom); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->nb_places); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->tranche_age_nom); ?></td>
                        <td style="text-align: center;">
                        <?php if ($activite->intervenant_nom == "") { ?>
                            <span title="Intervenant absent" style="color: #c9302c"><i class="fa fa-times fa-2x" aria-hidden="true"></i></span>
                        <?php } else { ?>
                            <?php echo MjcParagraphe::epure($activite->intervenant_nom); ?>
                        <?php } ?>
                        </td>
                        <td ><?php echo MjcParagraphe::epure($activite->domaine_nom); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->age); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->tarif); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->jour_heure); ?></td>
                        <td ><?php echo MjcParagraphe::epure($activite->lieu_nom); ?></td>
                        <td >
                        <?php if ($activite->photo == "") { ?>
                            <span title="Photo absente" style="color: #c9302c"><i class="fa fa-times fa-2x" aria-hidden="true"></i></span>
                        <?php } else { ?>
                            <span title="Photo présente" style="color: #5cb85c"><i class="fa fa-check fa-2x" aria-hidden="true"></i></span>
                        <?php } ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php
        $this->addJSCSS();
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

        $settings_fields_liste = array(
            'id' => 'N°',
            'nom' => 'Nom',
            'nb_places' => 'Nombre de places',
            'tranche_age' => 'Tranche d\'âge',
            'intervenant' => 'Intervenant',
            'domaine' => 'Domaine',
            'age_min' => 'Age min.',
            'age_max' => 'Age max.',
            'age' => 'Age',
            't1' => 'Tarif 1',
            't2' => 'Tarif 2',
            't3' => 'Tarif 3',
            't4' => 'Tarif 4',
            'tarif' => 'Tarif',
            'jour_heure' => 'Jour/Heure',
            'lieu' => 'Lieu',
            'photo' => 'Photo',
            'descriptif' => 'Descriptif'
        );

        // Éléments du formulaire d'ajout
        foreach ($settings_fields_liste as $key => $value) {
            // add_settings_field( $id, $title, $callback, $page, $section, $args );
            add_settings_field('mjc_activites_' . $key, $value, array($this, $key . '_html'), 'mjc_activites_ajouter_settings', 'mjc_activites_ajouter_section');
        }

        // Éléments du formulaire de modification
        foreach ($settings_fields_liste as $key => $value) {
            add_settings_field('mjc_activites_' . $key, $value, array($this, $key . '_html'), 'mjc_activites_modifier_settings', 'mjc_activites_modifier_section');
        }
    }

    public function section_ajouter_html()
    {
        echo 'Renseignez les informations sur l\'activité.';
    }

    public function section_modifier_html()
    {
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
    public function id_html()
    {
        $default_value = $this->getDefaultValueFromById('id', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_id_temp" value="<?php echo $default_value ?>" disabled>
        <input type="hidden" name="mjc_activites_id" value="<?php echo $default_value ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function nom_html()
    {
        $default_value = $this->getDefaultValueFromById('nom', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_nom" value="<?php echo MjcParagraphe::epure($default_value) ?>" required>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function nb_places_html()
    {
        $default_value = $this->getDefaultValueFromById('nb_places', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_nb_places" value="<?php echo MjcParagraphe::epure($default_value) ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function tranche_age_html()
    {
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
                echo '<option value="' . $intervenant->intervenant_id . '" ' . (($default_value == $intervenant->intervenant_id) ? 'selected="selected"' : '') . ' >' . MjcParagraphe::epure($intervenant->intervenant_prenom . " " . $intervenant->intervenant_nom) . '</option>';
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
    public function age_min_html()
    {
        $default_value = $this->getDefaultValueFromById('age_min', 'mjc_activites');
        ?>
        <input type="number" min="0" max="100" name="mjc_activites_age_min" value="<?php echo $default_value ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function age_max_html()
    {
        $default_value = $this->getDefaultValueFromById('age_max', 'mjc_activites');
        ?>
        <input type="number" min="0" max="100" name="mjc_activites_age_max" value="<?php echo $default_value ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function age_html()
    {
        $default_value = $this->getDefaultValueFromById('age', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_age" value="<?php echo MjcParagraphe::epure($default_value) ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t1_html()
    {
        $default_value = $this->getDefaultValueFromById('t1', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t1" value="<?php echo $default_value ?>">€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t2_html()
    {
        $default_value = $this->getDefaultValueFromById('t2', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t2" value="<?php echo $default_value ?>">€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t3_html()
    {
        $default_value = $this->getDefaultValueFromById('t3', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t3" value="<?php echo $default_value ?>">€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function t4_html()
    {
        $default_value = $this->getDefaultValueFromById('t4', 'mjc_activites');
        ?>
        <input type="number" min="0" max="1000" name="mjc_activites_t4" value="<?php echo $default_value ?>">€
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function tarif_html()
    {
        $default_value = $this->getDefaultValueFromById('tarif', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_tarif" value="<?php echo MjcParagraphe::epure($default_value) ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function jour_heure_html()
    {
        $default_value = $this->getDefaultValueFromById('jour_heure', 'mjc_activites');
        ?>
        <input type="text" name="mjc_activites_jour_heure" value="<?php echo MjcParagraphe::epure($default_value) ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function lieu_html()
    {
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
    public function photo_html()
    {
        $default_value = $this->getDefaultValueFromById('photo', 'mjc_activites');
        echo "<p id='photo_activite_preview'><img id='photo_activite' height='100px' alt='Photo' src='../wp-content/uploads/activites/" . $default_value . "' /></p><p id='photo_activite_taille'></p>";
        ?>
        <input type="file" name="mjc_activites_photo" id="mjc_activites_photo">
        <input type="hidden" name="mjc_activites_photo_bak" value="<?php echo $default_value ?>">
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'activité
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function descriptif_html()
    {
        $default_value = $this->getDefaultValueFromById('descriptif', 'mjc_activites');
        ?>
        <textarea name="mjc_activites_descriptif" cols="50" rows="8"><?php echo MjcParagraphe::epure($default_value) ?></textarea>
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
        if (isset($_GET['mjc_activites_form_supprimer'])) {
            $this->delete_activity();
        }

        // Eléments
        if (isset($_POST['elements_intervenant_modif'])) {
            $this->action_intervenants();
        }
        if (isset($_POST['elements_domaine_modif'])) {
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

            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $nom         = $_POST['mjc_activites_nom'];
            $nb_places   = $_POST['mjc_activites_nb_places'];
            $tranche_age = $_POST['mjc_activites_tranche_age'];
            $intervenant = $_POST['mjc_activites_intervenant'];
            $domaine     = $_POST['mjc_activites_domaine'];
            $age_min     = $_POST['mjc_activites_age_min'];
            $age_max     = $_POST['mjc_activites_age_max'];
            $age         = $_POST['mjc_activites_age'];
            $t1          = $_POST['mjc_activites_t1'];
            $t2          = $_POST['mjc_activites_t2'];
            $t3          = $_POST['mjc_activites_t3'];
            $t4          = $_POST['mjc_activites_t4'];
            $tarif       = $_POST['mjc_activites_tarif'];
            $jour_heure  = $_POST['mjc_activites_jour_heure'];
            $lieu        = $_POST['mjc_activites_lieu'];
            $descriptif  = $_POST['mjc_activites_descriptif'];
            $photo       = "";

            $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = '$nom' AND id_domaine = '$domaine' AND id_tranche_age='$tranche_age' AND age='$age' AND tarif='$tarif' AND jour_heure='$jour_heure'");
            if (is_null($row)) {
                $wpdb->insert("mjc_activites", array(
                    'nom'            => $nom,
                    'nb_places'      => $nb_places,
                    'id_tranche_age' => $tranche_age,
                    'id_intervenant' => $intervenant,
                    'id_domaine'     => $domaine,
                    'age_min'        => $age_min,
                    'age_max'        => $age_max,
                    'age'            => $age,
                    't1'             => $t1,
                    't2'             => $t2,
                    't3'             => $t3,
                    't4'             => $t4,
                    'tarif'          => $tarif,
                    'jour_heure'     => $jour_heure,
                    'id_lieu'        => $lieu,
                    'descriptif'     => $descriptif,
                ));

                $id = $wpdb->insert_id;
            }

            if (isset($_FILES['mjc_activites_photo']) AND $_FILES['mjc_activites_photo']['error'] == 0) {
                require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
                $mjcFolderFile = new MjcFolderFile();
                $cheminComplet = $mjcFolderFile->createActiviteFolder() . "/";

                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // Testons si le fichier n'est pas trop gros
                if ($_FILES['mjc_activites_photo']['size'] <= 5242880)
                {
                    // Testons si l'extension est autorisée
                    $infosfichier = pathinfo($_FILES['mjc_activites_photo']['name']);
                    $extension_upload = $infosfichier['extension'];
                    if (in_array(strtoupper($extension_upload), array('JPG', 'JPEG', 'GIF', 'PNG')))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        $nomFichier = MjcPragraphe::getNomFichierPhoto($nom . $jour_heure . "_" . $id) . "." . strtolower($extension_upload);
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

            if (! function_exists( 'wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            $id          = $_POST['mjc_activites_id'];
            $nom         = $_POST['mjc_activites_nom'];
            $nb_places   = $_POST['mjc_activites_nb_places'];
            $tranche_age = $_POST['mjc_activites_tranche_age'];
            $intervenant = $_POST['mjc_activites_intervenant'];
            $domaine     = $_POST['mjc_activites_domaine'];
            $age_min     = $_POST['mjc_activites_age_min'];
            $age_max     = $_POST['mjc_activites_age_max'];
            $age         = $_POST['mjc_activites_age'];
            $t1          = $_POST['mjc_activites_t1'];
            $t2          = $_POST['mjc_activites_t2'];
            $t3          = $_POST['mjc_activites_t3'];
            $t4          = $_POST['mjc_activites_t4'];
            $tarif       = $_POST['mjc_activites_tarif'];
            $jour_heure  = $_POST['mjc_activites_jour_heure'];
            $lieu        = $_POST['mjc_activites_lieu'];
            $descriptif  = $_POST['mjc_activites_descriptif'];
            $photo       = "";

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
                    if (in_array(strtoupper($extension_upload), array('JPG', 'JPEG', 'GIF', 'PNG')))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        $nomFichier = MjcParagraphe::getNomFichierPhoto($nom . $jour_heure . "_" . $id) . "." . strtolower($extension_upload);
                        $photo = date("Y") . "/" . date("m") . "/" . $nomFichier;

                        move_uploaded_file($_FILES['mjc_activites_photo']['tmp_name'], $cheminComplet . $nomFichier);
                    }
                }
            } else {
                $photo = $_POST['mjc_activites_photo_bak'];
            }
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = '$nom' AND id_domaine = '$domaine'");
            // print_r($row);
            // if (!is_null($row)) {
                $wpdb->update("mjc_activites", array(
                    'nom'            => $nom,
                    'nb_places'      => $nb_places,
                    'id_tranche_age' => $tranche_age,
                    'id_intervenant' => $intervenant,
                    'id_domaine'     => $domaine,
                    'age_min'        => $age_min,
                    'age_max'        => $age_max,
                    'age'            => $age,
                    't1'             => $t1,
                    't2'             => $t2,
                    't3'             => $t3,
                    't4'             => $t4,
                    'tarif'          => $tarif,
                    'jour_heure'     => $jour_heure,
                    'id_lieu'        => $lieu,
                    'descriptif'     => $descriptif,
                    'photo'          => $photo,
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
        if (isset($_GET['mjc_activites_form_supprimer']) && !empty($_GET['mjc_activites_form_supprimer'])) {
            global $wpdb;

            $id = $_GET['mjc_activites_supprimer_activite'];
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                $wpdb->delete("mjc_activites", array( 'id' => $id));
            // }
        }
    }

    /**
     * Modifie l'intervenant
     * @return [type] [description]
     */
    public function action_intervenants()
    {
        global $wpdb;
        # Modification
        if (isset($_POST['elements_intervenant_modif'])) {

            $id     = $_POST['mjc_activites_f_intervenant_id'];
            $nom    = $_POST['mjc_activites_f_intervenant_nom'];
            $prenom = $_POST['mjc_activites_f_intervenant_prenom'];

            $wpdb->update("mjc_activites_intervenants", array(
                    'intervenant_nom' => $nom,
                    'intervenant_prenom' => $prenom,
                    ),
                    array( 'intervenant_id' => $id)
                );
        }

        # Suppresion
        if (isset($_GET['mjc_activites_supprimer_intervenant'])) {
            $id = $_GET['mjc_activites_supprimer_intervenant'];

            // Récupération des activités correspondantes
            $actis = $this->getActiByIntervenant($id);
            // $date = date("Y-m-d H:i:s");

            $row = $wpdb->get_row("SELECT * FROM mjc_activites_intervenants WHERE intervenant_id = $id");

            if (!is_null($row)) {
                $wpdb->delete("mjc_activites_intervenants", array( 'intervenant_id' => $id));
            }
        }
    }

    /**
     * Modifie le domaine
     * @return [type] [description]
     */
    public function action_domaines()
    {
        global $wpdb;
        # Modification
        if (isset($_POST['elements_domaine_modif'])) {

            $id     = $_POST['mjc_activites_f_domaine_id'];
            $nom    = $_POST['mjc_activites_f_domaine_nom'];

            $wpdb->update("mjc_activites_domaines", array(
                    'domaine_nom' => $nom,
                    ),
                    array( 'domaine_id' => $id)
                );
        }

        # Suppresion
        if (isset($_GET['mjc_activites_supprimer_domaine'])) {
            $id = $_GET['mjc_activites_supprimer_domaine'];

            // Récupération des activités correspondantes
            $actis = $this->getActiByDomaine($id);
            // $date = date("Y-m-d H:i:s");

            // $row = $wpdb->get_row("SELECT * FROM mjc_activites WHERE nom = $id");

            // if (!is_null($row)) {
                // $wpdb->delete("mjc_activites_intervenants", array( 'intervenant_id' => $id));
            // }
        }
    }

    /**
     * Modifie le lieux
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

    /**
     *
     *  Méthodes Bases de données
     * 
     */

    public function getAllFromOrderBy( $table , $order )
    {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY $order" );
    }

    public function getAllActivitesOrderBy($order)
    {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_activites LEFT OUTER JOIN mjc_activites_domaines ON mjc_activites.id_domaine = mjc_activites_domaines.domaine_id LEFT OUTER JOIN mjc_activites_intervenants ON mjc_activites.id_intervenant = mjc_activites_intervenants.intervenant_id LEFT OUTER JOIN mjc_activites_tranches_ages ON mjc_activites.id_tranche_age = mjc_activites_tranches_ages.tranche_age_id LEFT OUTER JOIN mjc_activites_lieux ON mjc_activites.id_lieu = mjc_activites_lieux.lieu_id ORDER BY $order" );
    }

    public function getAllFromOrderByWhere( $table , $where )
    {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table WHERE $where" );
    }

    public function getFromById( $select , $table , $where )
    {
        global $wpdb;
        return $wpdb->get_var( "SELECT $select FROM $table WHERE $where" );
    }

    public function getDefaultValueFromById( $name , $table )
    {
        $res = "";
        if (isset($_POST['mjc_activites_modifier_activite']) && !empty($_POST['mjc_activites_modifier_activite'])) {
            $res = $this->getFromById($name, $table, 'id=' . $_POST['mjc_activites_modifier_activite']);
        }
        return $res;
    }

    public function getActiByIntervenant( $id_intervenant )
    {
        global $wpdb;
        return $wpdb->get_var( "SELECT * FROM mjc_activites WHERE id_intervenant = $id_intervenant" );
    }

    public function getActiByDomaine( $id_domaine )
    {
        global $wpdb;
        return $wpdb->get_var( "SELECT * FROM mjc_activites WHERE id_domaine = $id_domaine" );
    }
}
