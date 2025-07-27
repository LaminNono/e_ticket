// Hide loader when page loads
window.addEventListener("load", function () {
  document.getElementById("loader").style.display = "none";
});

// Chatbot toggle
function toggleChatbot() {
  const chatbot = document.getElementById("chatbot-box");
  chatbot.style.display = chatbot.style.display === "none" ? "block" : "none";
}

// Form validation
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const fromSelect = document.querySelector('select[name="from"]');
  const toSelect = document.querySelector('select[name="to"]');

  if (form && fromSelect && toSelect) {
    form.addEventListener("submit", function (e) {
      if (fromSelect.value === toSelect.value && fromSelect.value !== "") {
        e.preventDefault();
        alert("Departure and destination cannot be the same!");
        return false;
      }
    });
  }
});

// Initialize AOS animations
if (typeof AOS !== "undefined") {
  AOS.init();
}
