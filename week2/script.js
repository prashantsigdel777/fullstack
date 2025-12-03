// 1. Mobile Menu Toggle
const menuButton = document.getElementById("menu-button");
const navLinks = document.querySelector(".nav-links");

function toggleMenu() {
  if (!menuButton || !navLinks) return;

  navLinks.classList.toggle("open");

  const isExpanded = navLinks.classList.contains("open");
  menuButton.setAttribute("aria-expanded", isExpanded);

  // Change icon
  menuButton.textContent = isExpanded ? "✕" : "☰";
}

if (menuButton && navLinks) {
  menuButton.addEventListener("click", toggleMenu);

  // Close menu when clicking a link on mobile
  navLinks.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      if (navLinks.classList.contains("open")) {
        toggleMenu();
      }
    });
  });
}

// 2. Scroll Progress Bar
const scrollBar = document.getElementById("scroll-progress");

function updateScrollProgress() {
  if (!scrollBar) return;

  const scrollTop = window.scrollY;
  const docHeight = document.documentElement.scrollHeight - window.innerHeight;
  const scrolled = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

  scrollBar.style.width = scrolled + "%";
}

window.addEventListener("scroll", updateScrollProgress);

// 3. Form Handling / Validation
const contactForm = document.getElementById("contact-form");
const messageDiv = document.getElementById("form-message");

if (contactForm && messageDiv) {
  contactForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const nameValue = document.getElementById("name").value.trim();
    const emailValue = document.getElementById("email").value.trim();

    if (nameValue === "" || emailValue === "") {
      messageDiv.textContent = "Please fill out all required fields.";
      messageDiv.style.color = "red";
    } else {
      messageDiv.textContent =
        "Thank you for your message! I will be in touch shortly.";
      messageDiv.style.color = "green";
      contactForm.reset();
    }
  });
}

// 4. Auto-fill the year in the footer
const yearSpan = document.getElementById("year");
if (yearSpan) {
  yearSpan.textContent = new Date().getFullYear();
}
