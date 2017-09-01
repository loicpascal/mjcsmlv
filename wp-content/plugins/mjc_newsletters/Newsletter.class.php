<?php
include_once plugin_dir_path( __FILE__ ).'/NewsletterWidget.class.php';

class Newsletter
{
    public function __construct()
    {
    	add_action('wp_loaded', array($this, 'delete_email'));

    	add_action( 'widgets_init', function(){
			register_widget( 'NewsletterWidget' );
		});
		add_action('wp_loaded', array($this, 'save_email'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		// add_action('admin_menu', array($this, 'add_admin_sub_menu'));
		// add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Créé la table 'mjc_newsletter'
     * Elle est appelée lors de l'activation du widget avec la méthode register_activation_hook()
     * @return [type] [description]
     */
    public static function install()
    {
    	global $wpdb;

    	$wpdb->query("CREATE TABLE IF NOT EXISTS mjc_newsletter (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL, date DATETIME NOT NULL);");
    }

    /**
     * Supprime la table 'mjc_newsletter'
     * Elle est appelée lors de la désinstallation du widget
     * @return [type] [description]
     */
    public static function uninstall()
	{
	    global $wpdb;

	    $wpdb->query("DROP TABLE IF EXISTS mjc_newsletter;");
	}

	public function add_admin_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		$hook = add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'mjc_newsletter', array($this, 'tab_email'));
		add_action('load-'.$hook, array($this, 'process_action'));
	}

	public function tab_email()
	{
		$emails = $this->getAllFromOrderBy('mjc_newsletter', 'date DESC');
	    echo '<h1>'.get_admin_page_title().'</h1>';
	    echo '<p>Liste des E-mails</p>';
	    if (isset($_GET['mjc_newsletter_supprimer_email']) && !empty($_GET['mjc_newsletter_supprimer_email'])) {
            echo '<p style="color:#2ecc71">E-mail supprimé avec succès</p>';
        }
	    echo '<hr/>';
	    ?>
		<table class="widefat page fixed p_newsletter" cellspacing="0" style="width:auto;">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>Email</th>
                    <th>Date d'enregistrement</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $emails as $email )
                {
                ?>
                    <tr>
                    	<td style="text-align: center;"><a class="js-confirm" title="Supprimer" data-confirm="supprimer l'email '<?php echo trim(stripslashes($email->email)); ?>'" href="?page=mjc_newsletter_liste&mjc_newsletter_supprimer_email=<?php echo $email->id; ?>"><i class="fa fa-trash-o fa-2x" aria-hidden="true"></i>
                        </a></td>
                        <td><?php echo $email->email; ?></td>
                        <td ><?php echo date('d/m/Y H:i:s', strtotime($email->date)); ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

	    <?php
	    $this->addJSCSS();
	}

	public function process_action()
	{
	    if (isset($_GET['mjc_newsletter_supprimer_email'])) {
            $this->delete_email();
        }
	}

	/**
	 * Enregistre le mail du visiteur dans la table 'mjc_newsletter'
	 * @return [type] [description]
	 */
	public function save_email()
	{
	    if (isset($_POST['mjc_newsletter_email']) && !empty($_POST['mjc_newsletter_email'])) {
	        global $wpdb;
	        $email = $_POST['mjc_newsletter_email'];
	        $date = date("Y-m-d H:i:s");

	        $row = $wpdb->get_row("SELECT * FROM mjc_newsletter WHERE email = '$email'");
	        if (is_null($row)) {
	            $wpdb->insert("mjc_newsletter", array('email' => $email, 'date' => $date));
	        }
	    }
	}

	/**
     * Supprime l'email
     * @return [type] [description]
     */
    public function delete_email()
    {
    	if (isset($_GET['mjc_newsletter_supprimer_email'])) {
	        global $wpdb;
	        $id = $_GET['mjc_newsletter_supprimer_email'];
	        $wpdb->delete("mjc_newsletter", array( 'id' => $id));
	    }
    }

	private function addJSCSS() {
        // JS
        wp_register_script( 'jquery3', includes_url() . '/js/jquery/jquery-3.0.0.min.js');
        wp_enqueue_script('jquery3');

        wp_register_script( 'custom_jquery', plugins_url('/js/main.js', __FILE__));
        wp_enqueue_script('custom_jquery');

        // CSS
        wp_register_style( 'font-awesome', includes_url() . '/css/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('font-awesome');

        wp_register_style( 'custom_css', includes_url() . '/css/mjc/custom_css.css');
        wp_enqueue_style('custom_css');
    }

	/**
     *
     *  Méthodes Bases de données
     * 
     */

    public function getAllFromOrderBy( $table , $order = null ) {
        $order = ($order == null) ? '' : 'ORDER BY ' . $order;

        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $table $order" );
    }

}