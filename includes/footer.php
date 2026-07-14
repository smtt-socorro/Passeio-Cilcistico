        <?php if (!$admin_area): ?>
        <!-- Footer -->
        <footer style="margin-top: 80px; padding: 40px 0; text-align: center; color: rgba(29, 28, 28, 0.8); border-top: 1px solid rgba(34, 37, 187, 0.1);">
            <div style="margin-bottom: 30px;">
                <div class="footer-links">
                    <a href="index.php" class="footer-link">Início</a>
                    <a href="inscricao.php" class="footer-link">Inscrever-se</a>
                    <a href="verificar_inscricao.php" class="footer-link">Consultar</a>
                    <a href="faq.php" class="footer-link">FAQ</a>
                    <a href="termos.php" class="footer-link">Termos</a>
                </div>
                
                <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope"></i>
                        <span>smtt@socorro.se.gov.br</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-phone"></i>
                        <span>(79) 99898-1288</span>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(34, 37, 187, 0.1); padding-top: 20px;">
                <p style="margin: 0; font-size: 0.9rem;">
                    &copy; 2026 SMTT - Nossa Senhora do Socorro, SE. Todos os direitos reservados.
                </p>
                <p style="margin: 8px 0 0 0; font-size: 0.8rem; opacity: 0.7;">
                    Socorro no Pedal 2026 - 16 de Agosto de 2026
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
