function setupValidation() {
    var form = document.getElementById('user-form-container');
    if (form) {
        var datepunitionId = form.getAttribute('data-datepunition-id');
        var statutId = form.getAttribute('data-statut-id');

        var datepunitionField = document.getElementById(datepunitionId);
        var statutField = document.getElementById(statutId);

        var errorDiv = document.createElement('div');
        errorDiv.classList.add('text-danger');
        datepunitionField.parentNode.appendChild(errorDiv);

        var submitButton = form.querySelector('button[type="submit"]'); // Sélectionnez le bouton de soumission du formulaire

        function validateForm() {
            if (statutField.value === 'desactive') {
                var selectedDate = new Date(datepunitionField.value);
                var currentDate = new Date();

                if (selectedDate <= currentDate) {
                    errorDiv.textContent = "La date de punition doit être supérieure à la date d'aujourd'hui.";
                    submitButton.disabled = true; // Désactiver le bouton de soumission
                } else {
                    errorDiv.textContent = "";
                    submitButton.disabled = false; // Activer le bouton de soumission
                }
            } else {
                errorDiv.textContent = "";
                submitButton.disabled = false; // Activer le bouton de soumission
            }
        }

        // Ajouter des écouteurs d'événements sur les champs de la date de punition et du statut pour valider en temps réel
        datepunitionField.addEventListener('input', validateForm);
        statutField.addEventListener('input', validateForm);
        validateForm();
    }
}
