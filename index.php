<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PerformanceHub — Plataforma de Gestao de Desempenho</title>
    <link rel="stylesheet" href="styles/index.css" />
</head>
<body>
    <header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-icon">PH</span>
                <span class="logo-text">PerformanceHub</span>
            </div>
            <nav class="nav-links">
                <a href="#sobre">Sobre Nos</a>
                <a href="#faq">FAQ</a>
                <a href="pages/login.php" class="btn-outline">Entrar</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-content">
                <div class="hero-text">
                    <h1>Eleve o desempenho da sua equipe ao proximo nivel</h1>
                    <p>Acompanhe metas, realize avaliacoes 360° e ofereca feedbacks continuos em uma plataforma centralizada, intuitiva e baseada em dados.</p>
                    <div class="hero-actions">
                        <a href="pages/login.php" class="btn-primary">Acessar o Sistema</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="assets/equipe-colabore.avif" alt="Equipe colaborando e analisando dados de desempenho em um escritorio" />
                </div>
            </div>
        </section>

        <section id="sobre" class="about">
            <div class="container">
                <h2 class="section-title">Sobre o PerformanceHub</h2>
                <div class="about-grid">
                    <div class="about-card">
                        <h3>O que fazemos?</h3>
                        <p>Substituimos planilhas complexas por um sistema inteligente onde gestores e colaboradores acompanham metricas, OKRs e KPIs em tempo real.</p>
                    </div>
                    <div class="about-card">
                        <h3>Nossa Missao</h3>
                        <p>Nossa missao e transformar a cultura de feedback nas empresas, tornando o processo de avaliacao mais justo, transparente e focado no desenvolvimento continuo.</p>
                    </div>
                    <div class="about-card">
                        <h3>Para quem e?</h3>
                        <p>Ideal para equipes de RH, Business Partners, lideres e gestores que desejam engajar seus times e reter talentos com base em reconhecimento real.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="faq">
            <div class="container">
                <h2 class="section-title">Perguntas Frequentes (FAQ)</h2>
                <div class="faq-list">
                    <div class="faq-item">
                        <h4>Como funcionam os ciclos de avaliacao?</h4>
                        <p>O Administrador ou RH pode configurar ciclos periodicos (mensais, semestrais ou anuais), onde metas sao alinhadas e avaliadas atraves de check-ins e feedbacks continuos.</p>
                    </div>
                    <div class="faq-item">
                        <h4>O sistema suporta avaliacao 360°?</h4>
                        <p>Sim. Alem da autoavaliacao e da avaliacao do gestor direto, o sistema permite a coleta de feedbacks de pares (avaliadores cruzados) para uma visao completa do colaborador.</p>
                    </div>
                    <div class="faq-item">
                        <h4>Como os PDIs sao estruturados?</h4>
                        <p>Apos a calibracao das notas, o gestor e o colaborador podem utilizar os dados da avaliacao para gerar um Plano de Desenvolvimento Individual com prazos e acoes especificas.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-logo">
                <span class="logo-icon-small">PH</span>
                <span>PerformanceHub</span>
            </div>
            <div class="footer-links">
                <a href="#sobre">Sobre Nos</a>
                <a href="#faq">FAQ</a>
                <a href="pages/login.php">Login</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 PerformanceHub. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
