<?php
/**
 * Template for displaying pages
 * 
 * @package MovieFlix
 */

get_header(); ?>

<main class="main-content page-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article class="page-article">
                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                </header>
                
                <div class="page-content-wrapper">
                    <?php
                    the_content();
                    
                    // Special handling for contact page
                    if (get_the_ID() == get_option('movieflix_dynamic_page_contact')) {
                        movieflix_display_contact_form();
                    }
                    ?>
                </div>
                
                
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
// Enhanced Contact form function with AJAX support and database storage
function movieflix_display_contact_form() {
    ?>
    
    <div class="contact-form-section">
        <h3>Send us a Message</h3>
        <p>We'd love to hear from you! Your message will be saved and we'll get back to you as soon as possible.</p>
        
        <div id="contact-form-messages"></div>
        
        <form id="movieflix-contact-form" class="contact-form">
            <?php wp_nonce_field('movieflix_contact_form', 'movieflix_contact_nonce'); ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="contact_name">Name *</label>
                    <input type="text" id="contact_name" name="contact_name" required>
                </div>
                <div class="form-group">
                    <label for="contact_email">Email *</label>
                    <input type="email" id="contact_email" name="contact_email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="contact_subject">Subject *</label>
                <input type="text" id="contact_subject" name="contact_subject" required>
            </div>
            
            <div class="form-group">
                <label for="contact_message">Message *</label>
                <textarea id="contact_message" name="contact_message" rows="6" required placeholder="Tell us what's on your mind..."></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" id="contact-submit-btn" class="btn btn-primary">
                    <span class="btn-text">Send Message</span>
                    <span class="btn-loading" style="display: none;">
                        <span class="loading-spinner small"></span>
                        Sending...
                    </span>
                </button>
            </div>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#movieflix-contact-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $submitBtn = $('#contact-submit-btn');
            var $messages = $('#contact-form-messages');
            
            // Basic validation
            var name = $form.find('[name="contact_name"]').val().trim();
            var email = $form.find('[name="contact_email"]').val().trim();
            var subject = $form.find('[name="contact_subject"]').val().trim();
            var message = $form.find('[name="contact_message"]').val().trim();
            
            if (!name || !email || !subject || !message) {
                $messages.html('<div class="contact-error">Please fill in all required fields.</div>');
                return;
            }
            
            if (message.length < 10) {
                $messages.html('<div class="contact-error">Message must be at least 10 characters long.</div>');
                return;
            }
            
            // Show loading state
            $submitBtn.prop('disabled', true);
            $submitBtn.find('.btn-text').hide();
            $submitBtn.find('.btn-loading').show();
            $messages.empty();
            
            // Prepare form data
            var formData = {
                action: 'movieflix_contact_form',
                nonce: $form.find('[name="movieflix_contact_nonce"]').val(),
                contact_name: name,
                contact_email: email,
                contact_subject: subject,
                contact_message: message
            };
            
            // Send AJAX request
            $.ajax({
                url: movieflix_ajax.ajax_url,
                type: 'POST',
                data: formData,
                timeout: 30000,
                success: function(response) {
                    if (response.success) {
                        $messages.html('<div class="contact-success">' + response.data.message + '</div>');
                        $form[0].reset(); // Reset form
                        
                        // Clear any field errors
                        $form.find('.field-error').remove();
                        $form.find('.error').removeClass('error');
                    } else {
                        $messages.html('<div class="contact-error">' + response.data.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'An error occurred. Please try again later.';
                    if (status === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    }
                    $messages.html('<div class="contact-error">' + errorMessage + '</div>');
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false);
                    $submitBtn.find('.btn-text').show();
                    $submitBtn.find('.btn-loading').hide();
                    
                    // Scroll to messages
                    $('html, body').animate({
                        scrollTop: $messages.offset().top - 100
                    }, 500);
                }
            });
        });
    });
    </script>
    
    <style>
    .contact-form-section {
        background: var(--card-bg);
        padding: 2rem;
        border-radius: 10px;
        margin-top: 2rem;
        box-shadow: var(--box-shadow);
    }
    
    .contact-form-section h3 {
        color: var(--text-light);
        margin-bottom: 1rem;
        text-align: center;
        font-size: 1.8rem;
    }
    
    .contact-form-section p {
        color: var(--text-gray);
        text-align: center;
        margin-bottom: 2rem;
    }
    
    #contact-form-messages {
        margin-bottom: 1.5rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        color: var(--text-light);
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        background: var(--secondary-color);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        color: var(--text-light);
        font-size: 1rem;
        transition: border-color 0.3s ease;
        font-family: inherit;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    #contact-submit-btn {
        position: relative;
        min-width: 150px;
    }
    
    #contact-submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    .btn-loading {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .contact-success {
        background: #4caf50;
        color: white;
        padding: 1rem;
        border-radius: 5px;
        text-align: center;
        animation: slideInDown 0.5s ease;
    }
    
    .contact-error {
        background: #f44336;
        color: white;
        padding: 1rem;
        border-radius: 5px;
        text-align: center;
        animation: slideInDown 0.5s ease;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .contact-form-section {
            padding: 1.5rem;
        }
    }
    </style>
    <?php
}

get_footer(); ?>