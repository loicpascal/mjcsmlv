<?php
include_once plugin_dir_path( __FILE__ ).'/PartenairesWidget.class.php';

class Partenaires
{
    public function __construct()
    {
    	// add_action( 'widgets_init' , function() {register_widget( 'PartenairesWidget' );});

    	add_action( 'widgets_init', function(){
			register_widget( 'PartenairesWidget' );
		});
		add_action('wp_loaded', array($this, 'save_email'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('admin_menu', array($this, 'add_admin_sub_menu'));
		add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Créé la table 'mjc_partenaires'
     * Elle est appelée lors de l'activation du widget avec la méthode register_activation_hook()
     * @return [type] [description]
     */
    public static function install()
    {
    	global $wpdb;

    	$wpdb->query("CREATE TABLE IF NOT EXISTS mjc_partenaires (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL, date DATETIME NOT NULL);");
    }

    /**
     * Supprime la table 'mjc_partenaires'
     * Elle est appelée lors de la désinstallation du widget
     * @return [type] [description]
     */
    public static function uninstall()
	{
	    global $wpdb;

	    $wpdb->query("DROP TABLE IF EXISTS mjc_partenaires;");
	}

	/**
	 * Enregistre le mail du visiteur dans la table 'mjc_partenaires'
	 * @return [type] [description]
	 */
	public function save_email()
	{
	    if (isset($_POST['mjc_partenaires_email']) && !empty($_POST['mjc_partenaires_email'])) {
	        global $wpdb;
	        $email = $_POST['mjc_partenaires_email'];
	        $date = date("Y-m-d H:i:s");

	        $row = $wpdb->get_row("SELECT * FROM mjc_partenaires WHERE email = '$email'");
	        if (is_null($row)) {
	            $wpdb->insert("mjc_partenaires", array('email' => $email, 'date' => $date));
	        }
	    }
	}

	public function add_admin_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		$hook = add_menu_page('Partenaires', 'Partenaires', 'manage_options', 'mjc_partenaires', array($this, 'menu_html'));
		add_action('load-'.$hook, array($this, 'process_action'));
	}

	public function add_admin_sub_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_submenu_page('mjc_partenaires', 'Liste des mails', 'Liste', 'manage_options', 'mjc_partenaires_liste', array($this, 'tab_email'));
	}

	public function menu_html()
	{
	    echo '<h1>'.get_admin_page_title().'</h1>';
	    echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
	    echo '<hr/>';
	    ?>
	    <form method="post" action="options.php">
	    <?php settings_fields('mjc_partenaires_settings') ?>
	    <?php do_settings_sections('mjc_partenaires_settings') ?>
		<?php submit_button(); ?>
    	</form>

    	<form method="post" action="">
		    <input type="hidden" name="send_partenaires" value="1"/>
		    <?php submit_button('Envoyer la partenaires') ?>
		</form>
	    <?php
	    echo '<p>Dernier email enregistré : ' . $this->getLastEmail()->email . '</p>';
	}

	/**
	 * Affichage HTML du champ Expéditeur
	 * @return [type] [description]
	 */
	public function sender_html()
	{
		?>
	    <input type="text" name="zero_partenaires_sender" value="<?php echo get_option('zero_partenaires_sender')?>"/>
	    <?php
	}

	/**
	 * Affichage HTML du champ Objet
	 * @return [type] [description]
	 */
	public function object_html()
	{
		?>
	    <input type="text" name="zero_partenaires_object" value="<?php echo get_option('zero_partenaires_object')?>"/>
	    <?php
	}

	/**
	 * Affichage HTML du champ Objet
	 * @return [type] [description]
	 */
	public function body_html()
	{
		?>
	    <textarea name="mjc_partenaires_body"><?php echo get_option('mjc_partenaires_body')?></textarea>
	    <?php
	}

	public function tab_email()
	{
	    echo '<h1>'.get_admin_page_title().'</h1>';
	    echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
	    echo '<hr/>';
	    
	    echo '<p>Dernier email enregistré : ' . $this->getLastEmail()->email . '</p>';
	}

	/**
	 * Renvoi le dernier email enregistré
	 * @return [type] [description]
	 */
	private function getLastEmail()
	{
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM mjc_partenaires ORDER BY date DESC LIMIT 1;");

		if(!is_null($row)) {
			return $row;
		}
		return null;
	}

	public function register_settings()
	{
	    register_setting('mjc_partenaires_settings', 'mjc_partenaires_sender');
	    add_settings_section('mjc_partenaires_section', 'Paramètres d\'envoi', array($this, 'section_html'), 'mjc_partenaires_settings');
	    add_settings_field('mjc_partenaires_sender', 'Expéditeur', array($this, 'sender_html'), 'mjc_partenaires_settings', 'mjc_partenaires_section');
	    add_settings_field('mjc_partenaires_object', 'Objet', array($this, 'object_html'), 'mjc_partenaires_settings', 'mjc_partenaires_section');
	    add_settings_field('mjc_partenaires_body', 'Message', array($this, 'body_html'), 'mjc_partenaires_settings', 'mjc_partenaires_section');
	}

	public function section_html()
	{
	    echo 'Renseignez les paramètres d\'envoi de la partenaires.';
	}

	public function process_action()
	{
	    if (isset($_POST['send_partenaires'])) {
	        $this->send_partenaires();
	    }
	}

	public function send_partenaires()
	{
	    global $wpdb;
	    $destinataires = $wpdb->get_results("SELECT email FROM mjc_partenaires");
	    $object = get_option('mjc_partenaires_object', 'Partenaires');
	    $content = get_option('mjc_partenaires_message', 'Mon contenu');
	    $sender = get_option('mjc_partenaires_sender', 'no-reply@example.com');
	    $header = array('From: '.$sender);

	    foreach ($destinataires as $destinataire) {
	        $result = wp_mail($destinataire->email, $object, $content, $header);
	    }
	}
}