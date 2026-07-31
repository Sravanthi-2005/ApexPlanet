// =============================
// Typing Animation
// =============================

const text = [
    "Web Developer",
    "Java Programmer",
    "Data Science Student",
    "Frontend Developer"
];

let index = 0;
let charIndex = 0;
let currentText = "";
let isDeleting = false;

function typeEffect() {

    const typing = document.getElementById("typing");

    if (!typing) return;

    if (!isDeleting && charIndex <= text[index].length) {

        currentText = text[index].substring(0, charIndex++);
        typing.innerHTML = currentText;

    }

    else if (isDeleting && charIndex >= 0) {

        currentText = text[index].substring(0, charIndex--);
        typing.innerHTML = currentText;

    }

    if (charIndex === text[index].length + 1) {

        isDeleting = true;

        setTimeout(typeEffect, 1000);

        return;
    }

    if (charIndex < 0) {

        isDeleting = false;

        index++;

        if (index >= text.length)
            index = 0;

    }

    setTimeout(typeEffect, isDeleting ? 60 : 120);

}

typeEffect();


// =============================
// Dark Mode
// =============================

const themeBtn = document.getElementById("theme-btn");

themeBtn.addEventListener("click", () => {

    document.body.classList.toggle("dark-mode");

    if (document.body.classList.contains("dark-mode")) {

        themeBtn.innerHTML =
            '<i class="fa-solid fa-sun"></i>';

    } else {

        themeBtn.innerHTML =
            '<i class="fa-solid fa-moon"></i>';

    }

});


// =============================
// Scroll To Top
// =============================

const topBtn = document.getElementById("topBtn");

window.onscroll = function () {

    if (document.body.scrollTop > 300 ||
        document.documentElement.scrollTop > 300) {

        topBtn.style.display = "block";

    }

    else {

        topBtn.style.display = "none";

    }

};

topBtn.onclick = function () {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

};


// =============================
// Contact Form Validation
// =============================

const form = document.getElementById("contactForm");

form.addEventListener("submit", function (e) {

    e.preventDefault();

    const inputs = form.querySelectorAll("input, textarea");

    let valid = true;

    inputs.forEach(input => {

        if (input.value.trim() === "") {

            valid = false;

        }

    });

    if (valid) {

        alert("Message Sent Successfully!");

        form.reset();

    }

    else {

        alert("Please fill all fields.");

    }

});


// =============================
// Smooth Fade Animation
// =============================

const sections = document.querySelectorAll("section");

const observer = new IntersectionObserver((entries) => {

    entries.forEach(entry => {

        if (entry.isIntersecting) {

            entry.target.style.opacity = "1";

            entry.target.style.transform = "translateY(0px)";

        }

    });

}, {

    threshold: 0.2

});

sections.forEach(section => {

    section.style.opacity = "0";

    section.style.transform = "translateY(60px)";

    section.style.transition = "1s";

    observer.observe(section);

});


// =============================
// Active Navbar Link
// =============================

const navLinks = document.querySelectorAll(".nav-links a");

window.addEventListener("scroll", () => {

    let current = "";

    sections.forEach(section => {

        const sectionTop = section.offsetTop - 150;

        if (pageYOffset >= sectionTop) {

            current = section.getAttribute("id");

        }

    });

    navLinks.forEach(link => {

        link.classList.remove("active");

        if (link.getAttribute("href") === "#" + current) {

            link.classList.add("active");

        }

    });

});


// =============================
// Welcome Message
// =============================

window.onload = () => {

    console.log("Portfolio Loaded Successfully");

};