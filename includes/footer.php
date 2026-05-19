</main>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="logo">
                <img src="<?php echo $base; ?>assets/images/logo.png" alt="Maslaki Logo" class="brand-logo" style="height: 56px; vertical-align: middle; margin-right: 12px; object-fit: contain;">
                Maslaki
            </div>
            <p><?php echo __('footer_desc'); ?></p>
            
        </div>

        <div class="footer-group">
            <h4><?php echo __('navigation'); ?></h4>
            <ul>
                <li><a href="<?php echo $base; ?>index.php"><?php echo __('home'); ?></a></li>
                <li><a href="<?php echo $base; ?>views/institutions.php"><?php echo __('institutions'); ?></a></li>
                <li><a href="<?php echo $base; ?>views/ai_form.php"><?php echo __('ai_orientation'); ?></a></li>
            </ul>
        </div>

        <div class="footer-group">
            <h4><?php echo __('resources'); ?></h4>
            <ul>
                <li><a href="#"><?php echo __('registration_guide'); ?></a></li>
                <li><a href="#"><?php echo __('exam_dates'); ?></a></li>
                <li><a href="#"><?php echo __('help_support'); ?></a></li>
            </ul>
        </div>

        <div class="footer-group">
            <h4><?php echo __('contact'); ?></h4>
            <p>📧 contact@maslaki.ma</p>
            <p>📍 Tanger, Maroc</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Maslaki. <?php echo __('powered_by_innovation'); ?></p>
    </div>
</footer>


<style>
.main-footer { background: var(--primary-dark); color: #fff; padding: 80px 0 40px; margin-top: 80px; }
.footer-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 60px; }
.footer-brand .logo { font-size: 2rem; font-weight: 800; color: var(--accent); margin-bottom: 20px; }
.footer-brand p { color: rgba(255,255,255,0.6); line-height: 1.6; font-size: 0.95rem; }
.footer-group h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 25px; color: #fff; }
.footer-group ul { list-style: none; padding: 0; }
.footer-group ul li { margin-bottom: 12px; }
.footer-group ul li a { color: rgba(255,255,255,0.6); text-decoration: none; transition: var(--transition); }
.footer-group ul li a:hover { color: var(--accent); padding-left: 5px; }
.footer-group p { color: rgba(255,255,255,0.6); margin-bottom: 10px; font-size: 0.95rem; }
.footer-bottom { max-width: 1200px; margin: 60px auto 0; padding: 30px 24px 0; border-top: 1px solid rgba(255,255,255,0.1); text-align: center; color: rgba(255,255,255,0.4); font-size: 0.9rem; }

@media (max-width: 992px) {
    .footer-container { grid-template-columns: 1fr 1fr; gap: 40px; }
}
@media (max-width: 600px) {
    .footer-container { grid-template-columns: 1fr; }
}
</style>

<script src="<?php echo $base; ?>assets/js/script.js"></script>
</body>
</html>

