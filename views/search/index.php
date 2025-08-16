<?php include 'views/layout/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>Advanced Search
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form id="searchForm" method="GET" action="<?= APP_URL ?>search">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="searchInput" 
                                           name="q" placeholder="Search blood requests, users, donations..." 
                                           value="<?= htmlspecialchars($searchTerm ?? '') ?>" autocomplete="off">
                                    <button class="btn btn-primary btn-lg" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div id="searchSuggestions" class="position-absolute bg-white border rounded shadow-sm" style="display: none; z-index: 1000; width: 100%; max-height: 200px; overflow-y: auto;"></div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="fas fa-filter me-1"></i>Advanced Filters
                                </button>
                            </div>
                        </div>

                        <!-- Advanced Filters -->
                        <div class="collapse mt-3" id="advancedFilters">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Search Type</label>
                                        <select class="form-select" name="type">
                                            <option value="all" <?= ($filters['type'] ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                                            <option value="requests" <?= ($filters['type'] ?? '') === 'requests' ? 'selected' : '' ?>>Blood Requests</option>
                                            <option value="users" <?= ($filters['type'] ?? '') === 'users' ? 'selected' : '' ?>>Users</option>
                                            <option value="donations" <?= ($filters['type'] ?? '') === 'donations' ? 'selected' : '' ?>>Donations</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-select" name="blood_group">
                                            <option value="">Any</option>
                                            <option value="A+" <?= ($filters['blood_group'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= ($filters['blood_group'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= ($filters['blood_group'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= ($filters['blood_group'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                                            <option value="AB+" <?= ($filters['blood_group'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= ($filters['blood_group'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                            <option value="O+" <?= ($filters['blood_group'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= ($filters['blood_group'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" class="form-control" name="location" 
                                               placeholder="City, Hospital..." value="<?= htmlspecialchars($filters['location'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Urgency</label>
                                        <select class="form-select" name="urgency">
                                            <option value="">Any</option>
                                            <option value="low" <?= ($filters['urgency'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                                            <option value="medium" <?= ($filters['urgency'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="high" <?= ($filters['urgency'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                                            <option value="critical" <?= ($filters['urgency'] ?? '') === 'critical' ? 'selected' : '' ?>>Critical</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="">Any</option>
                                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" class="form-control" name="date_from" 
                                               value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" class="form-control" name="date_to" 
                                               value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Sort By</label>
                                        <select class="form-select" name="sort_by">
                                            <option value="relevance" <?= ($filters['sort_by'] ?? '') === 'relevance' ? 'selected' : '' ?>>Relevance</option>
                                            <option value="date" <?= ($filters['sort_by'] ?? '') === 'date' ? 'selected' : '' ?>>Date</option>
                                            <option value="urgency" <?= ($filters['sort_by'] ?? '') === 'urgency' ? 'selected' : '' ?>>Urgency</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Search Results -->
                    <?php if (isset($searchTerm) && !empty($searchTerm)): ?>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h5>
                                <div class="text-muted">
                                    <?= $totalResults ?> result<?= $totalResults !== 1 ? 's' : '' ?> found
                                </div>
                            </div>

                            <?php if (empty($results)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No results found</h5>
                                    <p class="text-muted">Try adjusting your search terms or filters</p>
                                </div>
                            <?php else: ?>
                                <div id="searchResults">
                                    <?php foreach ($results as $result): ?>
                                        <div class="card mb-3 search-result-item">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title mb-1">
                                                            <a href="<?= $result['url'] ?>" class="text-decoration-none">
                                                                <?= htmlspecialchars($result['title']) ?>
                                                            </a>
                                                        </h6>
                                                        <p class="card-text text-muted mb-2">
                                                            <?= htmlspecialchars($result['description']) ?>
                                                        </p>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-<?= $this->getResultTypeColor($result['result_type']) ?> me-2">
                                                                <?= ucfirst(str_replace('_', ' ', $result['result_type'])) ?>
                                                            </span>
                                                            <small class="text-muted">
                                                                <?= date('M j, Y g:i A', strtotime($result['created_at'])) ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <a href="<?= $result['url'] ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt me-1"></i>View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Search Tips -->
                        <div class="mt-4">
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Search Tips</h5>
                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-tint fa-2x text-danger mb-2"></i>
                                                <h6>Blood Requests</h6>
                                                <p class="small text-muted">Search by blood group, location, or requester name</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                                <h6>Users</h6>
                                                <p class="small text-muted">Find donors and recipients by name or username</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <i class="fas fa-heart fa-2x text-success mb-2"></i>
                                                <h6>Donations</h6>
                                                <p class="small text-muted">Search donation records and donor information</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    let suggestionTimeout;

    // Real-time search suggestions
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(suggestionTimeout);
        
        if (query.length < 2) {
            searchSuggestions.style.display = 'none';
            return;
        }
        
        suggestionTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
            searchSuggestions.style.display = 'none';
        }
    });

    function fetchSuggestions(query) {
        fetch(`<?= APP_URL ?>search/getSuggestions?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySuggestions(data.suggestions);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
            });
    }

    function displaySuggestions(suggestions) {
        if (suggestions.length === 0) {
            searchSuggestions.style.display = 'none';
            return;
        }

        let html = '';
        suggestions.forEach(suggestion => {
            html += `
                <div class="suggestion-item p-2 border-bottom" onclick="selectSuggestion('${suggestion.text}')">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-${getSuggestionIcon(suggestion.type)} text-muted me-2"></i>
                        <div>
                            <div class="fw-bold">${suggestion.text}</div>
                            <small class="text-muted">${suggestion.display}</small>
                        </div>
                    </div>
                </div>
            `;
        });

        searchSuggestions.innerHTML = html;
        searchSuggestions.style.display = 'block';
    }

    function getSuggestionIcon(type) {
        switch (type) {
            case 'blood_group': return 'tint';
            case 'location': return 'map-marker-alt';
            case 'user': return 'user';
            default: return 'search';
        }
    }
});

function selectSuggestion(text) {
    document.getElementById('searchInput').value = text;
    document.getElementById('searchSuggestions').style.display = 'none';
    document.getElementById('searchForm').submit();
}

function getResultTypeColor(type) {
    switch (type) {
        case 'blood_request': return 'danger';
        case 'user': return 'primary';
        case 'donation': return 'success';
        default: return 'secondary';
    }
}
</script>

<style>
.search-result-item {
    transition: transform 0.2s, box-shadow 0.2s;
}

.search-result-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.suggestion-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

#searchSuggestions {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
}

@media (max-width: 768px) {
    .col-md-8 {
        margin-bottom: 1rem;
    }
}
</style>

<?php include 'views/layout/footer.php'; ?>
