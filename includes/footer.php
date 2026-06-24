        <?php if (!$admin_area): ?>
        <!-- Footer -->
        <footer style="margin-top: 80px; padding: 40px 0; text-align: center; color: rgba(255,255,255,0.8); border-top: 1px solid rgba(255,255,255,0.1);">
            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 20px;">
                    <a href="index.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">Início</a>
                    <a href="inscricao.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">Inscrever-se</a>
                    <a href="verificar_inscricao.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">Consultar</a>
                    <a href="faq.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">FAQ</a>
                    <a href="termos.php" style="color: rgba(255,255,255,0.8); text-decoration: none; transition: color 0.3s;">Termos</a>
                </div>
                
                <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope"></i>
                        <span>smtt@socorro.se.gov.br</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i>
                        <span>(79) 3259-3920</span>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <p style="margin: 0; font-size: 0.9rem;">
                    &copy; 2025 SMTT - Nossa Senhora do Socorro, SE. Todos os direitos reservados.
                </p>
                <p style="margin: 8px 0 0 0; font-size: 0.8rem; opacity: 0.7;">
                    Evento de Bicicleta - 29 de Agosto de 2025
                </p>
            </div>
        </footer>
        <?php endif; ?>
    </div>

    <!-- Scripts JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script src="<?php echo $admin_area ? '../' : ''; ?>assets/js/main.js"></script>
    
    <?php if (isset($inline_js)): ?>
        <script>
            <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>
</body>
</html>
