 // Mobile menu toggle
 const hamburger = document.querySelector('.hamburger');
 const navLinks = document.querySelector('.nav-links');
 
 hamburger.addEventListener('click', () => {
     navLinks.classList.toggle('active');
     hamburger.innerHTML = navLinks.classList.contains('active') 
         ? '<i class="fas fa-times"></i>' 
         : '<i class="fas fa-bars"></i>';
 });
 
 // Form validation
 document.getElementById('feedbackForm').addEventListener('submit', function(e) {
     const rating = document.querySelector('input[name="rating"]:checked');
     const message = document.getElementById('message').value.trim();

     if (!rating) {
         e.preventDefault();
         alert('Please select a rating.');
         return;
     }

     if (!message) {
         e.preventDefault();
         alert('Please enter your feedback message.');
         return;
     }
 });
 
 // Add animation to feedback cards as they come into view
 const animateOnScroll = () => {
     const elements = document.querySelectorAll('.feedback-card');
     
     elements.forEach(element => {
         const elementPosition = element.getBoundingClientRect().top;
         const screenPosition = window.innerHeight / 1.3;
         
         if(elementPosition < screenPosition) {
             element.style.opacity = '1';
             element.style.transform = 'translateY(0)';
         }
     });
 };
 
 // Set initial state for animation
 document.querySelectorAll('.feedback-card').forEach(element => {
     element.style.opacity = '0';
     element.style.transform = 'translateY(20px)';
     element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
 });
 
 window.addEventListener('scroll', animateOnScroll);
 window.addEventListener('load', animateOnScroll);