/* ===== Maslaki Professional Platform - Core Logic ===== */

document.addEventListener('DOMContentLoaded', () => {
    console.log("Maslaki Core Initialized");
    
    // Smooth Scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Animations on scroll (Simple Reveal)
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card, .stat-card').forEach(el => {
        el.style.opacity = "0";
        el.style.transform = "translateY(20px)";
        el.style.transition = "all 0.6s cubic-bezier(0.4, 0, 0.2, 1)";
        observer.observe(el);
    });
});

// CSS for reveal animation
const style = document.createElement('style');
style.textContent = `
    .revealed {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
`;
document.head.appendChild(style);

// Global Toggle Save function
function toggleSave(id, btn) {
    fetch(`../save_school.php?id=${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                btn.classList.toggle('active');
                
                // Update global savedIds array if it exists
                if (typeof savedIds !== 'undefined') {
                    const idStr = id.toString();
                    const idNum = parseInt(id);
                    if (savedIds.includes(idStr)) {
                        savedIds = savedIds.filter(sid => sid !== idStr && parseInt(sid) !== idNum);
                    } else if (savedIds.includes(idNum)) {
                        savedIds = savedIds.filter(sid => sid !== idStr && parseInt(sid) !== idNum);
                    } else {
                        savedIds.push(idStr);
                    }
                }
                
                if (btn.classList.contains('active')) {
                    btn.innerHTML = '❤';
                    btn.title = 'Sauvegardé';
                } else {
                    btn.innerHTML = '❤';
                    btn.title = 'Sauvegarder';
                }
            }
        })
        .catch(err => console.error('Error saving:', err));
}
