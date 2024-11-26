document.getElementById("sidebarToggle").addEventListener("click", function() {
    document.getElementById("sidebar").classList.toggle("-translate-x-full");
});

document.getElementById("openModal").addEventListener("click", function() {
    document.getElementById("modal").style.display = "flex";
});

document.getElementById("closeModal").addEventListener("click", function() {
    document.getElementById("modal").style.display = "none";
});

// Menutup modal jika mengklik di luar area modal
window.addEventListener("click", function(event) {
    if (event.target == document.getElementById("modal")) {
        document.getElementById("modal").style.display = "none";
    }
});