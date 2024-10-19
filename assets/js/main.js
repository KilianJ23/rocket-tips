// HEADER -- Burger Menu

const hamburgerToggler = document.querySelector(".hamburger");
const navLinksContainer = document.querySelector(".navlinks-container");

// Animating the burger menu
const toggleNav = e => {
  
  hamburgerToggler.classList.toggle("open")
  
  //Aria Label management
  const ariaToggle = hamburgerToggler.getAttribute("aria-expanded") === "true" ? "false" : "true";
  hamburgerToggler.setAttribute("aria-expanded", ariaToggle);
  navLinksContainer.classList.toggle("open")
}
hamburgerToggler.addEventListener("click", toggleNav)

//Setting up resize management so that screen size changes are not visible on the burger menu

const handleResize = () => {
  if (window.innerWidth <= 768){
    navLinksContainer.style.transition = "transform 0.4s ease-out";
  }
  else {
    navLinksContainer.style.transition = "none";
  }
};
window.addEventListener("resize", handleResize);


// LOGO ANIMATION

document.addEventListener('DOMContentLoaded', function() {
    const logo = document.querySelector('.nav-icon');
    
    function bounceLogo() {
        // Adding the "bounce" class to initiate the animation
        logo.classList.add('bounce');
        
        // Removing the "bounce" class after the animation duration (0.5s)
        setTimeout(() => {
            logo.classList.remove('bounce');
        }, 500);
    }

    // Triggering the jump every 10 seconds
    setInterval(bounceLogo, 10000);
});
