<?php
$filesDir = __DIR__ . '/files';
$baseUrl = 'files';

if (!is_dir($filesDir)) {
    mkdir($filesDir, 0755, true);
}

$allowedExtensions = [
    'zip', 'rar', '7z', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'txt', 'csv', 'json', 'jpg', 'jpeg', 'png', 'webp', 'mp4', 'mp3', 'exe', 'msi'
];

function formatBytes(int $bytes, int $precision = 2): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function getFileIcon(string $extension): string {
    $map = [
        'zip' => '📦', 'rar' => '📦', '7z' => '📦',
        'pdf' => '📕',
        'doc' => '📘', 'docx' => '📘',
        'xls' => '📗', 'xlsx' => '📗', 'csv' => '📗',
        'ppt' => '📙', 'pptx' => '📙',
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'webp' => '🖼️',
        'mp4' => '🎬', 'mp3' => '🎵',
        'exe' => '⚙️', 'msi' => '⚙️',
        'json' => '🧩', 'txt' => '📄'
    ];
    return $map[$extension] ?? '📁';
}

$files = [];
if (is_dir($filesDir)) {
    $items = scandir($filesDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $fullPath = $filesDir . DIRECTORY_SEPARATOR . $item;
        if (!is_file($fullPath)) {
            continue;
        }

        $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $files[] = [
            'name' => $item,
            'url' => $baseUrl . '/' . rawurlencode($item),
            'size' => formatBytes(filesize($fullPath)),
            'mtime' => filemtime($fullPath),
            'date' => date('d.m.Y H:i', filemtime($fullPath)),
            'ext' => strtoupper($extension ?: 'FILE'),
            'icon' => getFileIcon($extension),
        ];
    }
}

usort($files, static function ($a, $b) {
    return $b['mtime'] <=> $a['mtime'];
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neon File Hub</title>
    <style>
        :root {
            --bg-1: #060816;
            --bg-2: #0b1225;
            --bg-3: #0f1b38;
            --glass: rgba(255, 255, 255, 0.08);
            --glass-strong: rgba(255, 255, 255, 0.12);
            --border: rgba(255, 255, 255, 0.14);
            --text: #ecf4ff;
            --muted: #9fb2d8;
            --cyan: #4df6ff;
            --blue: #5a8cff;
            --violet: #8a5cff;
            --pink: #ff4fd8;
            --green: #73ffa9;
            --shadow-cyan: 0 0 25px rgba(77, 246, 255, 0.35);
            --shadow-pink: 0 0 30px rgba(255, 79, 216, 0.28);
            --shadow-blue: 0 0 35px rgba(90, 140, 255, 0.28);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 18px;
            --container: 1240px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Inter, Segoe UI, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 15% 20%, rgba(138, 92, 255, 0.22), transparent 26%),
                radial-gradient(circle at 85% 18%, rgba(77, 246, 255, 0.18), transparent 24%),
                radial-gradient(circle at 75% 78%, rgba(255, 79, 216, 0.14), transparent 24%),
                linear-gradient(160deg, var(--bg-1) 0%, var(--bg-2) 55%, var(--bg-3) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            inset: auto;
            width: 440px;
            height: 440px;
            border-radius: 999px;
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.45;
            animation: floatOrb 10s ease-in-out infinite;
        }

        body::before {
            top: -90px;
            left: -70px;
            background: rgba(77, 246, 255, 0.22);
        }

        body::after {
            right: -120px;
            bottom: -120px;
            background: rgba(255, 79, 216, 0.20);
            animation-delay: -4s;
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(0, 22px, 0) scale(1.05); }
        }

        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 36px 36px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,0.55), transparent 85%);
            z-index: 0;
            pointer-events: none;
        }

        .container {
            width: min(100% - 32px, var(--container));
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .glass {
            background: linear-gradient(180deg, rgba(255,255,255,0.11), rgba(255,255,255,0.05));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.28);
        }

        .topbar {
            position: sticky;
            top: 16px;
            z-index: 20;
            margin: 18px auto 0;
            border-radius: 999px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .brand-badge {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(77,246,255,0.18), rgba(138,92,255,0.26));
            box-shadow: var(--shadow-cyan), inset 0 0 25px rgba(255,255,255,0.08);
            font-size: 1.2rem;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav a,
        .nav button {
            appearance: none;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            color: var(--text);
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 999px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: 0.25s ease;
        }

        .nav a:hover,
        .nav button:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 0 1px rgba(255,255,255,0.06), var(--shadow-blue);
            background: rgba(255,255,255,0.1);
        }

        .hero {
            padding: 72px 0 44px;
        }

        .hero-card {
            border-radius: var(--radius-xl);
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 15%, rgba(77,246,255,0.18), transparent 25%),
                radial-gradient(circle at 85% 22%, rgba(255,79,216,0.14), transparent 22%),
                linear-gradient(135deg, rgba(255,255,255,0.02), transparent 40%);
            pointer-events: none;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 28px;
            align-items: center;
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--cyan);
            font-weight: 700;
            margin-bottom: 18px;
            box-shadow: var(--shadow-cyan);
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 5rem);
            line-height: 0.98;
            margin-bottom: 18px;
            letter-spacing: -0.04em;
        }

        .hero .accent {
            background: linear-gradient(90deg, var(--cyan), #ffffff, var(--pink));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 28px rgba(77,246,255,0.16);
        }

        .hero p {
            color: var(--muted);
            font-size: 1.08rem;
            max-width: 700px;
            line-height: 1.75;
            margin-bottom: 26px;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 22px;
            border-radius: 16px;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.12);
            transition: 0.25s ease;
            font-weight: 700;
        }

        .btn-primary {
            color: #041018;
            background: linear-gradient(135deg, var(--cyan), #bdfaff);
            box-shadow: var(--shadow-cyan);
        }

        .btn-secondary {
            color: var(--text);
            background: rgba(255,255,255,0.06);
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.01);
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 26px;
        }

        .stat {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
        }

        .stat strong {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 8px;
        }

        .stat span {
            color: var(--muted);
            font-size: 0.95rem;
        }

        .screen-wrap {
            position: relative;
        }

        .screen-glow {
            position: absolute;
            inset: 16px;
            border-radius: 30px;
            background: radial-gradient(circle at 50% 20%, rgba(77,246,255,0.18), transparent 38%),
                        radial-gradient(circle at 80% 80%, rgba(255,79,216,0.16), transparent 34%);
            filter: blur(20px);
            z-index: 0;
        }

        .screen {
            position: relative;
            z-index: 1;
            border-radius: 30px;
            padding: 20px;
            min-height: 430px;
            background: linear-gradient(180deg, rgba(7,12,26,0.94), rgba(10,16,36,0.84));
            border: 1px solid rgba(255,255,255,0.14);
            box-shadow: var(--shadow-blue), var(--shadow-pink), inset 0 1px 0 rgba(255,255,255,0.08);
            overflow: hidden;
        }

        .screen-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .dots {
            display: flex;
            gap: 8px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            box-shadow: 0 0 12px rgba(255,255,255,0.12);
        }

        .screen-title {
            color: var(--muted);
            font-size: 0.95rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .terminal {
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            border: 1px solid rgba(255,255,255,0.08);
            padding: 18px;
            min-height: 320px;
        }

        .terminal-line {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #d9e7ff;
            font-family: Consolas, Monaco, monospace;
            font-size: 0.96rem;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(255,255,255,0.06);
            animation: pulseLine 3s ease-in-out infinite;
        }

        .terminal-line:nth-child(2) { animation-delay: 0.4s; }
        .terminal-line:nth-child(3) { animation-delay: 0.8s; }
        .terminal-line:nth-child(4) { animation-delay: 1.2s; }
        .terminal-line:nth-child(5) { animation-delay: 1.6s; }

        @keyframes pulseLine {
            0%, 100% { opacity: 0.78; transform: translateX(0); }
            50% { opacity: 1; transform: translateX(3px); }
        }

        .terminal-mark {
            color: var(--green);
        }

        .section {
            padding: 24px 0 72px;
        }

        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 26px;
        }

        .section-head h2 {
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1;
        }

        .section-head p {
            color: var(--muted);
            max-width: 760px;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 24px;
        }

        .feature-card {
            border-radius: 24px;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: auto -40px -40px auto;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(77,246,255,0.24), transparent 70%);
            filter: blur(12px);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: rgba(255,255,255,0.08);
            margin-bottom: 18px;
            box-shadow: var(--shadow-cyan);
            font-size: 1.35rem;
        }

        .feature-card h3 {
            margin-bottom: 12px;
            font-size: 1.2rem;
        }

        .feature-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .files-panel {
            border-radius: 28px;
            padding: 26px;
        }

        .files-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .search-box {
            flex: 1 1 280px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.06);
            color: var(--text);
            border-radius: 18px;
            padding: 15px 18px 15px 48px;
            outline: none;
            font-size: 1rem;
        }

        .search-box span {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.72;
        }

        .meta-pill {
            padding: 12px 16px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            color: var(--muted);
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .file-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px;
            border-radius: 22px;
            text-decoration: none;
            color: inherit;
            background: linear-gradient(180deg, rgba(255,255,255,0.07), rgba(255,255,255,0.04));
            border: 1px solid rgba(255,255,255,0.10);
            transition: 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .file-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            transform: translateX(-120%);
            transition: 0.6s ease;
        }

        .file-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-blue), 0 20px 40px rgba(0,0,0,0.2);
            border-color: rgba(77,246,255,0.30);
        }

        .file-card:hover::after {
            transform: translateX(120%);
        }

        .file-icon {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 1.6rem;
            background: rgba(255,255,255,0.08);
            box-shadow: inset 0 0 30px rgba(255,255,255,0.04), var(--shadow-cyan);
            flex-shrink: 0;
        }

        .file-info {
            min-width: 0;
            flex: 1;
        }

        .file-name {
            font-size: 1.02rem;
            font-weight: 700;
            margin-bottom: 8px;
            word-break: break-word;
        }

        .file-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .file-tag {
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .download-chip {
            flex-shrink: 0;
            padding: 10px 14px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(77,246,255,0.18), rgba(90,140,255,0.18));
            border: 1px solid rgba(77,246,255,0.24);
            box-shadow: var(--shadow-cyan);
            font-weight: 700;
        }

        .empty-state {
            border-radius: 24px;
            padding: 34px;
            text-align: center;
            color: var(--muted);
            background: rgba(255,255,255,0.04);
            border: 1px dashed rgba(255,255,255,0.14);
        }

        .placeholder-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .placeholder-card {
            border-radius: 24px;
            padding: 28px;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .placeholder-card h3 {
            font-size: 1.4rem;
            margin-bottom: 10px;
        }

        .placeholder-card p {
            color: var(--muted);
            line-height: 1.7;
        }

        .status-badge {
            display: inline-flex;
            width: fit-content;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            color: var(--cyan);
            font-weight: 700;
        }

        footer {
            padding: 20px 0 46px;
        }

        .footer-card {
            border-radius: 24px;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            color: var(--muted);
        }

        .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 1100px) {
            .hero-grid,
            .features-grid,
            .files-grid,
            .placeholder-grid {
                grid-template-columns: 1fr;
            }

            .hero-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .topbar {
                border-radius: 28px;
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-card,
            .files-panel,
            .feature-card,
            .placeholder-card,
            .footer-card {
                padding: 20px;
            }

            .hero {
                padding-top: 42px;
            }

            .hero h1 {
                font-size: 2.4rem;
            }

            .screen {
                min-height: 340px;
            }
        }
    </style>
</head>
<body>
    <div class="grid-overlay"></div>

    <div class="container">
        <header class="topbar glass reveal">
            <div class="brand">
                <div class="brand-badge">✦</div>
                <div>NEON FILE HUB</div>
            </div>
            <nav class="nav">
                <a href="#home">Главная</a>
                <a href="#files">Файлы</a>
                <a href="#future-1">В разработке</a>
                <a href="#future-2">В разработке</a>
            </nav>
        </header>

        <section class="hero" id="home">
            <div class="hero-card glass reveal">
                <div class="hero-grid">
                    <div>
                        <div class="hero-kicker">⚡ Автоматическое обнаружение файлов на сервере</div>
                        <h1>Красивый <span class="accent">центр загрузок</span><br>с неоном, стеклом и атмосферой</h1>
                        <p>
                            Загружай файлы в папку <strong>/files</strong> на сервере - сайт сам покажет их в списке.
                            Здесь уже есть насыщенный hero-блок, стеклянные панели, неоновые свечения, плавные анимации,
                            секция особенностей и псевдо-экран в футуристичном стиле.
                        </p>
                        <div class="hero-actions">
                            <a class="btn btn-primary" href="#files">Открыть файлы</a>
                            <a class="btn btn-secondary" href="#features">Посмотреть возможности</a>
                        </div>
                        <div class="hero-stats">
                            <div class="stat">
                                <strong><?php echo count($files); ?></strong>
                                <span>файлов обнаружено сейчас</span>
                            </div>
                            <div class="stat">
                                <strong>24/7</strong>
                                <span>доступ к загрузкам</span>
                            </div>
                            <div class="stat">
                                <strong>Auto</strong>
                                <span>обновление списка без ручного редактирования</span>
                            </div>
                        </div>
                    </div>

                    <div class="screen-wrap reveal">
                        <div class="screen-glow"></div>
                        <div class="screen">
                            <div class="screen-top">
                                <div class="dots">
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                    <span class="dot"></span>
                                </div>
                                <div class="screen-title">live server scan</div>
                            </div>
                            <div class="terminal">
                                <div class="terminal-line"><span class="terminal-mark">&gt;</span> scan /files directory</div>
                                <div class="terminal-line"><span class="terminal-mark">&gt;</span> detect new uploads automatically</div>
                                <div class="terminal-line"><span class="terminal-mark">&gt;</span> render neon file cards</div>
                                <div class="terminal-line"><span class="terminal-mark">&gt;</span> sort by newest first</div>
                                <div class="terminal-line"><span class="terminal-mark">&gt;</span> ready for download requests</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="features">
            <div class="section-head reveal">
                <div>
                    <h2>Что уже умеет сайт</h2>
                </div>
                <p>
                    Это не просто список ссылок. Шаблон уже подготовлен как яркая витрина для скачивания файлов:
                    с насыщенным визуалом, акцентами, стеклянным интерфейсом и понятной логикой для дальнейшей доработки.
                </p>
            </div>

            <div class="features-grid">
                <article class="feature-card glass reveal">
                    <div class="feature-icon">🧠</div>
                    <h3>Автопоиск файлов</h3>
                    <p>Все файлы, которые ты загружаешь в папку <strong>/files</strong>, автоматически появляются в разделе загрузок. Никакие ссылки вручную править не нужно.</p>
                </article>
                <article class="feature-card glass reveal">
                    <div class="feature-icon">💎</div>
                    <h3>Стеклянный UI</h3>
                    <p>Панели с blur-эффектом, мягкие границы, объёмные карточки и насыщенные полупрозрачные слои создают дорогой визуальный стиль.</p>
                </article>
                <article class="feature-card glass reveal">
                    <div class="feature-icon">🌌</div>
                    <h3>Неон и анимации</h3>
                    <p>Фоновое свечение, переливы, плавное появление блоков и атмосферный псевдо-экран делают главную страницу выразительной и живой.</p>
                </article>
            </div>
        </section>

        <section class="section" id="files">
            <div class="section-head reveal">
                <div>
                    <h2>Файлы для скачивания</h2>
                </div>
                <p>
                    Ниже отображаются файлы, найденные в папке сервера. Можно искать по названию, а список уже отсортирован по дате - сначала самые новые.
                </p>
            </div>

            <div class="files-panel glass reveal">
                <div class="files-toolbar">
                    <div class="search-box">
                        <span>🔎</span>
                        <input type="text" id="fileSearch" placeholder="Поиск по названию файла...">
                    </div>
                    <div class="meta-pill">Найдено файлов: <strong><?php echo count($files); ?></strong></div>
                </div>

                <?php if (count($files) > 0): ?>
                    <div class="files-grid" id="filesGrid">
                        <?php foreach ($files as $file): ?>
                            <a class="file-card" href="<?php echo htmlspecialchars($file['url'], ENT_QUOTES, 'UTF-8'); ?>" download data-name="<?php echo htmlspecialchars(mb_strtolower($file['name']), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="file-icon"><?php echo $file['icon']; ?></div>
                                <div class="file-info">
                                    <div class="file-name"><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="file-meta">
                                        <span class="file-tag"><?php echo htmlspecialchars($file['ext'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span><?php echo htmlspecialchars($file['size'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span><?php echo htmlspecialchars($file['date'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                                <div class="download-chip">Скачать</div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h3 style="margin-bottom: 10px; color: var(--text);">Пока файлов нет</h3>
                        <p>Загрузи любой файл в папку <strong>/files</strong> рядом с этим <strong>index.php</strong>, и он автоматически появится здесь.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section" id="future-1">
            <div class="section-head reveal">
                <div>
                    <h2>В разработке</h2>
                </div>
                <p>
                    Эти разделы оставлены специально под будущее расширение сайта - например, категории, новости, инструкции или личный кабинет.
                </p>
            </div>

            <div class="placeholder-grid">
                <article class="placeholder-card glass reveal">
                    <div>
                        <div class="status-badge">Скоро</div>
                        <h3 style="margin-top: 16px;">Умные категории</h3>
                        <p>Можно будет разбивать файлы по разделам: программы, документы, архивы, изображения, драйверы и другие типы материалов.</p>
                    </div>
                </article>

                <article class="placeholder-card glass reveal" id="future-2">
                    <div>
                        <div class="status-badge">Скоро</div>
                        <h3 style="margin-top: 16px;">Новости и обновления</h3>
                        <p>Сюда можно добавить блок с обновлениями, changelog, последними загрузками или важными объявлениями для посетителей сайта.</p>
                    </div>
                </article>
            </div>
        </section>

        <footer>
            <div class="footer-card glass reveal">
                <div>© <?php echo date('Y'); ?> Neon File Hub</div>
                <div>Просто загружай файлы в папку <strong>/files</strong> - список обновится автоматически.</div>
            </div>
        </footer>
    </div>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

        const searchInput = document.getElementById('fileSearch');
        const fileCards = document.querySelectorAll('.file-card');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const value = this.value.trim().toLowerCase();

                fileCards.forEach((card) => {
                    const name = card.dataset.name || '';
                    const match = name.includes(value);
                    card.style.display = match ? 'flex' : 'none';
                });
            });
        }
    </script>
</body>
</html>
