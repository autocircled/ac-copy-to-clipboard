document.addEventListener('hustle:module:loaded', function (e) {
    console.log("jello");
    ac_copy_to_clipboard();
});
document.addEventListener('DOMContentLoaded', function (e) {
    ac_copy_to_clipboard();
});

function ac_copy_to_clipboard() {
    const copyButton = document.querySelector('.ac-copy-to-clipboard');

    if (copyButton) {
        copyButton.addEventListener('click', function (e) {
            if (e.target.closest('.ac-copy-to-clipboard')) {
                // const copyButton = e.target.closest('.ac-copy-to-clipboard');
                const targetId = copyButton.getAttribute('data-clipboard-target');
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
            }
        });
    }
}

function showTooltip(element, message) {
    // Create the tooltip
    const tooltip = document.createElement('div');
    tooltip.classList.add('ac-copy-tooltip');
    tooltip.innerText = message;
    
    // Append the tooltip inside the target element (the div)
    element.appendChild(tooltip);

    // Position the tooltip above the copied text
    const rect = element.getBoundingClientRect();
    const tooltipWidth = tooltip.offsetWidth;

    tooltip.style.position = 'absolute';
    tooltip.style.top = `-30px`;  // Adjust this value to move the tooltip up or down
    tooltip.style.left = `${(element.offsetWidth - tooltipWidth) / 2}px`; // Center it horizontally within the div

    // Show the tooltip for 2 seconds, then remove it
    setTimeout(() => {
        tooltip.classList.add('show');
    }, 0);
    setTimeout(() => {
        tooltip.classList.remove('show');
        setTimeout(() => {
            tooltip.remove();  // Now remove the element from DOM
        }, 300);
    }, 2000);
     // Tooltip disappears after 2 seconds
}
