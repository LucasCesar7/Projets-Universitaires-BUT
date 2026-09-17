// admin-category-form.js

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('categoryForm');
    const descriptionInput = document.getElementById('description');
    const charCounter = document.getElementById('charCounter');
    const imageUpload = document.getElementById('image');
    const fileUploadArea = document.querySelector('.file-upload');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const platformOptions = document.querySelectorAll('.platform-option');
    const backToListLink = document.querySelector('.admin-page-header .btn.btn-secondary');
    const manageCategoriesUrl = backToListLink?.getAttribute('href') || 'admin/categories';

    function withSuccessQuery(url) {
        const separator = url.includes('?') ? '&' : '?';
        return `${url}${separator}success=1`;
    }
    const platformSelect = document.getElementById('plateforme_cible');
    const submitBtn = document.getElementById('submitBtn');
    
    // Character counter for description
    if (descriptionInput && charCounter) {
        descriptionInput.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 2000;
            charCounter.textContent = `${length} / ${maxLength} caractères`;
            
            if (length > maxLength * 0.9) {
                charCounter.classList.add('warning');
            } else {
                charCounter.classList.remove('warning');
            }
            
            if (length > maxLength) {
                charCounter.classList.add('error');
                charCounter.textContent += ' - Limite dépassée!';
            } else {
                charCounter.classList.remove('error');
            }
        });
        
        // Initial count
        descriptionInput.dispatchEvent(new Event('input'));
    }
    
    // File upload preview and drag & drop
    if (imageUpload && fileUploadArea && previewImage) {
        // Click on area triggers file input
        fileUploadArea.addEventListener('click', function(e) {
            if (e.target !== imageUpload) {
                imageUpload.click();
            }
        });
        
        // File selection
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                previewFile(file);
            }
        });
        
        // Drag & drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            fileUploadArea.classList.add('dragover');
        }
        
        function unhighlight() {
            fileUploadArea.classList.remove('dragover');
        }
        
        fileUploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            if (file && file.type.startsWith('image/')) {
                imageUpload.files = dt.files;
                previewFile(file);
            }
        });
        
        function previewFile(file) {
            if (!file.type.match('image.*')) {
                alert('Veuillez sélectionner une image valide (JPG, PNG, GIF)');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('L\'image ne doit pas dépasser 2MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                filePreview.style.display = 'block';
                fileUploadArea.querySelector('.file-upload-text').textContent = file.name;
                fileUploadArea.querySelector('.file-upload-hint').textContent = 
                    `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Platform selection
    if (platformOptions.length > 0 && platformSelect) {
        platformOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                
                // Update visual selection
                platformOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                
                // Update hidden select
                platformSelect.value = value;
            });
            
            // Set initial selection
            if (option.getAttribute('data-value') === platformSelect.value) {
                option.classList.add('selected');
            }
        });
        
        // Sync select change with visual options
        platformSelect.addEventListener('change', function() {
            platformOptions.forEach(option => {
                option.classList.remove('selected');
                if (option.getAttribute('data-value') === this.value) {
                    option.classList.add('selected');
                }
            });
        });
    }
    
    // Form validation and submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit form
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = withSuccessQuery(manageCategoriesUrl);
                } else {
                    throw new Error('Erreur lors de la création');
                }
            })
            .catch(error => {
                alert(error.message);
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
            
            // Fallback for browsers without fetch
            setTimeout(() => {
                form.submit();
            }, 3000);
        });
    }
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const nomInput = document.getElementById('nom');
        const descriptionInput = document.getElementById('description');
        const idEditionInput = document.getElementById('id_edition');
        
        // Reset previous errors
        document.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('error');
        });
        
        // Validate name
        if (!nomInput.value.trim()) {
            nomInput.classList.add('error');
            showFieldError(nomInput, 'Le nom est requis');
            isValid = false;
        } else if (nomInput.value.trim().length > 100) {
            nomInput.classList.add('error');
            showFieldError(nomInput, 'Le nom ne doit pas dépasser 100 caractères');
            isValid = false;
        }
        
        // Validate description
        if (!descriptionInput.value.trim()) {
            descriptionInput.classList.add('error');
            showFieldError(descriptionInput, 'La description est requise');
            isValid = false;
        } else if (descriptionInput.value.trim().length > 2000) {
            descriptionInput.classList.add('error');
            showFieldError(descriptionInput, 'La description ne doit pas dépasser 2000 caractères');
            isValid = false;
        }
        
        // Validate edition
        if (!idEditionInput.value || idEditionInput.value <= 0) {
            idEditionInput.classList.add('error');
            showFieldError(idEditionInput, 'Veuillez sélectionner une édition');
            isValid = false;
        }
        
        // Validate dates if provided
        const dateDebut = document.getElementById('date_debut_votes');
        const dateFin = document.getElementById('date_fin_votes');
        
        if (dateDebut.value && dateFin.value) {
            const debut = new Date(dateDebut.value);
            const fin = new Date(dateFin.value);
            
            if (debut >= fin) {
                dateDebut.classList.add('error');
                dateFin.classList.add('error');
                showFieldError(dateDebut, 'La date de début doit être avant la date de fin');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    function showFieldError(input, message) {
        // Remove existing error
        let errorElement = input.parentElement.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'field-error';
            input.parentElement.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        errorElement.style.color = 'var(--danger-color)';
        errorElement.style.fontSize = '0.875rem';
        errorElement.style.marginTop = '0.25rem';
        errorElement.style.animation = 'fadeIn 0.3s ease-out';
    }
    
    // Auto-resize textarea
    if (descriptionInput) {
        descriptionInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Set minimum date for date inputs
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="datetime-local"]').forEach(input => {
        input.min = today + 'T00:00';
    });
});