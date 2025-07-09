/**
 * Contact Form Handler
 * Handles AJAX form submission for the contact form
 */

(function($) {
    'use strict';
    
    // Initialize contact form when document is ready
    $(document).ready(function() {
        initializeContactForm();
    });
    
    function initializeContactForm() {
        const $form = $('#movieflix-contact-form');
        
        if (!$form.length) {
            return; // No contact form on this page
        }
        
        // Remove any previously attached submit handler (avoid duplicate bindings)
        $form.off('submit');

        $form.on('submit', handleFormSubmission);
        
        // Add real-time validation
        $form.find('input, textarea').off('blur').on('blur', validateField);
        
        // Clear messages when user starts typing
        $form.find('input, textarea').off('input').on('input', clearMessages);
    }
    
    function handleFormSubmission(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $('#contact-submit-btn');
        const $messages = $('#contact-form-messages');
        
        // Prevent double clicks
        if ($submitBtn.prop('disabled')) {
            return;
        }
        
        // Validate form before submission
        if (!validateForm($form)) {
            return;
        }
        
        // Show loading state
        setLoadingState($submitBtn, true);
        $messages.empty();
        
        // Prepare form data
        const formData = {
            action: 'movieflix_contact_form',
            nonce: $form.find('[name="movieflix_contact_nonce"]').val(),
            contact_name: $form.find('[name="contact_name"]').val().trim(),
            contact_email: $form.find('[name="contact_email"]').val().trim(),
            contact_subject: $form.find('[name="contact_subject"]').val().trim(),
            contact_message: $form.find('[name="contact_message"]').val().trim()
        };
        
        // Send AJAX request
        $.ajax({
            url: movieflix_ajax.ajax_url,
            type: 'POST',
            data: formData,
            timeout: 30000, // 30 second timeout
            success: function(response) {
                handleFormResponse(response, $form, $messages);
            },
            error: function(xhr, status, error) {
                handleFormError(xhr, status, error, $messages);
            },
            complete: function() {
                setLoadingState($submitBtn, false);
            }
        });
    }
    
    function validateForm($form) {
        let isValid = true;
        const fields = [
            { name: 'contact_name', label: 'Name', required: true },
            { name: 'contact_email', label: 'Email', required: true, type: 'email' },
            { name: 'contact_subject', label: 'Subject', required: true },
            { name: 'contact_message', label: 'Message', required: true, minLength: 10 }
        ];
        
        // Clear previous validation errors
        $form.find('.field-error').remove();
        $form.find('.error').removeClass('error');
        
        fields.forEach(function(field) {
            const $field = $form.find('[name="' + field.name + '"]');
            const value = $field.val().trim();
            
            if (field.required && !value) {
                showFieldError($field, field.label + ' is required.');
                isValid = false;
            } else if (field.type === 'email' && value && !isValidEmail(value)) {
                showFieldError($field, 'Please enter a valid email address.');
                isValid = false;
            } else if (field.minLength && value && value.length < field.minLength) {
                showFieldError($field, field.label + ' must be at least ' + field.minLength + ' characters.');
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    function validateField() {
        const $field = $(this);
        const fieldName = $field.attr('name');
        const value = $field.val().trim();
        
        // Clear previous error
        $field.removeClass('error');
        $field.siblings('.field-error').remove();
        
        // Validate based on field type
        switch (fieldName) {
            case 'contact_name':
                if (!value) {
                    showFieldError($field, 'Name is required.');
                }
                break;
                
            case 'contact_email':
                if (!value) {
                    showFieldError($field, 'Email is required.');
                } else if (!isValidEmail(value)) {
                    showFieldError($field, 'Please enter a valid email address.');
                }
                break;
                
            case 'contact_subject':
                if (!value) {
                    showFieldError($field, 'Subject is required.');
                }
                break;
                
            case 'contact_message':
                if (!value) {
                    showFieldError($field, 'Message is required.');
                } else if (value.length < 10) {
                    showFieldError($field, 'Message must be at least 10 characters.');
                }
                break;
        }
    }
    
    function showFieldError($field, message) {
        $field.addClass('error');
        $field.after('<div class="field-error">' + message + '</div>');
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function clearMessages() {
        $('#contact-form-messages').empty();
    }
    
    function setLoadingState($button, loading) {
        if (loading) {
            $button.prop('disabled', true);
            $button.find('.btn-text').hide();
            $button.find('.btn-loading').show();
        } else {
            $button.prop('disabled', false);
            $button.find('.btn-text').show();
            $button.find('.btn-loading').hide();
        }
    }
    
    function handleFormResponse(response, $form, $messages) {
        if (response.success) {
            showMessage($messages, response.data.message, 'success');
            $form[0].reset();
            $form.find('.field-error').remove();
            $form.find('.error').removeClass('error');
            
            // Track successful submission
            if (typeof gtag !== 'undefined') {
                gtag('event', 'form_submit', {
                    event_category: 'Contact',
                    event_label: 'Contact Form Submission'
                });
            }
        } else {
            showMessage($messages, response.data.message, 'error');
        }
        
        scrollToMessages($messages);
    }
    
    function handleFormError(xhr, status, error, $messages) {
        let errorMessage = 'An error occurred. Please try again later.';
        
        if (status === 'timeout') {
            errorMessage = 'Request timed out. Please check your connection and try again.';
        } else if (xhr.status === 0) {
            errorMessage = 'Network error. Please check your internet connection.';
        } else if (xhr.status >= 500) {
            errorMessage = 'Server error. Please try again later.';
        } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
            errorMessage = xhr.responseJSON.data.message;
        }
        
        showMessage($messages, errorMessage, 'error');
        scrollToMessages($messages);
        
        console.error('Contact form error:', { xhr, status, error });
    }
    
    function showMessage($container, message, type) {
        const iconMap = {
            success: '✓',
            error: '⚠',
            info: 'ℹ'
        };
        
        const icon = iconMap[type] || iconMap.info;
        
        const messageHtml = `
            <div class="contact-message contact-${type}">
                <span class="message-icon">${icon}</span>
                <span class="message-text">${message}</span>
            </div>
        `;
        
        $container.html(messageHtml);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $container.fadeOut(500, function() {
                    $container.empty().show();
                });
            }, 5000);
        }
    }
    
    function scrollToMessages($messages) {
        if ($messages.length) {
            $('html, body').animate({
                scrollTop: $messages.offset().top - 100
            }, 500);
        }
    }
    
    // Expose functions for external use
    window.MovieFlixContact = {
        validateForm: validateForm,
        showMessage: showMessage
    };
    
})(jQuery);
// End of contact-form.js