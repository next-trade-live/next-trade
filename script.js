// DOM Content Loaded
document.addEventListener("DOMContentLoaded", () => {
  // Mobile Navigation Toggle
  const navToggle = document.querySelector(".nav-toggle")
  const navMenu = document.querySelector(".nav-menu")

  if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active")
      navToggle.classList.toggle("active")
    })
  }

  // Smooth Scrolling for Navigation Links
  const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]')
  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()
      const targetId = this.getAttribute("href")
      const targetSection = document.querySelector(targetId)

      if (targetSection) {
        targetSection.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })

        // Close mobile menu if open
        navMenu.classList.remove("active")
        navToggle.classList.remove("active")
      }
    })
  })

  // Animated Counter for Hero Stats
  function animateCounter(element, target, suffix = "") {
    let current = 0
    const increment = target / 100
    const timer = setInterval(() => {
      current += increment
      if (current >= target) {
        current = target
        clearInterval(timer)
      }

      if (suffix === "%") {
        element.textContent = Math.floor(current) + suffix
      } else if (suffix === "+") {
        element.textContent = Math.floor(current).toLocaleString() + suffix
      } else {
        element.textContent = Math.floor(current).toLocaleString()
      }
    }, 50)
  }

  // Start counter animations after a delay
  setTimeout(() => {
    const studentsCount = document.getElementById("students-count")
    const profitCount = document.getElementById("profit-count")
    const successCount = document.getElementById("success-count")

    if (studentsCount) animateCounter(studentsCount, 5800, "+")
    if (profitCount) animateCounter(profitCount, 98, "%")
    if (successCount) animateCounter(successCount, 95, "%")
  }, 1000)

  // Trading Chart Animation
  const canvas = document.getElementById("trading-chart")
  if (canvas) {
    const ctx = canvas.getContext("2d")

    // Set canvas size
    canvas.width = 200
    canvas.height = 400

    // Generate sample trading data
    function generateTradingData() {
      const data = []
      let price = 100
      for (let i = 0; i < 50; i++) {
        price += (Math.random() - 0.5) * 5
        data.push({
          x: (i / 49) * canvas.width,
          y: canvas.height - ((price - 80) / 40) * canvas.height,
          price: price,
        })
      }
      return data
    }

    const tradingData = generateTradingData()
    let animationProgress = 0

    function drawTradingChart() {
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      // Draw grid
      ctx.strokeStyle = "rgba(99, 102, 241, 0.1)"
      ctx.lineWidth = 1

      // Horizontal grid lines
      for (let i = 0; i < 10; i++) {
        const y = (i / 9) * canvas.height
        ctx.beginPath()
        ctx.moveTo(0, y)
        ctx.lineTo(canvas.width, y)
        ctx.stroke()
      }

      // Vertical grid lines
      for (let i = 0; i < 10; i++) {
        const x = (i / 9) * canvas.width
        ctx.beginPath()
        ctx.moveTo(x, 0)
        ctx.lineTo(x, canvas.height)
        ctx.stroke()
      }

      // Draw animated trading line
      const pointsToShow = Math.floor(animationProgress * tradingData.length)

      if (pointsToShow > 1) {
        // Main trading line
        ctx.strokeStyle = "#0891b2"
        ctx.lineWidth = 3
        ctx.beginPath()
        ctx.moveTo(tradingData[0].x, tradingData[0].y)

        for (let i = 1; i < pointsToShow; i++) {
          ctx.lineTo(tradingData[i].x, tradingData[i].y)
        }
        ctx.stroke()

        // Draw candlesticks
        for (let i = 0; i < pointsToShow; i += 3) {
          const point = tradingData[i]
          const isGreen = Math.random() > 0.5

          ctx.fillStyle = isGreen ? "#34d399" : "#f87171"
          ctx.fillRect(point.x - 2, point.y - 10, 4, 20)
        }

        // Draw current price indicator
        if (pointsToShow > 0) {
          const currentPoint = tradingData[pointsToShow - 1]

          // Price dot
          ctx.fillStyle = "#0891b2"
          ctx.beginPath()
          ctx.arc(currentPoint.x, currentPoint.y, 6, 0, Math.PI * 2)
          ctx.fill()

          // Price label
          ctx.fillStyle = "#ffffff"
          ctx.font = "14px monospace"
          ctx.fillText(`$${currentPoint.price.toFixed(2)}`, currentPoint.x + 10, currentPoint.y - 10)
        }
      }

      // Continue animation
      if (animationProgress < 1) {
        animationProgress += 0.02
        requestAnimationFrame(drawTradingChart)
      }
    }

    // Start chart animation
    drawTradingChart()
  }

  // Scroll-based animations
  function handleScrollAnimations() {
    const elements = document.querySelectorAll(".asset-card, .success-card, .feature-item")

    elements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top
      const elementVisible = 150

      if (elementTop < window.innerHeight - elementVisible) {
        element.style.opacity = "1"
        element.style.transform = "translateY(0)"
      }
    })
  }

  // Initialize scroll animations
  const animatedElements = document.querySelectorAll(".asset-card, .success-card, .feature-item")
  animatedElements.forEach((element) => {
    element.style.opacity = "0"
    element.style.transform = "translateY(30px)"
    element.style.transition = "all 0.6s ease"
  })

  // Listen for scroll events
  window.addEventListener("scroll", handleScrollAnimations)

  // Initial check for elements in view
  handleScrollAnimations()

  // Form Submission Handler
  const contactForm = document.querySelector(".contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault()

      // Get form data
      const formData = new FormData(this)

      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]')
      const originalText = submitBtn.textContent
      submitBtn.textContent = "Sending..."
      submitBtn.disabled = true

      // Simulate form submission (replace with actual AJAX call)
      setTimeout(() => {
        alert("Thank you for your interest! We will contact you soon.")
        this.reset()
        submitBtn.textContent = originalText
        submitBtn.disabled = false
      }, 2000)
    })
  }

  // Navbar scroll effect
  window.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar")
    if (window.scrollY > 100) {
      navbar.style.background = "rgba(15, 23, 42, 0.98)"
    } else {
      navbar.style.background = "rgba(15, 23, 42, 0.95)"
    }
  })

  // Add floating animation to trading assets
  const assetCards = document.querySelectorAll(".asset-card")
  assetCards.forEach((card, index) => {
    card.style.animationDelay = `${index * 0.2}s`
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px) scale(1.02)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)"
    })
  })

  // Success cards hover effect
  const successCards = document.querySelectorAll(".success-card")
  successCards.forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px) scale(1.02)"
      this.style.boxShadow = "0 20px 40px rgba(8, 145, 178, 0.2)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0) scale(1)"
      this.style.boxShadow = "none"
    })
  })
})

// Utility function for smooth animations
function easeInOutQuad(t) {
  return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t
}

// Add particle effect to hero section
function createParticles() {
  const heroSection = document.querySelector(".hero-section")
  if (!heroSection) return

  for (let i = 0; i < 20; i++) {
    const particle = document.createElement("div")
    particle.className = "particle"
    particle.style.cssText = `
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(8, 145, 178, 0.5);
            border-radius: 50%;
            pointer-events: none;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            animation: float ${3 + Math.random() * 4}s ease-in-out infinite;
            animation-delay: ${Math.random() * 2}s;
        `
    heroSection.appendChild(particle)
  }
}

// Initialize particles
setTimeout(createParticles, 1000)
