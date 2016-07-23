<?php

class Actus
{
    public $list_table;

    public function __construct()
    {
        // add_action( 'widgets_init' , function() {register_widget( 'NewsletterWidget' );});

        add_action('wp_loaded', array($this, 'insert_actu'));
        add_action('wp_loaded', array($this, 'update_actu'));
        add_action('wp_loaded', array($this, 'delete_actu'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_menu', array($this, 'add_admin_sub_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        if (!isset($_POST['mjc_actus_modifier_actus']) && (isset($_GET['id_actus']) && !empty($_GET['id_actus']))) {
            $_POST['mjc_actus_modifier_actus'] = $_GET['id_actus'];
        }
    }

    /**
     * Créé la table 'mjc_actus'
     * Elle est appelée lors de l'activation de l'extension avec la méthode register_activation_hook()
     * @return [type] [description]
     */
    public static function install()
    {
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS mjc_actus (
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
     * Supprime la table 'mjc_actus'
     * Elle est appelée lors de la désinstallation de l'extension
     * @return [type] [description]
     */
    public static function uninstall()
    {
        global $wpdb;

        $wpdb->query("DROP TABLE IF EXISTS mjc_actus;");
    }

    public function add_admin_menu()
    {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        $hook = add_menu_page('Actus', 'Actus', 'manage_options', 'mjc_actus', array($this, 'mjc_actus_liste_html'));
        add_action('load-'.$hook, array($this, 'process_action'));
    }

    public function add_admin_sub_menu()
    {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_submenu_page('mjc_actus', 'Ajouter', 'Ajouter', 'manage_options', 'mjc_actus_ajouter', array($this, 'mjc_actus_ajouter_html'));
        add_submenu_page('mjc_actus', 'Modifier', 'Modifier', 'manage_options', 'mjc_actus_modifier', array($this, 'mjc_actus_modifier_html'));
        add_submenu_page('mjc_actus', 'Supprimer', 'Supprimer', 'manage_options', 'mjc_actus_supprimer', array($this, 'mjc_actus_supprimer_html'));
    }

    private function addJavascript() {
        echo '
        <script type="text/javascript" src="' . includes_url() . '/js/jquery/jquery-3.0.0.min.js"></script>
        <script type="text/javascript" src="' . plugins_url('',__FILE__) . '/js/main.js"></script>';
    }

    /**
     * Affichage du formulaire d'ajout d'une actu
     * @return [type] [description]
     */
    public function mjc_actus_ajouter_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_actus_form_ajouter']) && !empty($_POST['mjc_actus_form_ajouter'])) {
            echo '<p style="color:#2ecc71">Actu ajoutée avec succès</p>';
        }
        echo '<hr/>';
        ?>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('mjc_actus_ajouter_settings') ?>
            <?php do_settings_sections('mjc_actus_ajouter_settings') ?>
            <input type="hidden" name="mjc_actus_form_ajouter" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        $this->addJavascript();
    }

    /**
     * Affichage du formulaire de modification d'une actu
     * @return [type] [description]
     */
    public function mjc_actus_modifier_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_actus_form_modifier']) && !empty($_POST['mjc_actus_form_modifier'])) {
            echo '<p style="color:#2ecc71">Actu modifiée avec succès</p>';
        }
        echo '<hr/>';
        echo "<p>Choisissez l'actu à modifier</p>";
        ?>
        <form method="post" action="">
            <?php
            $actuss = $this->getAllactussOrderBy('nom');
            ?>
            <select name="mjc_actus_modifier_actus" onchange="this.form.submit()">
                <?php
                foreach ( $actuss as $actus )
                {
                    echo '<option value="' . $actus->id . '"' . ((( isset($_POST['mjc_actus_modifier_actus']) && !empty($_POST['mjc_actus_modifier_actus']) ) && ($_POST['mjc_actus_modifier_actus'] == $actus->id)) ? 'selected="selected"' : '') . '>' . trim(stripslashes($actus->nom . ' / ' . $actus->jour_heure)) . '</option>';
                }
                ?>
            </select>
            <?php submit_button('Voir l\'actu'); ?>
        </form>

        <?php 
        if ( isset($_POST['mjc_actus_modifier_actus']) && !empty($_POST['mjc_actus_modifier_actus']) ) {
        ?>
        <form method="post" action="" enctype="multipart/form-data">
            <?php settings_fields('mjc_actus_modifier_settings') ?>
            <?php do_settings_sections('mjc_actus_modifier_settings') ?>
            <input type="hidden" name="mjc_actus_form_modifier" value="1"/>
            <?php submit_button(); ?>
        </form>
        <?php
        }
    }

    /**
     * Affichage du formulaire de suppression d'une actu
     * @return [type] [description]
     */
    public function mjc_actus_supprimer_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        if (isset($_POST['mjc_actus_form_supprimer']) && !empty($_POST['mjc_actus_form_supprimer'])) {
            echo '<p style="color:#2ecc71">Actu suprimée avec succès</p>';
        }
        echo '<hr/>';
        echo '<p>Choisissez l\'actu à supprimer</p>';
        ?>
        <form method="post" action="">
            <?php
            $actuss = $this->getAllactussOrderBy('nom');
            ?>
            <select name="mjc_actus_supprimer_actus" onchange="this.form.submit()">
                <?php
                foreach ( $actuss as $actus )
                {
                    echo '<option value="' . $actus->id . '">' . trim(stripslashes($actus->nom . ' / ' . $actus->intervenant_prenom . ' ' . $actus->intervenant_nom . ' / ' . $actus->tranche_age_nom)) . '</option>';
                }
                ?>
            </select>
            <input type="hidden" name="mjc_actus_form_supprimer" value="1"/>
            <?php submit_button('Supprimer l\'actu'); ?>
        </form>

        <?php
    }

    /**
     * Affichage de la liste des actus (sous forme de tableau)
     * Correspond à la première page du plugin
     * @return [type] [description]
     */
    public function mjc_actus_liste_html()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Toutes les actus</p>';
        echo '<hr/>';
        $actuss = $this->getAllactussOrderByDesc('date_actu');
        // print_r($actuss);
        ?>
        <table class="widefat page fixed" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col" id="date" class="manage-column" style="">N°</th>
                    <th scope="col" id="date" class="manage-column" style="">Nom</th>
                    <th scope="col" id="date" class="manage-column" style="">Tarif</th>
                    <th scope="col" id="date" class="manage-column" style="">Date</th>
                    <th scope="col" id="date" class="manage-column" style="">Jour/Heure</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th scope="col" id="date" class="manage-column" style="">N°</th>
                    <th scope="col" id="date" class="manage-column" style="">Nom</th>
                    <th scope="col" id="date" class="manage-column" style="">Tarif</th>
                    <th scope="col" id="date" class="manage-column" style="">Date</th>
                    <th scope="col" id="date" class="manage-column" style="">Jour/Heure</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                foreach ( $actuss as $actus )
                {
                ?>
                    <tr id="voy_">
                        <td >
                            <form method="get" action="">
                                <input type="hidden" name="page" value="mjc_actus_modifier" />
                                <input type="hidden" name="id_actus" value="<?php echo $actus->id; ?>" />
                                <input type="submit" value="<?php echo $actus->id; ?>" />
                            </form>
                        </td>
                        <td ><?php echo trim(stripslashes($actus->nom)); ?></td>
                        <td ><?php echo trim(stripslashes($actus->tarif)); ?></td>
                        <td ><?php echo $actus->date_actu; ?></td>
                        <td ><?php echo trim(stripslashes($actus->jour_heure)); ?></td>
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
        register_setting('mjc_actus_ajouter_settings', 'mjc_actus_ajouter_settings');
        register_setting('mjc_actus_modifier_settings', 'mjc_actus_modifier_settings');

        add_settings_section('mjc_actus_ajouter_section', 'Ajouter une actu', array($this, 'section_ajouter_html'), 'mjc_actus_ajouter_settings');
        add_settings_section('mjc_actus_modifier_section', 'Modifier une actu', array($this, 'section_modifier_html'), 'mjc_actus_modifier_settings');

        /** add_settings_field( $id, $title, $callback, $page, $section, $args ); **/
        add_settings_field('mjc_actus_id', 'N°', array($this, 'id_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_nom', 'Nom', array($this, 'nom_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_tarif', 'Tarif', array($this, 'tarif_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_jour_heure', 'Jour/Heure', array($this, 'jour_heure_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_lieu', 'Lieu', array($this, 'lieu_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_date_actu', 'Date', array($this, 'date_actu_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_photo', 'Photo', array($this, 'photo_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_descriptif', 'Descriptif', array($this, 'descriptif_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_date_debut_publication', 'Début publication', array($this, 'date_debut_publication_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');
        add_settings_field('mjc_actus_date_fin_publication', 'Fin publication', array($this, 'date_fin_publication_html'), 'mjc_actus_ajouter_settings', 'mjc_actus_ajouter_section');


        add_settings_field('mjc_actus_id', 'N°', array($this, 'id_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_nom', 'Nom', array($this, 'nom_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_tarif', 'Tarif', array($this, 'tarif_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_jour_heure', 'Jour/Heure', array($this, 'jour_heure_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_lieu', 'Lieu', array($this, 'lieu_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_date_actu', 'Date', array($this, 'date_actu_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_photo', 'Photo', array($this, 'photo_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_descriptif', 'Descriptif', array($this, 'descriptif_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_date_debut_publication', 'Début publication', array($this, 'date_debut_publication_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
        add_settings_field('mjc_actus_date_fin_publication', 'Fin publication', array($this, 'date_fin_publication_html'), 'mjc_actus_modifier_settings', 'mjc_actus_modifier_section');
    }

    public function section_ajouter_html() {
        echo 'Renseignez les informations sur l\'actu.';
    }

    public function section_modifier_html() {
        echo 'Modifiez les informations sur l\'actu.';  
    }

    /********************************************************************************************
     *** Affichage des champs du formulaire (avec valeur par défaut pour la modification)
     *******************************************************************************************/

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function id_html() {
        $default_value = $this->getDefaultValueFromById('id', 'mjc_actus');
        ?>
        <input type="text" name="mjc_actus_id_temp" value="<?php echo $default_value ?>" disabled/>
        <input type="hidden" name="mjc_actus_id" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function nom_html() {
        $default_value = $this->getDefaultValueFromById('nom', 'mjc_actus');
        ?>
        <input type="text" name="mjc_actus_nom" value="<?php echo trim(stripslashes($default_value)) ?>" size="50"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function tarif_html() {
        $default_value = $this->getDefaultValueFromById('tarif', 'mjc_actus');
        ?>
        <input type="text" name="mjc_actus_tarif" value="<?php echo trim(stripslashes($default_value)) ?>" size="30"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function date_actu_html() {
        $default_value = $this->getDefaultValueFromById('date_actu', 'mjc_actus');
        ?>
        <input type="date" name="mjc_actus_date_actu" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function jour_heure_html() {
        $default_value = $this->getDefaultValueFromById('jour_heure', 'mjc_actus');
        ?>
        <input type="text" name="mjc_actus_jour_heure" value="<?php echo trim(stripslashes($default_value)) ?>" size="30"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function lieu_html() {
        $default_value = $this->getDefaultValueFromById('lieu', 'mjc_actus');
        ?>
        <input type="text" name="mjc_actus_lieu" value="<?php echo trim(stripslashes($default_value)) ?>" size="30"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function photo_html() {
        $default_value = $this->getDefaultValueFromById('photo', 'mjc_actus');
        echo "<p><img width='200px' alt='Photo' src='../wp-content/uploads/actus/" . $default_value . "' /></p>";
        ?>
        <input type="file" name="mjc_actus_photo"/>
        <input type="hidden" name="mjc_actus_photo_bak" value="<?php echo $default_value ?>"/>
        <?php
        $upload_dir = wp_upload_dir();
        $user_dirname = $upload_dir['basedir'];
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function descriptif_html() {
        $default_value = $this->getDefaultValueFromById('descriptif', 'mjc_actus');
        ?>
        <textarea name="mjc_actus_descriptif" cols="50" rows="8"><?php echo trim(stripslashes($default_value)) ?></textarea>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function date_debut_publication_html() {
        $default_value = $this->getDefaultValueFromById('date_debut_publication', 'mjc_actus');
        ?>
        <input type="date" name="mjc_actus_date_debut_publication" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * Si modification : récupère la valeur correspondant à l'actu
     * Puis affiche le champs
     * @return [type] [description]
     */
    public function date_fin_publication_html() {
        $default_value = $this->getDefaultValueFromById('date_fin_publication', 'mjc_actus');
        ?>
        <input type="date" name="mjc_actus_date_fin_publication" value="<?php echo $default_value ?>"/>
        <?php
    }

    /**
     * On vérifie si un des formulaires a été envoyé
     * @return [type] [description]
     */
    public function process_action()
    {
        if (isset($_POST['mjc_actus_form_ajouter'])) {
            $this->insert_actu();
        }
        if (isset($_POST['mjc_actus_form_modifier'])) {
            $this->update_actu();
        }
        if (isset($_POST['mjc_actus_form_supprimer'])) {
            $this->delete_actu();
        }
    }

    /**
     * Enregistre le mail du visiteur dans la table 'mjc_newsletter'
     * @return [type] [description]
     */
    public function insert_actu()
    {
        if (isset($_POST['mjc_actus_form_ajouter']) && !empty($_POST['mjc_actus_form_ajouter'])) {
            global $wpdb;

            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $nom                    = ucfirst($_POST['mjc_actus_nom']);
            $tarif                  = $_POST['mjc_actus_tarif'];
            $date_actu              = $_POST['mjc_actus_date_actu'];
            $jour_heure             = ucfirst($_POST['mjc_actus_jour_heure']);
            $lieu                   = ucfirst($_POST['mjc_actus_lieu']);
            $descriptif             = ucfirst($_POST['mjc_actus_descriptif']);
            $date_debut_publication = $_POST['mjc_actus_date_debut_publication'];
            $date_fin_publication   = $_POST['mjc_actus_date_fin_publication'];

            $row = $wpdb->get_row("SELECT * FROM mjc_actus WHERE nom = '$nom'");
            if (is_null($row)) {
                $wpdb->insert("mjc_actus", array(
                    'nom'                    => $nom,
                    'tarif'                  => $tarif,
                    'date_actu'              => $date_actu,
                    'jour_heure'             => $jour_heure,
                    'lieu'                   => $lieu,
                    'descriptif'             => $descriptif,
                    'date_debut_publication' => $date_debut_publication,
                    'date_fin_publication'   => $date_fin_publication,
                ));

                $id = $wpdb->insert_id;
            }

            if (isset($_FILES['mjc_actus_photo']) AND $_FILES['mjc_actus_photo']['error'] == 0) {
                require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
                $mjcFolderFile = new MjcFolderFile();
                $cheminComplet = $mjcFolderFile->createActuFolder() . "/";
                // $movefile = wp_handle_upload($_FILES['mjc_actus_photo'], $upload_overrides);

                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // Testons si le fichier n'est pas trop gros
                if ($_FILES['mjc_actus_photo']['size'] <= 5242880)
                {
                    // Testons si l'extension est autorisée
                    $infosfichier          = pathinfo($_FILES['mjc_actus_photo']['name']);
                    $extension_upload      = $infosfichier['extension'];
                    $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG');
                    if (in_array($extension_upload, $extensions_autorisees))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        $nomFichier = $id . "." . strtolower($extension_upload);
                        $photo = date("Y") . "/" . date("m") . "/" . $nomFichier;

                        move_uploaded_file($_FILES['mjc_actus_photo']['tmp_name'], $cheminComplet . $nomFichier);
                        $wpdb->update("mjc_actus", array(
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
     * Modifie l'actu
     * @return [type] [description]
     */
    public function update_actu()
    {
        if (isset($_POST['mjc_actus_form_modifier']) && !empty($_POST['mjc_actus_form_modifier'])) {
            global $wpdb;

            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            $id                     = $_POST['mjc_actus_id'];
            $nom                    = ucfirst($_POST['mjc_actus_nom']);
            $tarif                  = $_POST['mjc_actus_tarif'];
            $date_actu              = $_POST['mjc_actus_date_actu'];
            $jour_heure             = ucfirst($_POST['mjc_actus_jour_heure']);
            $lieu                   = ucfirst($_POST['mjc_actus_lieu']);
            $descriptif             = ucfirst($_POST['mjc_actus_descriptif']);
            $date_debut_publication = $_POST['mjc_actus_date_debut_publication'];
            $date_fin_publication   = $_POST['mjc_actus_date_fin_publication'];
            $photo                  = "";

            if (isset($_FILES['mjc_actus_photo']) AND $_FILES['mjc_actus_photo']['error'] == 0) {
                require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
                $mjcFolderFile = new MjcFolderFile();
                $cheminComplet = $mjcFolderFile->createActuFolder() . "/";
                // $movefile = wp_handle_upload($_FILES['mjc_actus_photo'], $upload_overrides);

                // Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur

                // Testons si le fichier n'est pas trop gros
                if ($_FILES['mjc_actus_photo']['size'] <= 5242880)
                {
                    // Testons si l'extension est autorisée
                    $infosfichier          = pathinfo($_FILES['mjc_actus_photo']['name']);
                    $extension_upload      = $infosfichier['extension'];
                    $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG');
                    if (in_array($extension_upload, $extensions_autorisees))
                    {
                        // On peut valider le fichier et le stocker définitivement
                        $nomFichier = $id . "." . strtolower($extension_upload);
                        $photo = date("Y") . "/" . date("m") . "/" . $nomFichier;

                        move_uploaded_file($_FILES['mjc_actus_photo']['tmp_name'], $cheminComplet . $nomFichier);
                    }
                }
            }

            // $row = $wpdb->get_row("SELECT * FROM mjc_actus WHERE nom = '$nom' AND id_domaine = '$domaine'");
            // print_r($row);
            // if (!is_null($row)) {
                $wpdb->update("mjc_actus", array(
                    'nom'                    => $nom,
                    'tarif'                  => $tarif,
                    'date_actu'              => $date_actu,
                    'jour_heure'             => $jour_heure,
                    'lieu'                   => $lieu,
                    'photo'                  => $photo,
                    'descriptif'             => $descriptif,
                    'date_debut_publication' => $date_debut_publication,
                    'date_fin_publication'   => $date_fin_publication,
                    ),
                    array( 'id' => $id)
                );
            // }            
        }
    }

    /**
     * Modifie l'actu
     * @return [type] [description]
     */
    public function delete_actu()
    {
        if (isset($_POST['mjc_actus_form_supprimer']) && !empty($_POST['mjc_actus_form_supprimer'])) {
            require_once( ABSPATH . 'wp-includes/mjc/class/MjcFolderFile.class.php' );
            $mjcFolderFile = new MjcFolderFile();
            $actuFolderPath = $mjcFolderFile->getActuFolderPath();
            global $wpdb;

            $id = $_POST['mjc_actus_supprimer_actus'];
            // $date = date("Y-m-d H:i:s");

            $row = $wpdb->get_row("SELECT * FROM mjc_actus WHERE nom = $id");

            // if (!is_null($row)) {
                if (file_exists($actuFolderPath . $row->photo)) {
                    // chmod($actuFolderPath, 0777);
                    // chmod($actuFolderPath . $row->photo, 0777);
                    // unlink($actuFolderPath . $row->photo);
                }
                $wpdb->delete("mjc_actus", array( 'id' => $id));
            // }
        }
    }

    public function getAllFromOrderBy( $table , $order ) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY $order" );
    }

    public function getAllactuss() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_actus" );
    }

    public function getAllactussOrderBy($order) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_actus ORDER BY $order" );
    }

    public function getAllactussOrderByDesc($order) {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM mjc_actus ORDER BY $order DESC" );
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
        if (isset($_POST['mjc_actus_modifier_actus']) && !empty($_POST['mjc_actus_modifier_actus'])) {
            $res = $this->getFromById($name, $table, 'id=' . $_POST['mjc_actus_modifier_actus']);
        }
        return $res;
    }
}