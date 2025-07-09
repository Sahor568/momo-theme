/**
 * Movie Comments Handler
 * Handles AJAX comment submission and display for movies
 */

(function($) {
    'use strict';
    
    // Initialize comments when document is ready
    $(document).ready(function() {
        initializeComments();
    });
    
    function initializeComments() {
        const $commentForm = $('#movie-comment-form');
        const $replyForms = $('.reply-form');
        
        if (!$commentForm.length) {
            return; // No comment form on this page
        }
        
        // Handle main comment form submission
        $commentForm.on('submit', handleCommentSubmission);
        
        // Handle reply form submissions
        $(document).on('submit', '.reply-form', handleCommentSubmission);
        
        // Handle reply button clicks
        $(document).on('click', '.reply-btn', showReplyForm);
        
        // Handle cancel reply button clicks
        $(document).on('click', '.cancel-reply', hideReplyForm);
        
        // Add real-time validation
        $commentForm.find('input, textarea').on('blur', validateField);
        $(document).on('blur', '.reply-form input, .reply-form textarea', validateField);
        
        // Clear messages when user starts typing
        $commentForm.find('input, textarea').on('input', clearMessages);
        $(document).on('input', '.reply-form input, .reply-form textarea', clearMessages);
    }
    
    function handleCommentSubmission(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('.comment-submit-btn');
        const $messages = $form.find('.comment-form-messages');
        const isReply = $form.hasClass('reply-form');
        
        // Validate form before submission
        if (!validateCommentForm($form)) {
            return;
        }
        
        // Show loading state
        setLoadingState($submitBtn, true);
        $messages.empty();
        
        // Prepare form data
        const formData = {
            action: 'movieflix_submit_comment',
            nonce: movieflix_ajax.nonce,
            movie_id: $form.find('[name="movie_id"]').val(),
            parent_id: $form.find('[name="parent_id"]').val() || 0,
            author_name: $form.find('[name="author_name"]').val().trim(),
            author_email: $form.find('[name="author_email"]').val().trim(),
            comment_content: $form.find('[name="comment_content"]').val().trim()
        };
        
        // Send AJAX request
        $.ajax({
            url: movieflix_ajax.ajax_url,
            type: 'POST',
            data: formData,
            timeout: 30000, // 30 second timeout
            success: function(response) {
                handleCommentResponse(response, $form, $messages, isReply);
            },
            error: function(xhr, status, error) {
                handleCommentError(xhr, status, error, $messages);
            },
            complete: function() {
                setLoadingState($submitBtn, false);
            }
        });
    }
    
    function validateCommentForm($form) {
        let isValid = true;
        const fields = [
            { name: 'author_name', label: 'Name', required: true },
            { name: 'author_email', label: 'Email', required: true, type: 'email' },
            { name: 'comment_content', label: 'Comment', required: true, minLength: 5 }
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
            case 'author_name':
                if (!value) {
                    showFieldError($field, 'Name is required.');
                }
                break;
                
            case 'author_email':
                if (!value) {
                    showFieldError($field, 'Email is required.');
                } else if (!isValidEmail(value)) {
                    showFieldError($field, 'Please enter a valid email address.');
                }
                break;
                
            case 'comment_content':
                if (!value) {
                    showFieldError($field, 'Comment is required.');
                } else if (value.length < 5) {
                    showFieldError($field, 'Comment must be at least 5 characters.');
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
        $(this).closest('form').find('.comment-form-messages').empty();
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
    
    function handleCommentResponse(response, $form, $messages, isReply) {
        if (response.success) {
            showMessage($messages, response.data.message, 'success');
            
            // Reset form
            $form[0].reset();
            
            // Clear any field errors
            $form.find('.field-error').remove();
            $form.find('.error').removeClass('error');
            
            // Hide reply form if it was a reply
            if (isReply) {
                hideReplyForm.call($form.find('.cancel-reply')[0]);
            }
            
            // Add new comment to the list (you might want to reload comments or add dynamically)
            showMessage($('#comments-section .section-header'), 'Comment posted! It may take a moment to appear.', 'info');
            
            // Optionally reload the page to show the new comment
            setTimeout(function() {
                location.reload();
            }, 2000);
            
        } else {
            showMessage($messages, response.data.message, 'error');
        }
        
        // Scroll to messages
        scrollToMessages($messages);
    }
    
    function handleCommentError(xhr, status, error, $messages) {
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
        
        // Log error for debugging
        console.error('Comment submission error:', { xhr, status, error });
    }
    
    function showMessage($container, message, type) {
        const iconMap = {
            success: '✓',
            error: '⚠',
            info: 'ℹ'
        };
        
        const icon = iconMap[type] || iconMap.info;
        
        const messageHtml = `
            <div class="comment-message comment-${type}">
                <span class="message-icon">${icon}</span>
                <span class="message-text">${message}</span>
            </div>
        `;
        
        $container.html(messageHtml);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success' || type === 'info') {
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
    
    function showReplyForm() {
        const $button = $(this);
        const commentId = $button.data('comment-id');
        const $commentItem = $button.closest('.comment-item');
        
        // Hide any existing reply forms
        $('.reply-form-container').remove();
        
        // Create reply form
        const replyFormHtml = `
            <div class="reply-form-container">
                <h4>Reply to Comment</h4>
                <form class="reply-form">
                    <input type="hidden" name="movie_id" value="${$('#movie-comment-form [name="movie_id"]').val()}">
                    <input type="hidden" name="parent_id" value="${commentId}">
                    
                    <div class="comment-form-messages"></div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="reply_author_name_${commentId}">Name *</label>
                            <input type="text" id="reply_author_name_${commentId}" name="author_name" required>
                        </div>
                        <div class="form-group">
                            <label for="reply_author_email_${commentId}">Email *</label>
                            <input type="email" id="reply_author_email_${commentId}" name="author_email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reply_comment_content_${commentId}">Your Reply *</label>
                        <textarea id="reply_comment_content_${commentId}" name="comment_content" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary comment-submit-btn">
                            <span class="btn-text">Post Reply</span>
                            <span class="btn-loading" style="display: none;">
                                <span class="loading-spinner small"></span>
                                Posting...
                            </span>
                        </button>
                        <button type="button" class="btn btn-secondary cancel-reply">Cancel</button>
                    </div>
                </form>
            </div>
        `;
        
        // Add reply form after the comment
        $commentItem.after(replyFormHtml);
        
        // Focus on the first input
        $commentItem.next('.reply-form-container').find('input[name="author_name"]').focus();
        
        // Scroll to reply form
        $('html, body').animate({
            scrollTop: $commentItem.next('.reply-form-container').offset().top - 100
        }, 500);
    }
    
    function hideReplyForm() {
        $(this).closest('.reply-form-container').remove();
    }
    
    // Expose functions for external use
    window.MovieFlixComments = {
        validateCommentForm: validateCommentForm,
        showMessage: showMessage
    };
    
})(jQuery);