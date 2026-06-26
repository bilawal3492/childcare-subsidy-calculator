<?php
if (!defined('ABSPATH')) { exit; }
        $changelog_file = CCS_CALCULATOR_PLUGIN_DIR . 'CHANGELOG.md';
        $changelog_content = '';
        
        if (file_exists($changelog_file)) {
            $changelog_content = file_get_contents($changelog_file);
        }
        ?>
        <div class="wrap">
            <h1>Changelog - Version History</h1>
            <div style="background:#fff; padding:20px; border:1px solid #ccc; border-radius:4px;">
                <?php if ($changelog_content): ?>
                    <div style="font-family: monospace; white-space: pre-wrap; line-height: 1.6;">
                        <?php echo esc_html($changelog_content); ?>
                    </div>
                <?php else: ?>
                    <p>No changelog available.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
