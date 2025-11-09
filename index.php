<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Key Generator</title>
  <link rel="icon" href="logo.png" type="image/png" />
  <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(135deg, #1e0033, #4a007a);
      color: #ffffff;
      font-family: Arial, sans-serif;
      margin: 0;
      position: relative;
      overflow: hidden;
    }
    #particles-js {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }
    .container {
      background-color: rgba(0, 0, 0, 0.85);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(180, 0, 255, 0.8);
      text-align: center;
      max-width: 500px;
      width: 100%;
      z-index: 2;
      position: relative;
    }
    h1 {
      margin-bottom: 20px;
      font-size: 2.5em;
      font-weight: bold;
      color: #c300ff;
      text-shadow: 0 0 20px #c300ff;
      cursor: pointer;
    }
    p, .key {
      color: #c300ff;
      text-shadow: 0 0 15px #c300ff;
    }
    .key {
      font-size: 1.8em;
      font-weight: bold;
      margin-top: 20px;
      padding: 15px;
      background-color: #1f1f1f;
      border-radius: 10px;
      word-wrap: break-word;
      white-space: pre-line;
    }
    .copy-button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #c300ff;
      color: #121212;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.3s;
      text-shadow: 0 0 10px #c300ff;
    }
    .copy-button:hover {
      animation: pulse 1s infinite;
      transform: scale(1.05);
    }
    .copy-button:active {
      background-color: #7a00cc;
      animation: shake 0.5s ease-in-out;
    }
    @keyframes shake {
      0% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
      100% { transform: translateX(0); }
    }
    @keyframes pulse {
      0% { background-color: #c300ff; }
      50% { background-color: #8a00b3; }
      100% { background-color: #c300ff; }
    }
    .hidden-message {
      display: none;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: #333;
      color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(180, 0, 255, 0.9);
      z-index: 5;
      text-align: center;
    }
  </style>
</head>
<body>
  <div id="particles-js"></div>

  <div class="container">
    <h1 onclick="showEasterEgg()">Key Generator</h1>
    <p>Your generated key is:</p>
    <div class="key" id="keyDisplay">Loading...</div>
    <button class="copy-button" onclick="copyToClipboard()">Copy Key</button>
  </div>

  <div class="hidden-message" id="hiddenMessage">
    🎉 You've unlocked a secret! 🎉 https://t.me/+S145y8OSyJdmZjdk
  </div>

  <script>
    const HALF_HOUR = 30 * 60 * 1000; // 30 dakika
    const keyDisplay = document.getElementById('keyDisplay');

    function loadNewCode() {
      fetch('codes.txt')
        .then(res => res.text())
        .then(text => {
          const codes = text.split('\n').map(c => c.trim()).filter(Boolean);
          const random = codes[Math.floor(Math.random() * codes.length)];
          keyDisplay.innerText = random;

          // localStorage’a kaydet
          localStorage.setItem('lastKey', random);
          localStorage.setItem('lastTime', Date.now().toString());
        })
        .catch(() => {
          keyDisplay.innerText = "Error loading codes.";
        });
    }

    function checkCooldown() {
      const lastKey = localStorage.getItem('lastKey');
      const lastTime = localStorage.getItem('lastTime');

      if (lastKey && lastTime) {
        const elapsed = Date.now() - parseInt(lastTime);
        if (elapsed < HALF_HOUR) {
          const remaining = Math.ceil((HALF_HOUR - elapsed) / 60000);
          keyDisplay.innerText = `${lastKey}\n⏰ Please wait ${remaining} minutes before getting a new key.`;
          return;
        }
      }
      // Süre dolmuşsa yeni kod ver
      loadNewCode();
    }

    function copyToClipboard() {
      const keyText = document.getElementById('keyDisplay').innerText.trim();
      navigator.clipboard.writeText(keyText).then(() => {
        alert('Key copied to clipboard.');
      });
    }

    function showEasterEgg() {
      const message = document.getElementById('hiddenMessage');
      message.style.display = 'block';
      setTimeout(() => {
        message.style.display = 'none';
      }, 3000);
      navigator.clipboard.writeText('https://t.me/+S145y8OSyJdmZjdk');
    }

    // Arka plan animasyonu
    particlesJS("particles-js", {
      particles: {
        number: { value: 80, density: { enable: true, value_area: 800 } },
        color: { value: "#ffffff" },
        shape: { type: "circle", stroke: { width: 0, color: "#000000" } },
        opacity: { value: 0.5, random: true, anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false } },
        size: { value: 3, random: true },
        line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
        move: { enable: true, speed: 6, direction: "none", out_mode: "out" }
      },
      interactivity: {
        events: { onhover: { enable: true, mode: "grab" }, onclick: { enable: true, mode: "push" }, resize: true },
        modes: { grab: { distance: 140, line_linked: { opacity: 1 } }, push: { particles_nb: 4 } }
      },
      retina_detect: true
    });

    // Sayfa yüklenince kontrol et
    checkCooldown();
  </script>
</body>
</html>
