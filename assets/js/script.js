document.addEventListener("DOMContentLoaded", function () {

    console.log("SmartCampus chargé");

    let buttons = document.querySelectorAll("button");

    buttons.forEach(function(button) {

        button.addEventListener("mouseenter", function() {
            button.style.opacity = "0.8";
        });

        button.addEventListener("mouseleave", function() {
            button.style.opacity = "1";
        });

    });

    let cards = document.querySelectorAll(".card");

    cards.forEach(function(card) {

        card.addEventListener("mouseenter", function() {
            card.style.transform = "scale(1.01)";
            card.style.transition = "0.2s";
        });

        card.addEventListener("mouseleave", function() {
            card.style.transform = "scale(1)";
        });

    });

});