# MovieFlix WordPress Theme

A modern, dynamic, and fully responsive WordPress theme designed for movie streaming and download websites. Built with performance, accessibility, and user experience in mind.

## 🎬 Features

### Core Features
- **Custom Post Type**: Movies with detailed metadata
- **Advanced Taxonomies**: Genres, Languages, Quality, Release Years
- **Dynamic Search**: Real-time AJAX search with autocomplete
- **Advanced Filtering**: Filter movies by genre, year, quality, and language
- **Comments System**: Custom movie comments with reply functionality
- **Contact Management**: Built-in contact form with database storage
- **Download Tracking**: Track download statistics by quality
- **View Tracking**: Monitor movie popularity
- **Social Sharing**: Share movies on social platforms
- **SEO Optimized**: Schema markup and meta tags
- **Admin Dashboard**: Comprehensive management interface

### Design Features
- **Fully Responsive**: Optimized for all devices (mobile-first approach)
- **Dark Theme**: Netflix-inspired design
- **Lazy Loading**: Improved performance with image lazy loading
- **Smooth Animations**: CSS transitions and hover effects
- **Accessibility**: WCAG compliant with proper ARIA labels
- **Progressive Enhancement**: Works without JavaScript

### Technical Features
- **AJAX Powered**: Seamless user experience
- **Security**: Nonce verification and input sanitization
- **Performance**: Optimized queries and caching
- **Customizable**: WordPress Customizer integration
- **Translation Ready**: Internationalization support

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern web browser with JavaScript enabled

## 🚀 Installation

1. **Download the theme**
   ```bash
   git clone https://github.com/your-repo/movieflix-theme.git
   ```

2. **Upload to WordPress**
   - Upload the theme folder to `/wp-content/themes/`
   - Or install via WordPress admin: Appearance > Themes > Add New > Upload Theme

3. **Activate the theme**
   - Go to Appearance > Themes
   - Click "Activate" on MovieFlix theme

4. **Configure the theme**
   - Go to Appearance > Customize
   - Set up your site logo, colors, and preferences
   - Configure dynamic pages (About, Contact, Privacy Policy)

## ⚙️ Configuration

### Theme Customizer Options

Navigate to **Appearance > Customize** to configure:

#### Site Identity & Branding
- Site Logo
- Site Icon (Favicon)
- Site Title

#### MovieFlix Theme Options
- **Primary Color**: Main theme color (default: #e50914)
- **Movies Per Page**: Number of movies to display per page (1-100)

#### Dynamic Pages Manager
- **About Us Page**: Enable/disable and customize content
- **Privacy Policy Page**: Enable/disable and customize content
- **Contact Us Page**: Enable/disable and customize content

#### Footer Settings
- **Footer Text**: Custom footer copyright text

### Menu Setup

1. Go to **Appearance > Menus**
2. Create a new menu or edit existing
3. Assign to "Primary Menu" location
4. Dynamic pages (About, Contact, Privacy) are automatically added

### Movie Management

#### Adding Movies

1. Go to **Movies > Add New Movie**
2. Fill in the movie details:
   - **Title**: Movie name
   - **Content**: Movie description/plot
   - **Featured Image**: Movie poster
   - **Movie Details**: Year, duration, IMDB rating
   - **Movie Media**: Trailer URL, screenshots
   - **Download Links**: 480p, 720p, 1080p, 4K links
   - **Taxonomies**: Genres, languages, quality, release year

#### Movie Taxonomies

- **Genres**: Action, Comedy, Drama, etc.
- **Languages**: English, Spanish, French, etc.
- **Quality**: HD, Full HD, 4K, etc.
- **Release Years**: 2020, 2021, 2022, etc.

### Admin Dashboard

Access the MovieFlix dashboard at **MovieFlix > Dashboard** for:

- **Statistics**: Movie counts, views, downloads
- **Contact Messages**: Manage user inquiries
- **Movie Comments**: Moderate user comments
- **Settings**: Theme configuration

## 🎨 Customization

### CSS Variables

The theme uses CSS custom properties for easy customization:

```css
:root {
  --primary-color: #e50914;
  --secondary-color: #221f1f;
  --dark-bg: #141414;
  --text-light: #ffffff;
  --text-gray: #b3b3b3;
  --card-bg: #2f2f2f;
  --hover-color: #f40612;
  --border-radius: 8px;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  --transition: all 0.3s ease;
}
```

### Child Theme

For custom modifications, create a child theme:

1. Create a new folder: `/wp-content/themes/movieflix-child/`
2. Create `style.css`:
   ```css
   /*
   Theme Name: MovieFlix Child
   Template: movieflix
   */
   
   @import url("../movieflix/style.css");
   
   /* Your custom styles here */
   ```
3. Create `functions.php`:
   ```php
   <?php
   function movieflix_child_enqueue_styles() {
       wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
   }
   add_action('wp_enqueue_scripts', 'movieflix_child_enqueue_styles');
   ```

## 📱 Responsive Design

The theme is built with a mobile-first approach and includes:

### Breakpoints
- **Mobile**: 320px - 767px
- **Tablet**: 768px - 1023px
- **Desktop**: 1024px+

### Key Responsive Features
- Flexible grid layouts
- Scalable typography
- Touch-friendly buttons (44px minimum)
- Optimized images
- Collapsible navigation
- Adaptive content layout

## 🔧 Development

### File Structure

```
movieflix/
├── admin/
│   └── movie-dashboard.php     # Admin dashboard
├── js/
│   ├── movieflix.js           # Main JavaScript
│   ├── contact-form.js        # Contact form handler
│   ├── movie-comments.js      # Comments system
│   ├── menu-toggle.js         # Mobile menu
│   ├── back-to-top.js         # Back to top button
│   └── customizer.js          # Customizer preview
├── template-parts/
│   └── movie-card.php         # Movie card template
├── 404.php                    # 404 error page
├── footer.php                 # Footer template
├── functions.php              # Theme functions
├── header.php                 # Header template
├── index.php                  # Main template
├── page.php                   # Page template
├── single-movie.php           # Single movie template
├── style.css                  # Main stylesheet
└── README.md                  # Documentation
```

### JavaScript Functions

#### Main Functions (movieflix.js)
- `initializeMovieflix()`: Initialize all components
- `performSearch()`: Handle AJAX search
- `loadMoviesWithFilters()`: Filter and load movies
- `trackDownload()`: Track download statistics

#### Contact Form (contact-form.js)
- `handleFormSubmission()`: Process contact form
- `validateForm()`: Form validation
- `showMessage()`: Display messages

#### Comments (movie-comments.js)
- `handleCommentSubmission()`: Submit comments
- `showReplyForm()`: Show reply interface
- `validateCommentForm()`: Comment validation

### PHP Functions

#### Core Functions (functions.php)
- `movieflix_setup()`: Theme setup
- `movieflix_register_movie_post_type()`: Register movie post type
- `movieflix_register_taxonomies()`: Register taxonomies
- `movieflix_ajax_search()`: AJAX search handler
- `movieflix_filter_movies_advanced()`: Advanced filtering
- `movieflix_handle_contact_form()`: Contact form processor

## 🔒 Security

The theme implements several security measures:

- **Nonce Verification**: All AJAX requests use nonces
- **Input Sanitization**: All user inputs are sanitized
- **SQL Injection Prevention**: Prepared statements
- **XSS Protection**: Output escaping
- **CSRF Protection**: Form tokens
- **Rate Limiting**: Contact form and comments

## 🚀 Performance

### Optimization Features
- **Lazy Loading**: Images load on scroll
- **Minified Assets**: Compressed CSS/JS
- **Efficient Queries**: Optimized database queries
- **Caching Ready**: Compatible with caching plugins
- **CDN Ready**: External asset support

### Performance Tips
1. Use a caching plugin (WP Rocket, W3 Total Cache)
2. Optimize images (WebP format recommended)
3. Use a CDN for static assets
4. Enable GZIP compression
5. Minimize plugins

## 🌐 Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## 🐛 Troubleshooting

### Common Issues

#### Movies Not Displaying
1. Check if movies are published
2. Verify permalink structure
3. Clear cache if using caching plugin

#### Search Not Working
1. Ensure JavaScript is enabled
2. Check browser console for errors
3. Verify AJAX URL in browser network tab

#### Contact Form Not Submitting
1. Check database permissions
2. Verify nonce generation
3. Check server error logs

#### Responsive Issues
1. Clear browser cache
2. Check viewport meta tag
3. Validate CSS syntax

### Debug Mode

Enable WordPress debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## 📞 Support

For support and questions:

1. Check the documentation
2. Search existing issues
3. Create a new issue with:
   - WordPress version
   - PHP version
   - Theme version
   - Detailed description
   - Steps to reproduce

## 🔄 Updates

### Version History

#### v1.8.0 (Current)
- Enhanced responsive design
- Improved accessibility
- Advanced filtering system
- Contact management
- Comments system
- Performance optimizations

### Update Process

1. Backup your website
2. Download the latest version
3. Replace theme files
4. Test functionality
5. Clear cache

## 📄 License

This theme is licensed under the GPL v2 or later.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📚 Resources

- [WordPress Codex](https://codex.wordpress.org/)
- [WordPress Developer Handbook](https://developer.wordpress.org/)
- [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)
- [Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

**MovieFlix Theme** - Built with ❤️ for the WordPress community