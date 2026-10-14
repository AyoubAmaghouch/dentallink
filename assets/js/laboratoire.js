(() => {
    const protectedForms = document.querySelectorAll('form[data-app-confirm]');
    if (!protectedForms.length || typeof HTMLDialogElement === 'undefined') return;

    const dialog = document.createElement('dialog');
    dialog.className = 'app-confirm-dialog';
    dialog.setAttribute('aria-labelledby', 'app-confirm-title');
    dialog.innerHTML = '<h2 id="app-confirm-title">Confirmer l’action</h2><p data-confirm-message></p><div class="app-confirm-actions"><button type="button" data-confirm-cancel>Annuler</button><button type="button" data-confirm-submit>Confirmer</button></div>';
    document.body.append(dialog);

    let pendingForm = null;
    let trigger = null;
    const message = dialog.querySelector('[data-confirm-message]');

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-app-confirm]');
        if (!form) return;
        if (form.dataset.confirmed === 'true') {
            delete form.dataset.confirmed;
            return;
        }

        event.preventDefault();
        pendingForm = form;
        trigger = event.submitter || form.querySelector('[type="submit"]');
        message.textContent = form.dataset.appConfirm;
        dialog.showModal();
        dialog.querySelector('[data-confirm-cancel]').focus();
    });

    dialog.querySelector('[data-confirm-cancel]').addEventListener('click', () => dialog.close());
    dialog.querySelector('[data-confirm-submit]').addEventListener('click', () => {
        const form = pendingForm;
        const submitter = trigger;
        pendingForm = null;
        trigger = null;
        if (!form) return dialog.close();
        form.dataset.confirmed = 'true';
        dialog.close();
        form.requestSubmit(submitter instanceof HTMLButtonElement ? submitter : undefined);
    });
    dialog.addEventListener('close', () => {
        pendingForm = null;
        if (trigger instanceof HTMLElement) trigger.focus();
        trigger = null;
    });
})();
