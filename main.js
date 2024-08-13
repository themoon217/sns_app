function RainDrop() {
    const rainDrop = document.createElement('div');
    rainDrop.className = 'rainDrop';
    document.getElementById('backScreen').appendChild(rainDrop);

    let x = 0;
    let posX = Math.random() * 100;
    let posY = Math.random() * 100;
    let size = Math.random();

    const animateRainDrop = () => {
        x++;
        rainDrop.style.background = `radial-gradient(
            rgba(150,150,150,${0.2 * (1 - x / 100)})${x - 80}%,
            rgba(200,200,200,${0.5 * (1 - x / 100)})${x - 75}%, 
            rgba(100,100,100,${0.2 * (1 - x / 100)})${x - 73}%,
            rgba(255,255,255,0)${x-70}%,

            rgba(150,150,150,${0.2 * (1 - x / 100)})${x - 40}%,
            rgba(200,200,200,${0.4 * (1 - x / 100)})${x - 35}%, 
            rgba(100,100,100,${0.1 * (1 - x / 100)})${x - 33}%,
            rgba(255,255,255,0)${x-30}%,

            rgba(150,150,150,${0.2 * (1 - x / 100)})${x - 10}%,
            rgba(200,200,200,${0.5 * (1 - x / 100)})${x - 5}%, 
            rgba(100,100,100,${0.2 * (1 - x / 100)})${x - 3}%,
            rgba(100,100,100,0)${x}%
            )`;
        rainDrop.style.position = 'absolute';
        rainDrop.style.top = `${posX}%`;
        rainDrop.style.left = `${posY}%`;
        rainDrop.style.height = `${size * 150 + 50}px`;
        rainDrop.style.width = `${size * 150 + 50}px`;
        if (x === 100) {
            clearInterval(animationInterval);
            rainDrop.remove();
        }
    }

    const animationInterval = setInterval(animateRainDrop, 20);
}

setInterval(RainDrop, Math.floor((Math.random() * 500)));
