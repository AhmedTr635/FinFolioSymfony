document.addEventListener('DOMContentLoaded', function() {
    const showButtons = document.querySelectorAll('.show-danger-card');
    showButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetCard = document.querySelector(targetId);
            if (!targetCard) {
                const parentDiv = this.closest('.card-body');
                const dangerCard = document.createElement('div');
                dangerCard.classList.add('col-md-6', 'col-xl-4', 'mt-3');
                dangerCard.id = targetId.slice(1); // Remove # from id
                dangerCard.innerHTML = `
                    <div class="card bg-danger text-white mb-3">
                        <div class="card-header cursor-move">No Virtual Tour Available</div>
                        <div class="card-body">
                            <h4 class="card-title text-white">No Virtual Tour</h4>
                            <p class="card-text">
                                We apologize, but there is no virtual tour available for this real estate.
                            </p>
                        </div>
                    </div>
                `;
                parentDiv.appendChild(dangerCard);
            } else {
                targetCard.classList.toggle('d-none');
            }
        });
    });
});
