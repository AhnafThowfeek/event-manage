 let menu = document.querySelector('#menu-bars');
 let navbar = document.querySelector('.navbar');

 menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
 } 

 profile = document.querySelector('.header .profile');

document.querySelector('#user-btn').onclick =() =>{
    profile.classList.toggle('active');
    navbar.classList.remove('active');
  
}

window.onscroll = () =>{
    navbar.classList.remove('active');
    profile.classList.remove('active');
}

let themeToggler = document.querySelector('.theme-toggler');
let toggleBtn = document.querySelector('.toggle-btn');


toggleBtn.onclick = () => {
    themeToggler.classList.toggle('active');
};


document.querySelectorAll('.theme-toggler .theme-btn').forEach(btn => {
    btn.onclick = () => {
        let color = btn.style.backgroundColor; 
        document.querySelector(':root').style.setProperty('--main-color', color); 
    };
});


window.onscroll = () => {
    themeToggler.classList.remove('active');
};

// Home Slider
var swiper = new Swiper(".home-slider", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "auto",
    coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2,
        slideShadows: true,
    },
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    breakpoints: {
        768: {
            slidesPerView: 2,
            spaceBetween: 10,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 20,
        },
    },
});

// Review Slider
var slider = new Swiper(".review-slider", {
    slidesPerView: 1,
    grabCursor: true,
    loop: true,
    spaceBetween: 20,
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        700: {
            slidesPerView: 2,
        },
        1050: {
            slidesPerView: 3,
        },
    },
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
});


// Select required elements
const wrapper = document.querySelector('.wrapper'); // Wrapper element
const loginLink = document.querySelector('.login-link'); // Sign In link
const registerLink = document.querySelector('.register-link'); // Sign Up link

// Debug: Check if elements are selected correctly
console.log('Wrapper:', wrapper);
console.log('Login Link:', loginLink);
console.log('Register Link:', registerLink);

// Add event listener to show "Sign Up" form
registerLink.addEventListener('click', (e) => {
    e.preventDefault(); // Prevent default link behavior
    console.log('Sign Up clicked'); // Debug log
    wrapper.classList.add('active'); // Add the 'active' class to show Sign Up form
});

// Add event listener to show "Sign In" form
loginLink.addEventListener('click', (e) => {
    e.preventDefault(); // Prevent default link behavior
    console.log('Sign In clicked'); // Debug log
    wrapper.classList.remove('active'); // Remove the 'active' class to show Sign In form
});

 
