function openDonationPopup(eventId) {
    var modal = document.getElementById("donationModal" + eventId);
    modal.style.display = "block";
}

// Function to close the donation popup

function closeDonationPopup() {
    var modal = document.querySelector(".modal");
    modal.style.display = "none";
}


// Close the donation popup when clicking outside of it
window.onclick = function(event) {
    if (event.target.className === "modal") {
        event.target.style.display = "none";
    }
};

// Get all buttons with class "btn-give-donation"
var donationButtons = document.querySelectorAll(".btn-give-donation");

// Add event listeners to each button
donationButtons.forEach(function(button) {
    button.addEventListener("click", function() {
        var eventId = this.getAttribute("data-event-id");
        openDonationPopup(eventId);
    });
});