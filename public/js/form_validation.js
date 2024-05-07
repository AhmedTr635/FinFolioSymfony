document.addEventListener('DOMContentLoaded', function() {
    const recentValueInput = document.getElementById('digital_coins_recentValue');
    const errorSpan = document.getElementById('recent-value-error');

    recentValueInput.addEventListener('input', function(event) {
        const inputValue = event.target.value;

        // Perform AJAX request to validate the input
        const formData = new FormData();
        formData.append('recentValue', inputValue);

        fetch('/validate_recent_value', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    errorSpan.textContent = ''; // Clear error message
                    recentValueInput.classList.remove('invalid');
                } else {
                    errorSpan.textContent = data.message; // Display error message
                    recentValueInput.classList.add('invalid');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});
