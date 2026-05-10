<?php
require_once "../includes/lang_helper.php";
$pageTitle = __("ai_orientation");
require "../includes/header.php";
?>

<div class="form-container">
    <h2><?php echo __('ai_personalized_orientation'); ?></h2>
    <p style="text-align:center; color:var(--text-muted); margin-bottom:20px; font-size:0.85rem;">
        <?php echo __('ai_subtitle'); ?>
    </p>

    <form method="POST" action="../ai_process.php">
        <div class="form-group">
            <label><?php echo __('bac_branch'); ?></label>
            <select name="bac_branch" required>
                <option value=""><?php echo __('choose'); ?></option>
                <option value="SVT"><?php echo __('bac_svt'); ?></option>
                <option value="PC"><?php echo __('bac_pc'); ?></option>
                <option value="Math"><?php echo __('bac_math'); ?></option>
                <option value="Eco"><?php echo __('bac_eco'); ?></option>
                <option value="Tech"><?php echo __('bac_tech'); ?></option>
                <option value="Lettres"><?php echo __('bac_letters'); ?></option>
            </select>
        </div>

        <div class="form-group">
            <label><?php echo __('bac_average'); ?></label>
            <input type="number" step="0.01" name="average" placeholder="14.50" min="0" max="20" required>
        </div>

        <div class="form-group">
            <label><?php echo __('preferred_city'); ?></label>
            <input type="text" name="city" placeholder="Casablanca">
        </div>

        <button type="submit" class="btn btn-orange"><?php echo __('get_recommendations'); ?></button>
    </form>
</div>

<?php require "../includes/footer.php"; ?>