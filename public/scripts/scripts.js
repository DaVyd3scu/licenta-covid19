// Selecting elements
let selectElement = (s) => document.querySelector(s);
// Open the menu on click
selectElement('.open').addEventListener('click', () => {
    selectElement('.nav-list').classList.add('active');
})
// Close the menu on click
selectElement('.close').addEventListener('click', () => {
    selectElement('.nav-list').classList.remove('active');
})

let dropdown = $(".dropdown");

$(document).ready(function(){
    dropdown.click(function (){
        $(".sub-nav-list").show(300);
        $(".dropdown  i").first().css("transform", "rotate(90deg)");
    });
    $(".first-item .nav-link").click(function () {
        $(".dropdown-content").show(300);
    });
    if(window.matchMedia("(min-width: 700px)").matches) {
        $(".first-item").mouseleave(function () {
            if ($(".dropdown-content:hover").length === 0) {
                $(".dropdown-content").hide(300);
            }
        });
        $(".dropdown-content").mouseleave(function () {
            $(this).hide(300)
        })
    }
})
