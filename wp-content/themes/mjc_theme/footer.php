<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
?>
		<p><a href="#top">↑&nbsp;Retour en haut&nbsp;↑</a></p>
		</div><!-- #main -->
		<footer id="colophon" class="site-footer" role="contentinfo">
			<?php get_sidebar( 'main' ); ?>

			<div class="site-info">
				© MJC Saint Marcel Lès Valence 2016 - Développé par <a target="_blank" href="http://www.loic-pascal.fr">Loïc PASCAL</a> - <a href="<?php echo get_permalink(391); ?>">Mentions légales</a> - <a href="http://www.freepik.com/free-photos-vectors/design">Freepik</a>
			</div>
			<!-- .site-info -->
		</footer><!-- #colophon -->
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>