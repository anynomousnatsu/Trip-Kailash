# Trip Kailash WordPress Theme

A custom WordPress theme built for Elementor for spiritual pilgrimage travel, featuring three deity tracks (Shiva, Vishnu, Devi).

## Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher
- Elementor 3.x or higher (required plugin)

## Installation

1. Upload the `Trip-Kailash` folder to `/wp-content/themes/`
   (the capitalisation matters: WordPress resolves the theme by directory name, and most hosts run a case-sensitive filesystem, so `trip-kailash` will not activate)
2. Install and activate Elementor plugin
3. Activate the Trip Kailash theme from WordPress admin
4. Navigate to Appearance > Themes and activate "Trip Kailash"

## Features

- Custom Post Types: Pilgrimage Packages, Guides, Lodges
- Custom Taxonomy: Deity (Shiva, Vishnu, Devi)
- Complete Elementor widget library for building pages
- Design system with CSS variables
- REST API endpoints for dynamic content
- Responsive design (mobile, tablet, desktop)
- Accessibility compliant (WCAG 2.1 AA)

## Theme Structure

```
trip-kailash/
├── style.css                          # Theme header
├── functions.php                      # Theme setup
├── header.php                         # Header template
├── footer.php                         # Footer template
├── index.php                          # Main template
├── assets/
│   ├── css/                          # Stylesheets
│   ├── js/                           # JavaScript files
│   └── images/                       # Images
├── inc/
│   ├── custom-post-types.php         # CPT registration
│   ├── taxonomies.php                # Taxonomy registration
│   ├── rest-api.php                  # REST endpoints
│   ├── enqueue.php                   # Asset enqueuing
│   ├── helpers.php                   # Shared cached queries
│   ├── form-handler.php              # Contact/booking form AJAX + email
│   ├── seo.php                       # Meta tags, canonical domain
│   ├── schema.php                    # JSON-LD structured data
│   ├── sitemap.php                   # XML sitemaps (replaces core sitemaps)
│   ├── geo-content.php               # Location-targeted content
│   ├── performance.php               # Asset hints, cache headers, defer
│   └── elementor/
│       ├── elementor-init.php        # Elementor integration
│       └── widgets/                  # Custom widgets
└── templates/
    └── archive-pilgrimage_package.php # Archive template
```

## Custom Post Types

### Pilgrimage Package
- Supports: title, editor, excerpt, thumbnail, custom-fields
- Custom fields: trip_length, deity, has_lodge, has_helicopter, includes_meals, includes_rituals, difficulty, best_months, price_from, key_stops

### Guide
- Supports: title, editor, thumbnail
- Custom fields: years_of_experience, short_bio

### Lodge
- Supports: title, editor, thumbnail
- Custom fields: location, amenities

## Elementor Widgets

All custom widgets are available in the "Trip Kailash" category in Elementor editor:

### Homepage Widgets
- **Hero Video Widget**: Full-screen video hero with sound toggle and deity buttons
- **Deity Cards Widget**: Three cards for Shiva, Vishnu, and Devi with color overlays
- **Why Choose Us Widget**: Feature grid with icons, titles, and descriptions
- **Featured Packages Widget**: Filterable grid of pilgrimage packages
- **Guides Widget**: Display guide profiles with photos and experience
- **Lodges Widget**: Showcase lodges with amenities
- **Contact Form Widget**: Contact form with package interest dropdown

### Deity Page Widgets
- **Deity Hero Widget**: Hero section with deity-specific styling
- **Package Filter Widget**: Filter packages by duration, difficulty, helicopter
- **Package Grid Widget**: Grid of packages with AJAX filtering support

### Utility Widgets
- **Package Card Widget**: Individual package card for custom layouts

## Widget Usage Guide

### Hero Video Widget
1. Add widget to page
2. Upload video (WebM format recommended)
3. Set heading and subheading text
4. Configure deity button links
5. Adjust overlay opacity

### Package Card Widget
1. Add widget to page
2. Select package from dropdown
3. Toggle "Show Key Stops on Hover"
4. Card automatically displays package data

### Contact Form Widget
1. Add widget to page
2. Set form title and button text
3. Configure success message
4. Set email recipient
5. Form automatically populates with packages

## REST API Endpoints

### Get Package Details
```
GET /wp-json/tripkailash/v1/package/{id}
```

Returns package data including title, content, featured image, and all meta fields.

## JavaScript Features

- **Package Overlay**: Click any package card to view details in overlay
- **Mobile Navigation**: Hamburger menu with slide-in animation
- **Sticky Header**: Header shrinks on scroll
- **Sticky Bottom Bar**: Mobile-only bottom bar with WhatsApp and booking buttons
- **Video Controls**: Sound toggle for hero video
- **Form Handling**: AJAX form submission with validation
- **Form Pre-fill**: URL parameters pre-fill contact form

## CSS Variables

The theme uses CSS custom properties for consistent styling:

```css
--tk-bg-main: #F5F2ED
--tk-text-main: #2C2B28
--tk-gold: #B8860B
--tk-shiva: #1A1B3A
--tk-vishnu: #E67E22
--tk-devi: #8B1538
```

## Deity-Specific Styling

Add deity classes to body or containers for automatic color theming:
- `.tk-deity--shiva`
- `.tk-deity--vishnu`
- `.tk-deity--devi`

## Creating Sample Content

### Sample Package
1. Go to Packages > Add New
2. Add title, content, featured image
3. Set deity taxonomy
4. Fill in custom fields:
   - Trip Length: "7 nights / 8 days"
   - Difficulty: "Moderate"
   - Price From: 85000
   - Key Stops: ["Kathmandu", "Manasarovar", "Kailash"]
   - Toggle amenities: Lodge, Meals, Rituals

### Sample Guide
1. Go to Guides > Add New
2. Add name, bio, featured image
3. Set years of experience: 15

### Sample Lodge
1. Go to Lodges > Add New
2. Add name, description, featured image
3. Set location: "Kathmandu, Nepal"
4. Add amenities: ["Hot Water", "Puja Room", "WiFi"]

## Building Pages with Elementor

### Homepage
1. Create new page
2. Edit with Elementor
3. Add widgets in order:
   - Hero Video Widget
   - Deity Cards Widget
   - Why Choose Us Widget
   - Featured Packages Widget
   - Guides Widget
   - Lodges Widget
   - Contact Form Widget

### Deity Page (Shiva/Vishnu/Devi)
1. Create new page
2. Add body class: `tk-deity--shiva` (or vishnu/devi)
3. Edit with Elementor
4. Add widgets:
   - Deity Hero Widget
   - Package Filter Widget
   - Package Grid Widget
   - Contact Form Widget

## Troubleshooting

### Widgets Not Showing
- Ensure Elementor is installed and activated
- Clear Elementor cache: Elementor > Tools > Regenerate CSS

### Images Not Loading
- Check file permissions on uploads folder
- Regenerate thumbnails: Tools > Regenerate Thumbnails

### REST API Not Working
- Go to Settings > Permalinks and click Save
- Check .htaccess file permissions

## Development

Theme version: see `TRIP_KAILASH_VERSION` in `functions.php` and `Version:` in `style.css`.
Keep those two in sync -- the constant is what busts browser caches for CSS and JS.

Text domain: trip-kailash

### Before you push

A WordPress theme has no build step, so nothing catches a PHP parse error before
it reaches the server, where it becomes a white screen on the live site. Run the
linter first:

```bash
bin/lint-php.sh
```

It needs PHP on your PATH (`winget install PHP.PHP` on Windows, `brew install php`
on macOS). The same check runs in CI on every push and pull request via
`.github/workflows/lint.yml`, pinned to PHP 8.4 to match production.

### Where email goes

Enquiry and booking email is sent to `TK_FORM_RECIPIENT`, defined in
`functions.php` and overridable from `wp-config.php`. It is deliberately not
configurable from the page: reading the recipient from the form let anyone submit
their own destination address and use the site as a mail relay.

### Configuration that lives outside this repo

- `.htaccess` is not in version control, so the canonical domain redirect is
  handled in `inc/seo.php` instead.
- Page layouts are stored as Elementor data in the database, not as templates
  here. A widget's PHP controls what it *can* render; the live page decides what
  it *does* render.

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- iOS Safari (latest)
- Chrome Mobile (latest)

## Performance

- Lazy loading for images
- Vanilla JavaScript (no jQuery)
- Minimal CSS bundle
- RequestAnimationFrame for smooth animations
- Optimized video delivery

## Accessibility

- WCAG 2.1 AA compliant
- Semantic HTML5
- ARIA labels
- Keyboard navigation
- Focus trap in modals
- 7:1 color contrast ratio

## Support

For support and documentation, visit: https://tripkailash.com

## License

GNU General Public License v2 or later
