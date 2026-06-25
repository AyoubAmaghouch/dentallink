/**
 * DentalLink — Register Page JavaScript
 * Handles: Multi-select, Password UX, Image Upload Preview
 */

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================
       1. MULTI-SELECT COMPONENT
       ========================================= */

    const container   = document.getElementById('services-container');
    const trigger     = document.getElementById('services-trigger');
    const dropdown    = document.getElementById('services-dropdown');
    const tagsEl      = document.getElementById('selected-tags');
    const placeholder = document.getElementById('services-placeholder');
    const hiddenSel   = document.getElementById('services');
    const items       = dropdown ? dropdown.querySelectorAll('.dropdown-item') : [];

    if (trigger && container) {

        // Toggle on click
        trigger.addEventListener('click', function (e) {
            if (e.target.classList.contains('tag-remove')) return;
            const isOpen = container.classList.toggle('active');
            trigger.setAttribute('aria-expanded', isOpen);
        });

        // Keyboard support on trigger
        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                trigger.click();
            }
            if (e.key === 'Escape') {
                container.classList.remove('active');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!container.contains(e.target)) {
                container.classList.remove('active');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        // Handle item selection
        items.forEach(function (item) {
            item.addEventListener('click', function () {
                const value = this.dataset.value;
                const opt   = hiddenSel.querySelector(`option[value="${value}"]`);
                const sel   = this.classList.toggle('selected');
                this.setAttribute('aria-selected', sel);
                if (opt) opt.selected = sel;
                renderTags();
            });

            item.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); item.click(); }
                if (e.key === 'Escape') { container.classList.remove('active'); trigger.focus(); }
            });
        });

        function renderTags() {
            tagsEl.innerHTML = '';
            const selected = Array.from(hiddenSel.selectedOptions);
            if (selected.length === 0) {
                placeholder.style.display = '';
            } else {
                placeholder.style.display = 'none';
                selected.forEach(function (opt) {
                    const tag = document.createElement('span');
                    tag.className = 'tag';
                    tag.dataset.value = opt.value;
                    tag.innerHTML = `${escHtml(opt.text)}<button type="button" class="tag-remove" aria-label="Retirer ${escHtml(opt.text)}">&times;</button>`;
                    tag.querySelector('.tag-remove').addEventListener('click', function (e) {
                        e.stopPropagation();
                        const dropItem = dropdown.querySelector(`.dropdown-item[data-value="${opt.value}"]`);
                        if (dropItem) { dropItem.classList.remove('selected'); dropItem.setAttribute('aria-selected', 'false'); }
                        opt.selected = false;
                        renderTags();
                    });
                    tagsEl.appendChild(tag);
                });
            }
        }

        // Reset form handler
        const form = document.getElementById('register-form');
        if (form) {
            form.addEventListener('reset', function () {
                setTimeout(function () {
                    items.forEach(function (i) { i.classList.remove('selected'); i.setAttribute('aria-selected', 'false'); });
                    renderTags();
                }, 10);
            });
        }

        renderTags();
    }


    /* =========================================
       2. SHOW / HIDE PASSWORD
       ========================================= */

    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            const show = target.type === 'password';
            target.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Masquer' : 'Afficher';
        });
    });


    /* =========================================
       3. PASSWORD STRENGTH
       ========================================= */

    const pwInput     = document.getElementById('mot_de_passe');
    const confirmInput= document.getElementById('confirmer_mot_de_passe');
    const fillEl      = document.getElementById('strength-fill');
    const labelEl     = document.getElementById('strength-label');
    const matchLabel  = document.getElementById('match-label');

    if (pwInput && fillEl && labelEl) {
        pwInput.addEventListener('input', function () {
            const val = pwInput.value;
            const score = getStrength(val);
            const cfg = [
                { w: '0%',   bg: 'transparent',          txt: '' },
                { w: '25%',  bg: '#EF4444',               txt: 'Très faible' },
                { w: '50%',  bg: '#FB923C',               txt: 'Faible' },
                { w: '75%',  bg: '#FBBF24',               txt: 'Moyen' },
                { w: '100%', bg: '#22C55E',               txt: 'Fort' },
            ][score];
            fillEl.style.width           = cfg.w;
            fillEl.style.backgroundColor = cfg.bg;
            labelEl.textContent          = cfg.txt;
            labelEl.style.color          = cfg.bg;
            checkMatch();
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', checkMatch);
    }

    function checkMatch() {
        if (!pwInput || !confirmInput || !matchLabel) return;
        if (confirmInput.value === '') { matchLabel.textContent = ''; return; }
        if (pwInput.value === confirmInput.value) {
            matchLabel.textContent = 'Les mots de passe correspondent';
            matchLabel.style.color = '#22C55E';
        } else {
            matchLabel.textContent = 'Les mots de passe ne correspondent pas';
            matchLabel.style.color = '#EF4444';
        }
    }

    function getStrength(pw) {
        if (!pw) return 0;
        let s = 0;
        if (pw.length >= 8)                    s++;
        if (/[A-Z]/.test(pw))                  s++;
        if (/[0-9]/.test(pw))                  s++;
        if (/[^A-Za-z0-9]/.test(pw))           s++;
        return s;
    }


    /* =========================================
       4. IMAGE UPLOAD PREVIEW
       ========================================= */

    const uploadArea   = document.getElementById('upload-area');
    const imageInput   = document.getElementById('images');
    const previewGrid  = document.getElementById('preview-grid');
    const counterEl    = document.getElementById('image-counter');
    const counterText  = document.getElementById('counter-text');
    const MAX_IMAGES   = 12;

    // We maintain a local array of File objects so the user can remove items
    let selectedFiles = [];

    if (imageInput && previewGrid) {

        // Drag-over visual
        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });
        uploadArea.addEventListener('dragleave', function () {
            uploadArea.classList.remove('drag-over');
        });
        uploadArea.addEventListener('drop', function () {
            uploadArea.classList.remove('drag-over');
        });

        imageInput.addEventListener('change', function () {
            const incoming = Array.from(this.files);
            const remaining = MAX_IMAGES - selectedFiles.length;

            if (incoming.length === 0) return;

            if (incoming.length > remaining) {
                alert(`Vous pouvez encore ajouter ${remaining} photo(s). Maximum ${MAX_IMAGES} au total.`);
            }

            const toAdd = incoming.slice(0, remaining);
            selectedFiles = selectedFiles.concat(toAdd);
            syncNativeInput();
            renderPreviews();

            // reset so same files can be re-selected if needed
            this.value = '';
        });

        function renderPreviews() {
            previewGrid.innerHTML = '';

            if (selectedFiles.length === 0) {
                counterEl.style.display = 'none';
                return;
            }

            counterEl.style.display = '';
            counterText.textContent = `${selectedFiles.length} / ${MAX_IMAGES} photo${selectedFiles.length > 1 ? 's' : ''} sélectionnée${selectedFiles.length > 1 ? 's' : ''}`;

            selectedFiles.forEach(function (file, idx) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const card = document.createElement('div');
                    card.className = 'preview-card';
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="${escHtml(file.name)}">
                        <button type="button" class="remove-btn" data-idx="${idx}" aria-label="Retirer la photo">&times;</button>
                    `;
                    card.querySelector('.remove-btn').addEventListener('click', function () {
                        selectedFiles.splice(parseInt(this.dataset.idx), 1);
                        syncNativeInput();
                        renderPreviews();
                    });
                    previewGrid.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }

        function syncNativeInput() {
            // Build a new DataTransfer to assign our array to the real input
            try {
                const dt = new DataTransfer();
                selectedFiles.forEach(function (f) { dt.items.add(f); });
                imageInput.files = dt.files;
            } catch (e) {
                // DataTransfer not supported — the hidden select is still populated
            }
        }
    }


    /* =========================================
       5. RESET HANDLER
       ========================================= */

    const form = document.getElementById('register-form');
    if (form) {
        form.addEventListener('reset', function () {
            setTimeout(function () {
                selectedFiles = [];
                if (previewGrid) previewGrid.innerHTML = '';
                if (counterEl) counterEl.style.display = 'none';
                if (fillEl) { fillEl.style.width = '0%'; fillEl.style.backgroundColor = 'transparent'; }
                if (labelEl) labelEl.textContent = '';
                if (matchLabel) matchLabel.textContent = '';
            }, 10);
        });
    }


    /* =========================================
       UTILS
       ========================================= */

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

});