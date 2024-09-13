// HEADER -- Menu Burger

const hamburgerToggler = document.querySelector(".hamburger");
const navLinksContainer = document.querySelector(".navlinks-container");
// Animation du burger menu
const toggleNav = e => {
  
  hamburgerToggler.classList.toggle("open")
  
  //Gestion du Aria Label
  const ariaToggle = hamburgerToggler.getAttribute("aria-expanded") === "true" ? "false" : "true";
  hamburgerToggler.setAttribute("aria-expanded", ariaToggle);
  navLinksContainer.classList.toggle("open")
}
hamburgerToggler.addEventListener("click", toggleNav)

//Mise en place de la gestion de resize pour que les changements de dimension de l'écran ne se voient pas sur le menu burger

const handleResize = () => {
  if (window.innerWidth <= 768){
    navLinksContainer.style.transition = "transform 0.4s ease-out";
  }
  else {
    navLinksContainer.style.transition = "none";
  }
};
window.addEventListener("resize", handleResize);

