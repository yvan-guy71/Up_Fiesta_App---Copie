// Utilitaires pour la gestion des formulaires avancés

export function initForms() {
    // Initialiser tous les formulaires avec validation
    document.querySelectorAll('form[data-validate]').forEach(form => {
        initFormValidation(form);
    });
    
    // Initialiser les formulaires avec upload de fichiers
    document.querySelectorAll('form[data-upload]').forEach(form => {
        initFileUpload(form);
    });
    
    // Initialiser les formulaires avec autocomplete
    document.querySelectorAll('[data-autocomplete]').forEach(input => {
        initAutocomplete(input);
    });
    
    // Initialiser les formulaires avec date picker
    document.querySelectorAll('[data-datepicker]').forEach(input => {
        initDatePicker(input);
    });
    
    // Initialiser les formulaires avec éditeur de texte
    document.querySelectorAll('[data-editor]').forEach(textarea => {
        initTextEditor(textarea);
    });
}

function initFormValidation(form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        // Validation en temps réel
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => clearFieldError(input));
    });
    
    // Validation à la soumission
    form.addEventListener('submit', (event) => {
        if (!validateForm(form)) {
            event.preventDefault();
            showFormError(form, 'Veuillez corriger les erreurs dans le formulaire.');
        }
    });
}

function validateField(field) {
    const rules = getFieldRules(field);
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Validation required
    if (rules.required && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire.';
    }
    
    // Validation email
    if (rules.email && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Veuillez entrer une adresse email valide.';
    }
    
    // Validation téléphone
    if (rules.phone && value && !isValidPhone(value)) {
        isValid = false;
        errorMessage = 'Veuillez entrer un numéro de téléphone valide.';
    }
    
    // Validation longueur
    if (rules.minLength && value.length < rules.minLength) {
        isValid = false;
        errorMessage = `Ce champ doit contenir au moins ${rules.minLength} caractères.`;
    }
    
    if (rules.maxLength && value.length > rules.maxLength) {
        isValid = false;
        errorMessage = `Ce champ ne peut pas dépasser ${rules.maxLength} caractères.`;
    }
    
    // Validation numérique
    if (rules.numeric && value && !isValidNumber(value)) {
        isValid = false;
        errorMessage = 'Veuillez entrer une valeur numérique valide.';
    }
    
    // Validation pattern
    if (rules.pattern && value && !new RegExp(rules.pattern).test(value)) {
        isValid = false;
        errorMessage = rules.patternMessage || 'Le format de ce champ est invalide.';
    }
    
    // Afficher/masquer l'erreur
    if (isValid) {
        clearFieldError(field);
    } else {
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function getFieldRules(field) {
    const rules = {};
    
    // Récupérer les règles depuis les attributs data
    if (field.dataset.required !== undefined) rules.required = true;
    if (field.dataset.email !== undefined) rules.email = true;
    if (field.dataset.phone !== undefined) rules.phone = true;
    if (field.dataset.minLength) rules.minLength = parseInt(field.dataset.minLength);
    if (field.dataset.maxLength) rules.maxLength = parseInt(field.dataset.maxLength);
    if (field.dataset.numeric !== undefined) rules.numeric = true;
    if (field.dataset.pattern) rules.pattern = field.dataset.pattern;
    if (field.dataset.patternMessage) rules.patternMessage = field.dataset.patternMessage;
    
    // Récupérer les règles depuis les attributs HTML5
    if (field.required) rules.required = true;
    if (field.type === 'email') rules.email = true;
    if (field.type === 'tel') rules.phone = true;
    if (field.min) rules.minLength = parseInt(field.min);
    if (field.max) rules.maxLength = parseInt(field.max);
    if (field.type === 'number') rules.numeric = true;
    if (field.pattern) rules.pattern = field.pattern;
    
    return rules;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
}

function clearFieldError(field) {
    field.classList.remove('error');
    
    const errorElement = field.parentNode.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

function validateForm(form) {
    const inputs = form.querySelectorAll('input, select, textarea');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function showFormError(form, message) {
    const existingError = form.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }
    
    const errorElement = document.createElement('div');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    
    form.insertBefore(errorElement, form.firstChild);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        errorElement.remove();
    }, 5000);
}

function initFileUpload(form) {
    const fileInput = form.querySelector('input[type="file"]');
    const dropZone = form.querySelector('[data-drop-zone]');
    const preview = form.querySelector('[data-preview]');
    
    if (!fileInput) return;
    
    // Gestion du drag & drop
    if (dropZone) {
        setupDropZone(dropZone, fileInput, preview);
    }
    
    // Gestion du changement de fichier
    fileInput.addEventListener('change', (event) => {
        handleFileSelect(event.target.files, preview);
    });
}

function setupDropZone(dropZone, fileInput, preview) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
    });
    
    dropZone.addEventListener('drop', (event) => {
        const files = event.dataTransfer.files;
        handleFileSelect(files, preview);
        fileInput.files = files;
    }, false);
    
    dropZone.addEventListener('click', () => fileInput.click());
}

function preventDefaults(event) {
    event.preventDefault();
    event.stopPropagation();
}

function handleFileSelect(files, preview) {
    if (!preview) return;
    
    preview.innerHTML = '';
    
    Array.from(files).forEach(file => {
        const fileElement = createFilePreview(file);
        preview.appendChild(fileElement);
    });
}

function createFilePreview(file) {
    const div = document.createElement('div');
    div.className = 'file-preview';
    
    if (file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        div.appendChild(img);
    } else {
        const icon = document.createElement('div');
        icon.className = 'file-icon';
        icon.innerHTML = '<i class="fas fa-file"></i>';
        div.appendChild(icon);
    }
    
    const info = document.createElement('div');
    info.className = 'file-info';
    info.innerHTML = `
        <div class="file-name">${file.name}</div>
        <div class="file-size">${formatFileSize(file.size)}</div>
    `;
    div.appendChild(info);
    
    const remove = document.createElement('button');
    remove.className = 'file-remove';
    remove.innerHTML = '<i class="fas fa-times"></i>';
    remove.addEventListener('click', () => div.remove());
    div.appendChild(remove);
    
    return div;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function initAutocomplete(input) {
    const url = input.dataset.autocomplete;
    let timeout;
    let currentResults = [];
    
    input.addEventListener('input', () => {
        clearTimeout(timeout);
        const query = input.value.trim();
        
        if (query.length < 2) {
            hideAutocompleteResults();
            return;
        }
        
        timeout = setTimeout(() => {
            fetchAutocompleteResults(url, query);
        }, 300);
    });
    
    input.addEventListener('blur', () => {
        setTimeout(hideAutocompleteResults, 200);
    });
    
    input.addEventListener('keydown', (event) => {
        handleAutocompleteKeydown(event, input);
    });
}

async function fetchAutocompleteResults(url, query) {
    try {
        const response = await fetch(`${url}?q=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
            }
        });
        
        if (response.ok) {
            const results = await response.json();
            currentResults = results;
            showAutocompleteResults(results, query);
        }
    } catch (error) {
        console.error('Autocomplete error:', error);
    }
}

function showAutocompleteResults(results, query) {
    hideAutocompleteResults();
    
    if (results.length === 0) return;
    
    const container = document.createElement('div');
    container.className = 'autocomplete-results';
    
    results.forEach(result => {
        const item = document.createElement('div');
        item.className = 'autocomplete-item';
        item.textContent = result.label || result.name || result.text;
        item.dataset.value = result.value || result.id;
        
        item.addEventListener('click', () => {
            selectAutocompleteResult(result);
        });
        
        container.appendChild(item);
    });
    
    document.body.appendChild(container);
    
    // Positionner le conteneur
    const input = document.querySelector('[data-autocomplete]');
    const rect = input.getBoundingClientRect();
    container.style.top = `${rect.bottom + window.scrollY}px`;
    container.style.left = `${rect.left + window.scrollX}px`;
    container.style.width = `${rect.width}px`;
}

function hideAutocompleteResults() {
    const container = document.querySelector('.autocomplete-results');
    if (container) {
        container.remove();
    }
}

function selectAutocompleteResult(result) {
    const input = document.querySelector('[data-autocomplete]');
    input.value = result.label || result.name || result.text;
    
    // Déclencher l'événement change
    input.dispatchEvent(new Event('change'));
    
    hideAutocompleteResults();
}

function handleAutocompleteKeydown(event, input) {
    const container = document.querySelector('.autocomplete-results');
    if (!container) return;
    
    const items = container.querySelectorAll('.autocomplete-item');
    const currentIndex = Array.from(items).findIndex(item => item.classList.contains('selected'));
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectNextItem(items, currentIndex);
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectPreviousItem(items, currentIndex);
            break;
        case 'Enter':
            event.preventDefault();
            const selectedItem = container.querySelector('.autocomplete-item.selected');
            if (selectedItem) {
                selectedItem.click();
            }
            break;
        case 'Escape':
            hideAutocompleteResults();
            break;
    }
}

function selectNextItem(items, currentIndex) {
    const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
    selectAutocompleteItem(items, nextIndex);
}

function selectPreviousItem(items, currentIndex) {
    const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
    selectAutocompleteItem(items, prevIndex);
}

function selectAutocompleteItem(items, index) {
    items.forEach(item => item.classList.remove('selected'));
    items[index].classList.add('selected');
    items[index].scrollIntoView({ block: 'nearest' });
}

function initDatePicker(input) {
    // Utiliser Flatpickr si disponible
    if (typeof flatpickr !== 'undefined') {
        const options = {
            dateFormat: 'Y-m-d',
            locale: 'fr',
            allowInput: true,
        };
        
        // Options personnalisées depuis les attributs data
        if (input.dataset.minDate) options.minDate = input.dataset.minDate;
        if (input.dataset.maxDate) options.maxDate = input.dataset.maxDate;
        if (input.dataset.dateFormat) options.dateFormat = input.dataset.dateFormat;
        if (input.dataSet.enableTime) options.enableTime = true;
        if (input.dataset.timeFormat) options.timeFormat = input.dataset.timeFormat;
        
        flatpickr(input, options);
    }
}

function initTextEditor(textarea) {
    // Implémentation simple d'un éditeur de texte
    const toolbar = document.createElement('div');
    toolbar.className = 'editor-toolbar';
    toolbar.innerHTML = `
        <button type="button" data-command="bold"><i class="fas fa-bold"></i></button>
        <button type="button" data-command="italic"><i class="fas fa-italic"></i></button>
        <button type="button" data-command="underline"><i class="fas fa-underline"></i></button>
        <button type="button" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
        <button type="button" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
        <button type="button" data-command="createLink"><i class="fas fa-link"></i></button>
    `;
    
    textarea.parentNode.insertBefore(toolbar, textarea);
    
    toolbar.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (button) {
            executeEditorCommand(button.dataset.command);
            textarea.focus();
        }
    });
}

function executeEditorCommand(command) {
    document.execCommand(command, false, null);
}

// Fonctions utilitaires de validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/;
    return phoneRegex.test(phone);
}

function isValidNumber(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
}

// Export des fonctions
export {
    initForms,
    validateField,
    validateForm,
    showFieldError,
    clearFieldError,
};
