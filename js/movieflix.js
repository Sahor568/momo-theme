;(function($) {
  // Global variables
  let searchTimeout
  let currentPage = 1
  let isLoading = false
  
  // Check if movieflix_ajax is available
  if (typeof movieflix_ajax === 'undefined') {
    console.error('MovieFlix: AJAX object not found');
    return;
  }

  // Initialize when document is ready
  $(document).ready(function() {
    initializeMovieflix()
  })

  function initializeMovieflix() {
    initializeSearch()
    initializeCategoryTabs()
    initializeFilters()
    initializePagination()
    initializeDownloadTracking()
    initializeLazyLoading()
    initializeImageErrorHandling()
    initializeClearFilters()
  }

  // Enhanced error handling
  function handleAjaxError(xhr, status, error) {
    console.error("AJAX Error:", { xhr, status, error })

    let errorMessage = movieflix_ajax.error_text

    if (xhr.responseJSON && xhr.responseJSON.data) {
      errorMessage = xhr.responseJSON.data
    } else if (xhr.status === 0) {
      errorMessage = "Network error. Please check your connection."
    } else if (xhr.status === 500) {
      errorMessage = "Server error. Please try again later."
    } else if (xhr.status === 403) {
      errorMessage = "Access denied. Please refresh the page."
    }

    return errorMessage
  }

  // Show loading state
  function showLoading(container) {
    const loadingHtml = `
      <div class="loading-container">
        <div class="loading-spinner"></div>
        <p class="loading-text">${movieflix_ajax.loading_text}</p>
      </div>
    `
    $(container).html(loadingHtml)
  }

  // Show error state
  function showError(container, message) {
    const errorHtml = `
      <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h3>Error Loading Content</h3>
        <p>${message}</p>
        <button class="btn btn-primary retry-btn" onclick="location.reload()">
          Try Again
        </button>
      </div>
    `
    $(container).html(errorHtml)
  }

  // Show no results state
  function showNoResults(container) {
    const noResultsHtml = `
      <div class="no-results">
        <div class="no-results-icon">🎬</div>
        <h3>No Movies Found</h3>
        <p>No movies match your current filters. Try adjusting your search criteria.</p>
      </div>
    `
    $(container).html(noResultsHtml)
  }

  // Enhanced Search Functionality
  function initializeSearch() {
    const searchInput = $("#movie-search")
    const resultsContainer = $("#search-results")
    const searchForm = $(".search-form")

    if (!searchInput.length) return

    // Handle form submission (search button click or Enter key)
    searchForm.on("submit", function(e) {
      e.preventDefault()
      const searchTerm = searchInput.val().trim()
      
      if (searchTerm.length >= 2) {
        // Redirect to search results page
        const searchUrl = movieflix_ajax.home_url + '/?s=' + encodeURIComponent(searchTerm) + '&post_type=movie'
        window.location.href = searchUrl
      }
    })

    // Handle real-time search for dropdown
    searchInput.on("input", function () {
      const searchTerm = $(this).val().trim()

      clearTimeout(searchTimeout)

      if (searchTerm.length < 2) {
        resultsContainer.hide().empty()
        return
      }

      searchTimeout = setTimeout(function() {
        performSearch(searchTerm, resultsContainer)
      }, 300)
    })

    // Handle search result clicks
    $(document).on("click", ".search-result-item", function (e) {
      e.preventDefault()
      const url = $(this).data("url")
      if (url) {
        window.location.href = url
      }
    })

    // Hide search results when clicking outside
    $(document).on("click", function(e) {
      if (!$(e.target).closest(".search-container").length) {
        resultsContainer.hide()
      }
    })

    // Handle escape key
    searchInput.on("keydown", function (e) {
      if (e.keyCode === 27) {
        // Escape key
        $(this).val("")
        resultsContainer.hide()
      }
    })
  }

  function performSearch(searchTerm, resultsContainer) {
    $.ajax({
      url: movieflix_ajax.ajax_url,
      type: "POST",
      data: {
        action: "movieflix_search_enhanced",
        search_term: searchTerm,
        nonce: movieflix_ajax.nonce,
      },
      beforeSend: function() {
        resultsContainer
          .html(`
          <div class="search-loading">
            <div class="loading-spinner small"></div>
            <p>Searching...</p>
          </div>
        `)
          .show()
      },
      success: function(response) {
        if (response.success && response.data && response.data.length > 0) {
          displaySearchResults(response.data, resultsContainer)
        } else {
          resultsContainer
            .html(`
            <div class="search-no-results">
              <p>No movies found for "${searchTerm}"</p>
              <p><small>Try searching with different keywords</small></p>
            </div>
          `)
            .show()
        }
      },
      error: function(xhr, status, error) {
        const errorMessage = handleAjaxError(xhr, status, error)
        resultsContainer
          .html(`
          <div class="search-error">
            <p>Search error: ${errorMessage}</p>
          </div>
        `)
          .show()
      },
    })
  }

  function displaySearchResults(results, container) {
    let html = ""
    results.forEach(function(movie) {
      const genresHtml = movie.genres ? `<div class="search-result-genres">${movie.genres}</div>` : "";
      const excerptHtml = movie.excerpt ? `<div class="search-result-excerpt">${movie.excerpt}</div>` : "";
      
      html += `
        <div class="search-result-item" data-url="${movie.url}">
          <div class="search-result-content">
            <img src="${movie.poster}" alt="${movie.title}" 
                 class="search-result-poster"
                 onerror="this.src='${movieflix_ajax.theme_url}/images/no-poster.jpg'">
            <div class="search-result-info">
              <div class="search-result-title">${movie.title}</div>
              ${genresHtml}
              ${excerptHtml}
              <div class="search-result-meta">
                <span class="search-result-year">${movie.year || 'N/A'}</span>
                ${movie.rating ? `<span class="search-result-rating">⭐ ${movie.rating}</span>` : ""}
                ${movie.views ? `<span class="search-result-views">👁 ${movie.views}</span>` : ""}
              </div>
            </div>
          </div>
        </div>
      `
    })
    container.html(html).show()
  }

  // Enhanced Category Tabs
  function initializeCategoryTabs() {
    $(".tab-button").on("click", function () {
      if (isLoading) return

      const category = $(this).data("category")

      $(".tab-button").removeClass("active")
      $(this).addClass("active")

      // Reset filters when changing category
      $(".filter-select").val("")

      currentPage = 1
      loadMoviesWithFilters({}, category, currentPage)
    })
  }

  // Enhanced Filters
  function initializeFilters() {
    $(".filter-select").on("change", function() {
      if (isLoading) return

      const filters = getActiveFilters()
      const activeCategory = $(".tab-button.active").data("category") || "all"

      currentPage = 1
      loadMoviesWithFilters(filters, activeCategory, currentPage)
    })
  }

  // Clear Filters
  function initializeClearFilters() {
    $("#clear-filters").on("click", function() {
      if (isLoading) return

      // Reset all filters
      $(".filter-select").val("")
      $(".tab-button").removeClass("active")
      $(".tab-button[data-category='all']").addClass("active")

      currentPage = 1
      loadMoviesWithFilters({}, "all", currentPage)
    })
  }

  function getActiveFilters() {
    const filters = {}

    const filterMap = {
      "year-filter": "year",
      "quality-filter": "quality",
      "language-filter": "language",
    }

    Object.keys(filterMap).forEach(function(filterId) {
      const value = $("#" + filterId).val()
      if (value) {
        filters[filterMap[filterId]] = value
      }
    })

    return filters
  }

  // Enhanced Movie Loading with better error handling
  function loadMoviesWithFilters(filters, category, page) {
    filters = filters || {}
    category = category || "all"
    page = page || 1
    
    if (isLoading) return

    isLoading = true
    const moviesContainer = $("#movies-container")
    const paginationContainer = $("#movies-pagination")

    // Get movies per page from localized script
    const moviesPerPage = movieflix_ajax.movies_per_page || 24

    $.ajax({
      url: movieflix_ajax.ajax_url,
      type: "POST",
      data: {
        action: "movieflix_filter_advanced",
        filters: filters,
        category: category,
        paged: page,
        nonce: movieflix_ajax.nonce,
      },
      beforeSend: function() {
        showLoading(moviesContainer)
        // Disable filter controls during loading
        $(".filter-select, .tab-button, #clear-filters").prop("disabled", true)
      },
      success: function(response) {
        isLoading = false
        
        // Re-enable filter controls
        $(".filter-select, .tab-button, #clear-filters").prop("disabled", false)

        if (response.success && response.data) {
          if (response.data.movies && response.data.movies.length > 0) {
            displayMovies(response.data.movies, moviesContainer)
            updatePagination(response.data.max_pages, page, paginationContainer)
            updateResultsCount(response.data.found_posts || response.data.movies.length)
          } else {
            showNoResults(moviesContainer)
            updatePagination(0, 1, paginationContainer)
            updateResultsCount(0)
          }
        } else {
          showNoResults(moviesContainer)
          updatePagination(0, 1, paginationContainer)
          updateResultsCount(0)
        }
      },
      error: function(xhr, status, error) {
        isLoading = false
        
        // Re-enable filter controls
        $(".filter-select, .tab-button, #clear-filters").prop("disabled", false)
        
        const errorMessage = handleAjaxError(xhr, status, error)
        showError(moviesContainer, errorMessage)
        updatePagination(0, 1, paginationContainer)
        updateResultsCount(0)
      },
    })
  }

  // Display movies function
  function displayMovies(movies, container) {
    let html = ""
    
    movies.forEach(function(movie) {
      const qualityBadge = movie.quality && movie.quality.length > 0 
        ? `<div class="movie-quality">${movie.quality[0]}</div>` 
        : ""
      
      const rating = movie.rating 
        ? `<span class="movie-rating">⭐ ${movie.rating}</span>` 
        : ""
      
      html += `
        <div class="movie-card">
          <div class="movie-poster">
            <a href="${movie.url}">
              <img src="${movie.poster}" alt="${movie.title}" class="lazy-load" 
                   onerror="this.src='${movieflix_ajax.theme_url}/images/no-poster.jpg'">
            </a>
            ${qualityBadge}
          </div>
          <div class="movie-info">
            <h3 class="movie-title">
              <a href="${movie.url}">${movie.title}</a>
            </h3>
            <div class="movie-meta">
              <span class="movie-year">${movie.year || 'N/A'}</span>
              ${rating}
              <span class="movie-views">👁 ${movie.views ? Number(movie.views).toLocaleString() : '0'}</span>
            </div>
          </div>
        </div>
      `
    })
    
    container.html(html)
    
    // Reinitialize lazy loading for new images
    initializeLazyLoading()
  }

  // Enhanced Pagination
  function initializePagination() {
    $(document).on("click", ".pagination-btn", function (e) {
      e.preventDefault()

      if (isLoading) return

      const page = parseInt($(this).data("page"))
      if (isNaN(page)) return

      const activeCategory = $(".tab-button.active").data("category") || "all"
      const filters = getActiveFilters()

      currentPage = page
      loadMoviesWithFilters(filters, activeCategory, page)

      // Smooth scroll to movies container
      $("html, body").animate(
        {
          scrollTop: $("#movies-container").offset().top - 100,
        },
        500,
      )
    })
  }

  function updatePagination(maxPages, currentPage, container) {
    let paginationHtml = ""

    if (maxPages > 1) {
      paginationHtml += '<div class="pagination-container">'
      
      // Previous button
      if (currentPage > 1) {
        paginationHtml += `<a href="#" class="pagination-btn" data-page="${currentPage - 1}">
          ‹ Previous
        </a>`
      }

      // Page numbers
      const startPage = Math.max(1, currentPage - 2)
      const endPage = Math.min(maxPages, currentPage + 2)

      if (startPage > 1) {
        paginationHtml += `<a href="#" class="pagination-btn" data-page="1">1</a>`
        if (startPage > 2) {
          paginationHtml += `<span class="pagination-dots">...</span>`
        }
      }

      for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
          paginationHtml += `<span class="pagination-current">${i}</span>`
        } else {
          paginationHtml += `<a href="#" class="pagination-btn" data-page="${i}">${i}</a>`
        }
      }

      if (endPage < maxPages) {
        if (endPage < maxPages - 1) {
          paginationHtml += `<span class="pagination-dots">...</span>`
        }
        paginationHtml += `<a href="#" class="pagination-btn" data-page="${maxPages}">${maxPages}</a>`
      }

      // Next button
      if (currentPage < maxPages) {
        paginationHtml += `<a href="#" class="pagination-btn" data-page="${currentPage + 1}">
          Next ›
        </a>`
      }
      
      paginationHtml += '</div>'
    }

    container.html(paginationHtml)
  }

  function updateResultsCount(count) {
    // Optional: Update results count display if needed
    console.log(`Found ${count} movies`)
  }

  // Download Tracking
  function initializeDownloadTracking() {
    $(document).on("click", ".download-btn", function (e) {
      const movieId = $(this).data("movie-id")
      const quality = $(this).data("quality")

      if (movieId && quality) {
        trackDownload(movieId, quality)
      }
    })
  }

  function trackDownload(movieId, quality) {
    $.ajax({
      url: movieflix_ajax.ajax_url,
      type: "POST",
      data: {
        action: "movieflix_track_download",
        movie_id: movieId,
        quality: quality,
        nonce: movieflix_ajax.nonce,
      },
      success: function(response) {
        if (response.success) {
          console.log("Download tracked successfully")
        }
      },
      error: function(xhr, status, error) {
        console.error("Failed to track download:", error)
      },
    })
  }

  // Enhanced Lazy Loading
  function initializeLazyLoading() {
    if ("IntersectionObserver" in window) {
      const images = document.querySelectorAll(".lazy-load:not(.loaded)")

      if (images.length === 0) return

      const imageObserver = new IntersectionObserver(
        function(entries, observer) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              const img = entry.target

              // Add loading class
              img.classList.add("loading")

              // Create a new image to preload
              const newImg = new Image()
              newImg.onload = function() {
                img.src = newImg.src
                img.classList.remove("loading")
                img.classList.add("loaded")
              }
              newImg.onerror = function() {
                img.src = movieflix_ajax.theme_url + "/images/no-poster.jpg"
                img.classList.remove("loading")
                img.classList.add("loaded", "error")
              }
              newImg.src = img.dataset.src || img.src

              observer.unobserve(img)
            }
          })
        },
        {
          rootMargin: "50px 0px",
          threshold: 0.01,
        },
      )

      images.forEach(function(img) {
        imageObserver.observe(img)
      })
    } else {
      // Fallback for older browsers
      $(".lazy-load:not(.loaded)").addClass("loaded")
    }
  }

  // Image Error Handling
  function initializeImageErrorHandling() {
    $(document).on("error", "img", function () {
      if (!$(this).hasClass("error-handled")) {
        $(this).addClass("error-handled")
        $(this).attr("src", movieflix_ajax.theme_url + "/images/no-poster.jpg")
      }
    })
  }

  // Copy to Clipboard Function
  window.copyToClipboard = function(text) {
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard
        .writeText(text)
        .then(function() {
          showNotification("Link copied to clipboard!", "success")
        })
        .catch(function() {
          fallbackCopyTextToClipboard(text)
        })
    } else {
      fallbackCopyTextToClipboard(text)
    }
  }

  function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea")
    textArea.value = text
    textArea.style.position = "fixed"
    textArea.style.left = "-999999px"
    textArea.style.top = "-999999px"
    document.body.appendChild(textArea)
    textArea.focus()
    textArea.select()

    try {
      document.execCommand("copy")
      showNotification("Link copied to clipboard!", "success")
    } catch (err) {
      console.error("Failed to copy: ", err)
      showNotification("Failed to copy link", "error")
    }

    document.body.removeChild(textArea)
  }

  // Notification System
  function showNotification(message, type) {
    type = type || "success"
    
    // Remove existing notifications
    $(".movieflix-notification").remove()

    const notification = $(`
      <div class="movieflix-notification ${type}">
        <div class="notification-content">
          <span class="notification-icon">${type === "success" ? "✓" : "⚠"}</span>
          <span class="notification-message">${message}</span>
        </div>
      </div>
    `)

    $("body").append(notification)

    // Animate in
    setTimeout(function() {
      notification.addClass("show")
    }, 100)

    // Auto remove
    setTimeout(function() {
      notification.removeClass("show")
      setTimeout(function() {
        notification.remove()
      }, 300)
    }, 3000)
  }

  // Smooth scrolling for anchor links
  $(document).on("click", 'a[href^="#"]', function (e) {
    const target = $(this.getAttribute("href"))
    if (target.length) {
      e.preventDefault()
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 100,
        },
        500,
      )
    }
  })

  // Expose global functions
  window.loadMoviesWithFilters = loadMoviesWithFilters
  window.showNotification = showNotification

})(jQuery)