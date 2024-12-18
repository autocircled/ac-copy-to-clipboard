<?php
/**
 * Plugin Name: AC Copy to Clipboard
 * Description: A plugin to copy text wrapped in [copy][/copy] shortcode to the clipboard.
 * Version: 1.1
 * Author: autocircled
 * Text Domain: ac-copy-to-clipboard
 */


function copy_to_clipboard_shortcode($atts, $content = null) {
    $content = esc_html(do_shortcode($content));
    $icon_url = plugin_dir_url(__FILE__) . 'icon-clipboard.png';

    return <<<EOT
        <div class="ac-copy-to-clipboard">
            <span class="copy-text">{$content}</span>
            <img src="{$icon_url}" alt="Copy">
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                (function ac_copy_to_clipboard() {
                    const copyButtons = document.querySelectorAll('.ac-copy-to-clipboard');

                    copyButtons.forEach((copyButton, index) => {
                        // Assign a unique ID dynamically in JS
                        const uniqueId = 'copy_text_' + index;
                        const textElement = copyButton.querySelector('.copy-text');
                        
                        if (textElement) {
                            textElement.id = uniqueId;
                            const u_id = "#"+uniqueId;
                            copyButton.setAttribute('data-clipboard-target', u_id);
                        }

                        copyButton.addEventListener('click', function () {
                            const targetId = copyButton.getAttribute('data-clipboard-target');
                            if (!targetId) {
                                console.error('No data-clipboard-target found.');
                                return;
                            }
                            
                            const textElement = document.querySelector(targetId);
                            if (textElement) {
                                const textToCopy = textElement.textContent || textElement.innerText;

                                // Clipboard API with fallback
                                if (!navigator.clipboard) {
                                    const tempTextarea = document.createElement('textarea');
                                    tempTextarea.value = textToCopy;
                                    document.body.appendChild(tempTextarea);
                                    tempTextarea.select();
                                    try {
                                        document.execCommand('copy');
                                        showTooltip(copyButton, 'Text copied to clipboard!');
                                    } catch (err) {
                                        console.error('Fallback copy failed:', err);
                                    } finally {
                                        document.body.removeChild(tempTextarea);
                                    }
                                    return;
                                }

                                navigator.clipboard.writeText(textToCopy)
                                    .then(() => {
                                        showTooltip(copyButton, 'Text copied to clipboard!');
                                    })
                                    .catch(err => {
                                        console.error('Failed to copy text:', err);
                                    });
                            } else {
                                console.error('Target element not found:', targetId);
                            }
                        });
                    });

                    function showTooltip(element, message) {
                        const tooltip = document.createElement('div');
                        tooltip.classList.add('ac-copy-tooltip');
                        tooltip.innerText = message;
                        
                        element.appendChild(tooltip);

                        const tooltipWidth = tooltip.offsetWidth;
                        tooltip.style.position = 'absolute';
                        tooltip.style.top = '-30px'; 
                        tooltip.style.left = ((element.offsetWidth - tooltipWidth) / 2) + 'px';

                        setTimeout(() => {
                            tooltip.classList.add('show');
                        }, 0);
                        setTimeout(() => {
                            tooltip.classList.remove('show');
                            setTimeout(() => {
                                tooltip.remove();
                            }, 300);
                        }, 2000);
                    }
                })();
            });
        </script>
EOT;
}
add_shortcode('copy', 'copy_to_clipboard_shortcode');





function copy_to_clipboard_scripts() {
    wp_enqueue_style('clipboard-style', plugin_dir_url(__FILE__) . 'clipboard.css', array(), null, 'all');
    // wp_enqueue_script('clipboard-js', plugin_dir_url(__FILE__) . 'clipboard.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'copy_to_clipboard_scripts');
