(() => {
  document.documentElement.dataset.theme = 'dark';
  const alerts = document.querySelectorAll('.admin-alert');
  alerts.forEach((alert) => {
    alert.setAttribute('role', 'status');
    alert.setAttribute('aria-live', 'polite');
    window.setTimeout(() => {
      alert.classList.add('admin-alert-dismissed');
      window.setTimeout(() => alert.remove(), 350);
    }, 5000);
  });
  const forms = [...document.querySelectorAll('form[data-confirm]')];
  if (!forms.length) return;

  const dialog = document.createElement('dialog');
  dialog.className = 'admin-confirm-dialog';
  dialog.setAttribute('aria-labelledby', 'admin-confirm-title');
  dialog.innerHTML = '<form method="dialog" class="admin-confirm-card"><span class="admin-confirm-icon" aria-hidden="true">!</span><div class="admin-confirm-copy"><p class="admin-confirm-eyebrow">Confirmation requise</p><h2 id="admin-confirm-title">Confirmer cette action</h2><p class="admin-confirm-message"></p></div><div class="admin-confirm-actions"><button type="submit" value="cancel" class="admin-button admin-ghost">Annuler</button><button type="submit" value="confirm" class="admin-button admin-red">Confirmer</button></div></form>';
  document.body.append(dialog);

  let pendingForm = null;
  let pendingSubmitter = null;
  forms.forEach((form) => form.addEventListener('submit', (event) => {
    if (form.dataset.confirmed === 'true') {
      delete form.dataset.confirmed;
      return;
    }
    event.preventDefault();
    pendingForm = form;
    pendingSubmitter = event.submitter || form.querySelector('[type="submit"]');
    dialog.querySelector('.admin-confirm-message').textContent = form.dataset.confirm || 'Confirmer cette action ?';
    const confirmButton = dialog.querySelector('[value="confirm"]');
    const isApproval = pendingSubmitter?.value === 'valide';
    const isRejection = pendingSubmitter?.value === 'rejete';
    const isDelete = /supprimer|suppression/i.test(form.dataset.confirm || '');
    dialog.querySelector('.admin-confirm-icon').textContent = isApproval ? '✓' : isRejection || isDelete ? '!' : 'i';
    dialog.querySelector('#admin-confirm-title').textContent = isApproval ? 'Valider ce laboratoire ?' : isRejection ? 'Rejeter cette inscription ?' : isDelete ? 'Confirmer la suppression' : 'Confirmer cette action';
    confirmButton.textContent = isApproval ? 'Valider le laboratoire' : isRejection ? 'Rejeter l’inscription' : isDelete ? 'Supprimer définitivement' : 'Confirmer';
    confirmButton.classList.toggle('admin-green', isApproval);
    confirmButton.classList.toggle('admin-red', !isApproval);
    if (typeof dialog.showModal === 'function') dialog.showModal();
    else if (window.confirm(form.dataset.confirm || 'Confirmer cette action ?')) {
      form.dataset.confirmed = 'true';
      form.requestSubmit(event.submitter);
    }
  }));

  dialog.addEventListener('close', () => {
    if (dialog.returnValue === 'confirm' && pendingForm) {
      pendingForm.dataset.confirmed = 'true';
      pendingForm.requestSubmit(pendingSubmitter instanceof HTMLButtonElement ? pendingSubmitter : undefined);
    }
    pendingForm = null;
    pendingSubmitter = null;
  });
})();
