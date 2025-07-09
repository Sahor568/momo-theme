<?php
/**
 * 404 Error Page Template
 * 
 * @package MovieFlix
 */

get_header(); ?>

<main class="main-content error-page">
    <div class="container">
        <div class="error-content">
            <div class="error-animation">
                <div class="error-number">404</div>
                <div class="error-film-strip">
                    <div class="film-hole"></div>
                    <div class="film-hole"></div>
                    <div class="film-hole"></div>
                    <div class="film-hole"></div>
                </div>
            </div>
            
            <div class="error-message">
                <h1><?php _e('Oops! Movie Not Found', 'movieflix'); ?></h1>
                <p><?php _e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'movieflix'); ?></p>
                
                <div class="error-suggestions">
                    <h3><?php _e('What can you do?', 'movieflix'); ?></h3>
                    <ul>
                        <li><?php _e('Check the URL for types', 'movieflix'); ?></li>
                        <li><?php _e('Go back to the previous page', 'movieflix'); ?></li>
                        <li><?php _e('Visit our homepage', 'movieflix'); ?></li>
                        <li><?php _e('Search for movies', 'movieflix'); ?></li>
                    </ul>
                </div>
                
                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9,22 9,12 15,12 15,22"></polyline>
                        </svg>
                        <?php _e('Go Home', 'movieflix'); ?>
                    </a>
                    
                    <button onclick="history.back()" class="btn btn-secondary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15,18 9,12 15,6"></polyline>
                        </svg>
                        <?php _e('Go Back', 'movieflix'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.error-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.error-content {
    text-align: center;
    margin-bottom: 4rem;
}

.error-animation {
    position: relative;
    margin-bottom: 3rem;
}

.error-number {
    font-size: 8rem;
    font-weight: 900;
    color: var(--primary-color);
    text-shadow: 0 0 20px rgba(229, 9, 20, 0.5);
    margin-bottom: 1rem;
    animation: pulse 2s infinite;
}

.error-film-strip {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1rem;
}

.film-hole {
    width: 20px;
    height: 20px;
    background: var(--primary-color);
    border-radius: 50%;
    animation: blink 1.5s infinite;
}

.film-hole:nth-child(2) { animation-delay: 0.3s; }
.film-hole:nth-child(3) { animation-delay: 0.6s; }
.film-hole:nth-child(4) { animation-delay: 0.9s; }

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.error-message h1 {
    font-size: 2.5rem;
    color: var(--text-light);
    margin-bottom: 1rem;
}

.error-message p {
    font-size: 1.2rem;
    color: var(--text-gray);
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.error-suggestions {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 10px;
    margin: 2rem auto;
    max-width: 500px;
    text-align: left;
}

.error-suggestions h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    text-align: center;
}

.error-suggestions ul {
    list-style: none;
    padding: 0;
}

.error-suggestions li {
    padding: 0.5rem 0;
    color: var(--text-gray);
    position: relative;
    padding-left: 1.5rem;
}

.error-suggestions li:before {
    content: "🎬";
    position: absolute;
    left: 0;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.error-actions .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    font-size: 1.1rem;
}

</style>

<?php get_footer(); ?>