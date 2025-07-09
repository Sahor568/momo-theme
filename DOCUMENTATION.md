# MovieFlix Theme - Technical Documentation

## Architecture Overview

The MovieFlix theme follows WordPress best practices and modern web development standards. It's built with a modular architecture that separates concerns and promotes maintainability.

## Database Schema

### Custom Tables

#### movieflix_contacts
```sql
CREATE TABLE wp_movieflix_contacts (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    subject varchar(200) NOT NULL,
    message text NOT NULL,
    status varchar(20) DEFAULT 'unread',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

#### movieflix_comments
```sql
CREATE TABLE wp_movieflix_comments (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    movie_id mediumint(9) NOT NULL,
    parent_id mediumint(9) DEFAULT 0,
    author_name varchar(100) NOT NULL,
    author_email varchar(100) NOT NULL,
    comment_content text NOT NULL,
    status varchar(20) DEFAULT 'approved',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY movie_id (movie_id),
    KEY parent_id (parent_id)
);
```

### Custom Post Types

#### Movie Post Type
- **Post Type**: `movie`
- **Supports**: title, editor, thumbnail, excerpt, custom-fields
- **Hierarchical**: false
- **Public**: true
- **Has Archive**: true

### Custom Taxonomies

#### Movie Genre
- **Taxonomy**: `movie_genre`
- **Hierarchical**: true
- **Public**: true

#### Movie Language
- **Taxonomy**: `movie_language`
- **Hierarchical**: true
- **Public**: true

#### Movie Quality
- **Taxonomy**: `movie_quality`
- **Hierarchical**: true
- **Public**: true

#### Movie Year
- **Taxonomy**: `movie_year`
- **Hierarchical**: true
- **Public**: true

### Custom Fields (Meta Keys)

#### Movie Details
- `_movie_release_year`: Release year (integer)
- `_movie_duration`: Duration in minutes (integer)
- `_movie_imdb_rating`: IMDB rating (float)


#### Movie Media
- `_movie_trailer_url`: YouTube trailer URL (URL)
- `_movie_screenshots`: Comma-separated screenshot URLs (text)

#### Download Links
- `_movie_download_480p`: 480p download link (URL)
- `_movie_download_720p`: 720p download link (URL)
- `_movie_download_1080p`: 1080p download link (URL)
- `_movie_download_4k`: 4K download link (URL)

#### Statistics
- `_movie_views`: View count (integer)
- `_download_count_{quality}`: Download count per quality (integer)

## API Endpoints

### AJAX Endpoints

#### Search Movies
- **Action**: `movieflix_search`
- **Method**: POST
- **Parameters**:
  - `search_term`: Search query string
  - `nonce`: Security nonce
- **Response**: Array of movie objects

#### Filter Movies
- **Action**: `movieflix_filter_advanced`
- **Method**: POST
- **Parameters**:
  - `filters`: Filter criteria object
  - `category`: Category slug
  - `paged`: Page number
  - `nonce`: Security nonce
- **Response**: Filtered movies with pagination

#### Submit Contact Form
- **Action**: `movieflix_contact_form`
- **Method**: POST
- **Parameters**:
  - `contact_name`: Sender name
  - `contact_email`: Sender email
  - `contact_subject`: Message subject
  - `contact_message`: Message content
  - `nonce`: Security nonce
- **Response**: Success/error message

#### Submit Comment
- **Action**: `movieflix_submit_comment`
- **Method**: POST
- **Parameters**:
  - `movie_id`: Movie ID
  - `parent_id`: Parent comment ID (for replies)
  - `author_name`: Comment author name
  - `author_email`: Comment author email
  - `comment_content`: Comment text
  - `nonce`: Security nonce
- **Response**: Success/error message

#### Track Download
- **Action**: `movieflix_track_download`
- **Method**: POST
- **Parameters**:
  - `movie_id`: Movie ID
  - `quality`: Download quality
  - `nonce`: Security nonce
- **Response**: Updated download count

## CSS Architecture

### CSS Custom Properties (Variables)
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

### Responsive Breakpoints
```css
/* Mobile First Approach */
/* Base styles: 320px+ */

/* Small Mobile */
@media (max-width: 480px) { }

/* Mobile */
@media (max-width: 768px) { }

/* Tablet */
@media (max-width: 1024px) { }

/* Desktop */
@media (min-width: 1025px) { }
```

### Grid System
The theme uses CSS Grid for layout:
```css
.movie-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 1.5rem;
}
```

## JavaScript Architecture

### Module Pattern
Each JavaScript file follows a module pattern:
```javascript
(function($) {
    'use strict';
    
    // Module code here
    
})(jQuery);
```

### Event Handling
Events are delegated for dynamic content:
```javascript
$(document).on('click', '.dynamic-element', function() {
    // Event handler
});
```

### AJAX Pattern
Consistent AJAX handling:
```javascript
$.ajax({
    url: movieflix_ajax.ajax_url,
    type: 'POST',
    data: {
        action: 'action_name',
        nonce: movieflix_ajax.nonce,
        // other data
    },
    success: function(response) {
        // Handle success
    },
    error: function(xhr, status, error) {
        // Handle error
    }
});
```

## Security Implementation

### Nonce Verification
```php
if (!wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_send_json_error('Security check failed');
    return;
}
```

### Input Sanitization
```php
$name = sanitize_text_field($_POST['name']);
$email = sanitize_email($_POST['email']);
$content = sanitize_textarea_field($_POST['content']);
$url = esc_url_raw($_POST['url']);
```

### Output Escaping
```php
echo esc_html($variable);
echo esc_url($url);
echo esc_attr($attribute);
```

### SQL Injection Prevention
```php
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM table WHERE column = %s",
    $value
));
```

## Performance Optimization

### Database Queries
- Use `WP_Query` with proper parameters
- Implement pagination
- Use `get_posts()` for simple queries
- Cache expensive queries

### Image Optimization
- Lazy loading implementation
- Responsive images with `srcset`
- WebP format support
- Proper image sizes

### JavaScript Optimization
- Event delegation
- Debounced search
- Minimal DOM manipulation
- Efficient selectors

### CSS Optimization
- Mobile-first approach
- Efficient selectors
- Minimal reflows
- Hardware acceleration for animations

## Accessibility Features

### ARIA Labels
```html
<button aria-label="Search movies" aria-expanded="false">
<input aria-describedby="search-help">
<div role="alert" aria-live="polite">
```

### Keyboard Navigation
- Tab order management
- Focus indicators
- Keyboard shortcuts
- Skip links

### Screen Reader Support
- Semantic HTML
- Proper heading hierarchy
- Alt text for images
- Form labels

### Color Contrast
- WCAG AA compliance
- High contrast mode support
- Color-blind friendly palette

## Testing Guidelines

### Browser Testing
- Chrome (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Edge (latest 2 versions)
- Mobile browsers

### Device Testing
- iPhone (various sizes)
- Android phones
- Tablets
- Desktop screens
- Large displays (4K)

### Performance Testing
- Google PageSpeed Insights
- GTmetrix
- WebPageTest
- Lighthouse audit

### Accessibility Testing
- WAVE Web Accessibility Evaluator
- axe DevTools
- Keyboard navigation testing
- Screen reader testing

## Deployment Checklist

### Pre-deployment
- [ ] Code review completed
- [ ] All tests passing
- [ ] Performance optimized
- [ ] Accessibility verified
- [ ] Cross-browser tested
- [ ] Mobile responsive verified
- [ ] SEO optimized
- [ ] Security audit completed

### Deployment
- [ ] Backup current site
- [ ] Upload new files
- [ ] Update database if needed
- [ ] Clear all caches
- [ ] Test critical functionality
- [ ] Monitor error logs

### Post-deployment
- [ ] Verify site functionality
- [ ] Check performance metrics
- [ ] Monitor user feedback
- [ ] Update documentation
- [ ] Plan next iteration

## Maintenance

### Regular Tasks
- Update WordPress core
- Update plugins
- Monitor performance
- Check security logs
- Backup database
- Optimize images
- Clean up unused files

### Monthly Tasks
- Review analytics
- Update content
- Check broken links
- Optimize database
- Review user feedback
- Plan improvements

### Quarterly Tasks
- Security audit
- Performance review
- Accessibility audit
- Code review
- Documentation update
- Feature planning

## Troubleshooting Guide

### Common Issues

#### JavaScript Errors
1. Check browser console
2. Verify jQuery is loaded
3. Check for conflicts
4. Validate syntax

#### CSS Issues
1. Clear browser cache
2. Check for specificity conflicts
3. Validate CSS syntax
4. Test in different browsers

#### PHP Errors
1. Enable debug mode
2. Check error logs
3. Verify file permissions
4. Check memory limits

#### Database Issues
1. Check connection
2. Verify table structure
3. Check user permissions
4. Optimize tables

### Debug Tools

#### WordPress Debug
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);
```

#### Query Debug
```php
define('SAVEQUERIES', true);
```

#### Browser DevTools
- Console for JavaScript errors
- Network tab for AJAX requests
- Elements tab for CSS debugging
- Performance tab for optimization

## Best Practices

### Code Standards
- Follow WordPress Coding Standards
- Use meaningful variable names
- Comment complex logic
- Keep functions small and focused
- Use proper indentation

### Security
- Validate all inputs
- Sanitize all outputs
- Use nonces for forms
- Implement rate limiting
- Regular security updates

### Performance
- Minimize HTTP requests
- Optimize images
- Use caching
- Minimize database queries
- Compress assets

### Accessibility
- Use semantic HTML
- Provide alt text
- Ensure keyboard navigation
- Maintain color contrast
- Test with screen readers

### SEO
- Use proper heading structure
- Implement schema markup
- Optimize meta tags
- Create XML sitemaps
- Use clean URLs

---

This documentation should be updated with each major release to reflect changes and improvements.