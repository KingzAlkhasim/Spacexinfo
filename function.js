 // Create some decorative sparkles in the hero area
     (function placeSparkles(){
            const container = document.body;
            for(let i=0;i<6;i++){
                const s = document.createElement('div');
                s.className = 'sparkle';
                s.style.left = (6 + Math.random()*85) + '%';
                s.style.top = (6 + Math.random()*60) + '%';
                s.style.animationDuration = (3 + Math.random()*3) + 's';
                s.style.opacity = 0.6 + Math.random()*0.4;
                container.appendChild(s);
            }
        })();

    f