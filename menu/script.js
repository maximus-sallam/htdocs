// JavaScript for interactivity
document.addEventListener("DOMContentLoaded", () => {
    const menuItems = document.querySelectorAll('.menu-item');

    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            alert(`You've selected ${item.querySelector('h2').innerText}! Enjoy your drink!`);
        });
    });
});
