<?php
/**
 * The header
 *
 * Transparent over a hero, solid once the reader has scrolled past it. The
 * state is decided by an IntersectionObserver in header.js rather than by a
 * scroll offset, so it is correct whatever height the hero happens to be.
 *
 * @package TripKailash
 * @since 1.1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="theme-color" content="#0E0904">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php /* The first thing in the tab order, for anyone who does not want to walk
         the navigation on every page. Visible only when focused. */ ?>
<a class="tk-skip" href="#main"><?php esc_html_e('Skip to content', 'trip-kailash'); ?></a>

<header class="tk-header" id="tk-header">
	<div class="tk-header__container">

		<div class="tk-header__logo">
			<?php if (function_exists('trip_kailash_site_logo')) { trip_kailash_site_logo(); } ?>
		</div>

		<nav class="tk-header__nav" aria-label="<?php esc_attr_e('Main', 'trip-kailash'); ?>">
			<?php
			wp_nav_menu(array(
				'theme_location' => 'primary',
				'menu_class'     => 'tk-nav-menu',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => 'tk_default_nav',
			));
			?>
		</nav>

		<div class="tk-header__cta">
			<a href="<?php echo esc_url(home_url('/book-yatra')); ?>" class="tk-btn tk-header__btn">
				<?php esc_html_e('Book Yatra', 'trip-kailash'); ?>
			</a>
		</div>

		<button class="tk-mobile-menu-toggle" type="button"
			aria-label="<?php esc_attr_e('Menu', 'trip-kailash'); ?>"
			aria-expanded="false" aria-controls="tk-mobile-nav">
			<span></span>
			<span></span>
			<span></span>
		</button>
	</div>

	<?php /* The mobile panel is a real element rather than a clone of the desktop
	         nav, so the two cannot drift apart. */ ?>
	<div class="tk-mobile-nav" id="tk-mobile-nav" hidden>
		<?php
		wp_nav_menu(array(
			'theme_location' => 'primary',
			'menu_class'     => 'tk-mobile-nav__menu',
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => 'tk_default_nav',
		));
		?>
		<a href="<?php echo esc_url(home_url('/book-yatra')); ?>" class="tk-btn tk-mobile-nav__cta">
			<?php esc_html_e('Book Yatra', 'trip-kailash'); ?>
		</a>
	</div>
</header>

<div id="page" class="site">
