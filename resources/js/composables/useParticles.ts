import { ref } from 'vue';

export function useParticles() {
    const particlesContainer = ref<HTMLElement | null>(null);
    let lastParticleTime = 0;

    function handleMouseMove(e: MouseEvent) {
        if (!particlesContainer.value) return;
        
        const el = document.createElement('div');
        // PlayStation icons: Cross, Circle, Square, Triangle
        const shapes = ['✕', '◯', '□', '△'];
        const colors = ['#3b82f6', '#ec4899', '#2dd4bf', '#a855f7']; 
        
        const shape = shapes[Math.floor(Math.random() * shapes.length)];
        const color = colors[Math.floor(Math.random() * colors.length)];
        
        el.innerHTML = shape;
        // Set absolute positioning, completely ignore pointer events, style text and shadows
        el.className = 'absolute font-black select-none pointer-events-none drop-shadow-md z-50 text-xl';
        el.style.color = color;
        el.style.textShadow = `0 0 10px ${color}, 0 0 20px ${color}`;
        
        const rect = particlesContainer.value.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        el.style.left = `${x}px`;
        el.style.top = `${y}px`;
        
        // Initial state
        const initialRot = (Math.random() - 0.5) * 90;
        el.style.transform = `translate(-50%, -50%) scale(0.5) rotate(${initialRot}deg)`;
        el.style.transition = 'all 1.2s cubic-bezier(0.1, 0.8, 0.3, 1)';
        el.style.opacity = '1';
        
        particlesContainer.value.appendChild(el);
        
        // Ensure the browser registers the initial state before applying the destination state
        setTimeout(() => {
            // Natural chaotic spread: they float up and out randomly
            const destX = (Math.random() - 0.5) * 150;
            const destY = (Math.random() - 0.5) * 150 - 50; 
            const destRot = initialRot + (Math.random() - 0.5) * 360;
            
            el.style.transform = `translate(calc(-50% + ${destX}px), calc(-50% + ${destY}px)) scale(1.5) rotate(${destRot}deg)`;
            el.style.opacity = '0';
        }, 15);
        
        // Cleanup after transition finishes
        setTimeout(() => {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 1200);
    }

    function throttledMouseMove(e: MouseEvent) {
        const now = Date.now();
        // Throttle to roughly 60 FPS
        if (now - lastParticleTime > 16) {
            handleMouseMove(e);
            lastParticleTime = now;
        }
    }

    return {
        particlesContainer,
        throttledMouseMove
    };
}
