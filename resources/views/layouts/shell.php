<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wijaya Barbershop - Modern Haircut & Grooming</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #020617;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        #wijaya-viewport-frame {
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            border: 0;
            display: block;
            margin: 0;
            padding: 0;
            background: #020617;
        }
    </style>
</head>
<body>
    <iframe id="wijaya-viewport-frame" src="<?= htmlspecialchars($frameSrc) ?>" allow="autoplay"></iframe>

    <!-- Persistent Music Engine in Top Window -->
    <script src="/js/app.js?v=20261120c"></script>
    <script>
        // Sync URL & Title from iframe to browser address bar
        const frame = document.getElementById('wijaya-viewport-frame');
        
        function syncFrameState() {
            try {
                if (frame && frame.contentWindow && frame.contentWindow.location) {
                    const loc = frame.contentWindow.location;
                    if (loc.origin === window.location.origin) {
                        const targetPath = loc.pathname + loc.search;
                        // Strip internal query param from browser bar for clean URL
                        const cleanPath = targetPath.replace(/(\?|&)in_app_frame=1/, '').replace(/\?$/, '');
                        const currentPath = window.location.pathname + window.location.search;
                        if (cleanPath && cleanPath !== currentPath) {
                            window.history.replaceState({ inShell: true }, '', cleanPath || '/');
                        }
                        if (frame.contentDocument && frame.contentDocument.title) {
                            document.title = frame.contentDocument.title;
                        }
                        // Sync position of floating player if entering admin
                        const player = document.getElementById('barber-music-player');
                        if (player) {
                            const isAdmin = loc.pathname.startsWith('/admin');
                            player.className = `fixed ${isAdmin ? 'bottom-6 left-72 max-md:left-4 z-40' : 'bottom-6 left-6 max-md:bottom-4 max-md:left-4 z-40'} no-print transition-all duration-300 font-sans`;
                        }
                    }
                }
            } catch (e) {}
        }

        if (frame) {
            frame.addEventListener('load', syncFrameState);
            setInterval(syncFrameState, 400);
        }

        // Handle browser Back / Forward buttons
        window.addEventListener('popstate', () => {
            try {
                if (frame && frame.contentWindow) {
                    frame.contentWindow.history.back();
                }
            } catch(e) {}
        });

        // Pastikan modal WhatsApp di iframe otomatis tertutup saat beralih aplikasi atau kembali
        window.addEventListener('blur', () => {
            try {
                if (frame && frame.contentWindow && typeof frame.contentWindow.closeWaModal === 'function') {
                    frame.contentWindow.closeWaModal();
                }
            } catch (e) {}
        });
        window.addEventListener('focus', () => {
            try {
                if (frame && frame.contentWindow && typeof frame.contentWindow.closeWaModal === 'function') {
                    frame.contentWindow.closeWaModal();
                }
            } catch (e) {}
        });
    </script>
</body>
</html>

