// assets/js/script.js
document.addEventListener("DOMContentLoaded", function () {
  const toggle =
    document.getElementById("navToggle") ||
    document.getElementById("navToggle2") ||
    document.getElementById("navToggle3");
  if (toggle) {
    toggle.addEventListener("click", () => {
      const nav = document.querySelector(".nav-links");
      if (nav)
        nav.style.display = nav.style.display === "flex" ? "none" : "flex";
    });
  }
});
