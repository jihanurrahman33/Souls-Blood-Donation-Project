<?php $pageTitle = 'Create Post'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Create New Post
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= APP_URL ?>forum/create" id="createPostForm">
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading me-1"></i>Title
                        </label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>" 
                               placeholder="Enter your post title" maxlength="255" required>
                        <div class="invalid-feedback" id="titleError"></div>
                        <small class="form-text text-muted">Maximum 255 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">
                            <i class="fas fa-edit me-1"></i>Content
                        </label>
                        <textarea class="form-control" id="content" name="content" rows="8" 
                                  placeholder="Share your thoughts, questions, or experiences..." 
                                  maxlength="5000" required><?= htmlspecialchars($_SESSION['form_data']['content'] ?? '') ?></textarea>
                        <div class="invalid-feedback" id="contentError"></div>
                        <small class="form-text text-muted">Maximum 5000 characters</small>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= APP_URL ?>forum" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Create Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Posting Guidelines -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Posting Tips
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Be clear and specific in your title
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Provide relevant details in your content
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Ask questions to encourage discussion
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Share personal experiences when appropriate
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>Be respectful and constructive
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createPostForm');
    const titleInput = document.getElementById('title');
    const contentInput = document.getElementById('content');
    
    // Character counter
    function updateCharCount(input, maxLength) {
        const remaining = maxLength - input.value.length;
        const counter = input.parentNode.querySelector('.form-text');
        if (counter) {
            counter.textContent = `${remaining} characters remaining`;
            if (remaining < 50) {
                counter.classList.add('text-warning');
            } else {
                counter.classList.remove('text-warning');
            }
        }
    }
    
    titleInput.addEventListener('input', function() {
        updateCharCount(this, 255);
    });
    
    contentInput.addEventListener('input', function() {
        updateCharCount(this, 5000);
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Title validation
        const title = titleInput.value.trim();
        if (!title) {
            showError('title', 'Title is required');
            isValid = false;
        } else if (title.length < 5) {
            showError('title', 'Title must be at least 5 characters long');
            isValid = false;
        } else if (title.length > 255) {
            showError('title', 'Title is too long');
            isValid = false;
        } else {
            clearError('title');
        }
        
        // Content validation
        const content = contentInput.value.trim();
        if (!content) {
            showError('content', 'Content is required');
            isValid = false;
        } else if (content.length < 10) {
            showError('content', 'Content must be at least 10 characters long');
            isValid = false;
        } else if (content.length > 5000) {
            showError('content', 'Content is too long');
            isValid = false;
        } else {
            clearError('content');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    function showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        input.classList.add('is-invalid');
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
    
    function clearError(fieldId) {
        const input = document.getElementById(fieldId);
        input.classList.remove('is-invalid');
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
    
    // Initialize character counters
    updateCharCount(titleInput, 255);
    updateCharCount(contentInput, 5000);
});
</script>

<?php include 'views/layout/footer.php'; ?> 