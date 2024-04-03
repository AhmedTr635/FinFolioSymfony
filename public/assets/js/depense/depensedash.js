
//pop up add
document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('add-expense-button');
    const popup = document.getElementById('expense-popup');
    const closeButton = document.getElementById('close-expense-popup');
    const formContainer = document.getElementById('form-container');

    addButton.addEventListener('click', function() {
        popup.style.display = 'block';
        // Charger le contenu du formulaire dans le popup
        formContainer.innerHTML = '';
        fetch('/depense/new') // Modifier l'URL selon vos besoins
            .then(response => response.text())
            .then(html => {
                formContainer.innerHTML = html;
                const form = document.getElementById('expense_form');
                form.action = '/depense/new';
            });
    });

    closeButton.addEventListener('click', function() {
        popup.style.display = 'none';
    });

});
//pop up edit
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-expense-button');
    const popup = document.getElementById('expense-popup');
    const closeButton = document.getElementById('close-expense-popup');
    const formContainer = document.getElementById('form-container');

    editButtons.forEach(function(editButton) {
        editButton.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default link behavior
            const expenseId = this.getAttribute('data-id');
            popup.style.display = 'block';
            formContainer.innerHTML = '';
            fetch('/depense/' + expenseId + '/edit')
                .then(response => response.text())
                .then(html => {
                    formContainer.innerHTML = html;
                    const form = document.getElementById('expense_form');
                    form.action = '/depense/' + expenseId + '/edit';
                });
        });
    });

    closeButton.addEventListener('click', function() {
        popup.style.display = 'none';
    });

});




