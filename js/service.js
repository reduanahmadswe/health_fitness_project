 // Mobile menu toggle
 const hamburger = document.querySelector('.hamburger');
 const navLinks = document.querySelector('.nav-links');
 
 hamburger.addEventListener('click', () => {
     navLinks.classList.toggle('active');
     hamburger.innerHTML = navLinks.classList.contains('active') 
         ? '<i class="fas fa-times"></i>' 
         : '<i class="fas fa-bars"></i>';
 });
 
 // Add animation to service cards as they come into view
 const animateOnScroll = () => {
     const serviceCards = document.querySelectorAll('.service-card');
     
     serviceCards.forEach(card => {
         const cardPosition = card.getBoundingClientRect().top;
         const screenPosition = window.innerHeight / 1.3;
         
         if(cardPosition < screenPosition) {
             card.style.opacity = '1';
             card.style.transform = 'translateY(0)';
         }
     });
 };
 
 // Set initial state for animation
 document.querySelectorAll('.service-card').forEach(card => {
     card.style.opacity = '0';
     card.style.transform = 'translateY(20px)';
     card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
 });
 
 window.addEventListener('scroll', animateOnScroll);
 window.addEventListener('load', animateOnScroll);

 // Update the filter functionality to handle both category and type
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (this.tagName === 'A') return;
        
        e.preventDefault();
        const category = this.dataset.category;
        
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Filter results
        document.querySelectorAll('.result-card').forEach(card => {
            if (category === 'all') {
                card.style.display = 'block';
            } else {
                const cardCategory = card.dataset.category;
                const cardType = card.dataset.type;
                if (cardCategory === category || (category === 'trainers' && cardType === 'trainer')) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            }
        });
    });
});