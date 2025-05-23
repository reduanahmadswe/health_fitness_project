document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (!searchInput || !searchResults) {
        console.error('Search elements not found');
        return;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`../includes/search.php?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Search error:', data.error);
                        searchResults.innerHTML = '<div class="no-results">Error performing search</div>';
                    } else {
                        displayResults(data);
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="no-results">Error performing search</div>';
                });
        }, 300);
    });

    function displayResults(results) {
        if (!Array.isArray(results) || results.length === 0) {
            searchResults.innerHTML = '<div class="no-results">No results found</div>';
        } else {
            searchResults.innerHTML = results.map(result => `
                <a href="${result.url}" class="search-result-item">
                    <div class="result-title">${escapeHtml(result.name)}</div>
                    <div class="result-category">${escapeHtml(result.category)}</div>
                </a>
            `).join('');
        }
        searchResults.style.display = 'block';
    }

 
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });


    searchInput.addEventListener('keydown', function(e) {
        const results = searchResults.querySelectorAll('.search-result-item');
        const currentIndex = Array.from(results).findIndex(item => item === document.activeElement);

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (currentIndex < results.length - 1) {
                    results[currentIndex + 1].focus();
                }
                break;
            case 'ArrowUp':
                e.preventDefault();
                if (currentIndex > 0) {
                    results[currentIndex - 1].focus();
                }
                break;
            case 'Escape':
                searchResults.style.display = 'none';
                searchInput.blur();
                break;
        }
    });
}); 