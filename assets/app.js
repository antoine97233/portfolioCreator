import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Fonction pour afficher ou masquer le bouton "Retour en haut de page"
function toggleTopButton() {
  let mybutton = document.getElementById("myBtn");
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

// Fonction pour faire défiler la page vers le haut lorsque le bouton est cliqué
function scrollToTop() {
  document.body.scrollTop = 0; // Pour Safari
  document.documentElement.scrollTop = 0; // Pour Chrome, Firefox, IE et Opera
}

// Écouteur d'événement pour faire défiler la page vers le haut lorsque le bouton est cliqué
let mybutton = document.getElementById("myBtn");
if (mybutton) {
  mybutton.addEventListener("click", scrollToTop);
}


// Écouteur d'événement pour déclencher la fonction du bouton de retour en haut de la page
window.addEventListener("scroll", toggleTopButton);
