document.addEventListener("DOMContentLoaded", () => {
  // Initialize Lucide icons
  lucide.createIcons()

  // Set current year in footer
  document.getElementById("currentYear").textContent = new Date().getFullYear()

  // Mobile menu functionality
  const menuButton = document.getElementById("menuButton")
  const closeMenuButton = document.getElementById("closeMenuButton")
  const mobileMenu = document.getElementById("mobileMenu")
  const mobileMenuLinks = document.querySelectorAll(".mobile-nav .nav-link")

  function openMenu() {
    mobileMenu.classList.add("open")
    document.body.style.overflow = "hidden" // Prevent scrolling when menu is open
  }

  function closeMenu() {
    mobileMenu.classList.remove("open")
    document.body.style.overflow = "" // Restore scrolling
  }

  menuButton.addEventListener("click", openMenu)
  closeMenuButton.addEventListener("click", closeMenu)

  // Close menu when clicking on a link
  mobileMenuLinks.forEach((link) => {
    link.addEventListener("click", closeMenu)
  })

  // Close menu when clicking outside
  document.addEventListener("click", (event) => {
    const isClickInsideMenu = mobileMenu.contains(event.target)
    const isClickOnMenuButton = menuButton.contains(event.target)

    if (mobileMenu.classList.contains("open") && !isClickInsideMenu && !isClickOnMenuButton) {
      closeMenu()
    }
  })

  // Handle search form submission
  const searchForm = document.querySelector(".search-box")
  const searchInput = document.querySelector(".search-input")
  const searchButton = document.querySelector(".search-button")

  searchButton.addEventListener("click", (event) => {
    event.preventDefault()
    if (searchInput.value.trim() !== "") {
      alert("Búsqueda: " + searchInput.value)
      // En una aplicación real, aquí redirigirías a la página de resultados
      // window.location.href = '/busqueda?q=' + encodeURIComponent(searchInput.value);
    }
  })

  // Add hover effects to cards
  const cards = document.querySelectorAll(".card")
  cards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-5px)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
    })
  })
})
