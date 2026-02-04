/**
 * LearnBridge Low-Bandwidth Mode Manager
 * Helps users in areas with slow/limited network access
 */

class LowBandwidthManager {
    constructor() {
        this.isLowBandwidth = localStorage.getItem('lowBandwidthMode') === 'true';
        this.isSlowConnection = false;
        this.imageQuality = localStorage.getItem('imageQuality') || 'medium';
        this.init();
    }

    /**
     * Initialize low-bandwidth detection and setup
     */
    init() {
        // Check for slow connection using Network Information API
        if (navigator.connection) {
            this.checkConnectionSpeed();
            navigator.connection.addEventListener('change', () => this.checkConnectionSpeed());
        }

        // Listen for user preference changes
        document.addEventListener('lowBandwidthToggle', (e) => {
            this.setLowBandwidth(e.detail.enabled);
        });

        // Auto-enable low-bandwidth if connection is very slow
        if (this.isSlowConnection) {
            this.setLowBandwidth(true);
        }

        // Apply current settings
        this.applySettings();
    }

    /**
     * Check if connection is slow using Network Information API
     */
    checkConnectionSpeed() {
        if (!navigator.connection) return;

        const connection = navigator.connection;
        const effectiveType = connection.effectiveType;
        const downlink = connection.downlink;

        // Consider connection slow if:
        // - effective type is 2g or 3g, OR
        // - downlink speed is less than 1 Mbps
        this.isSlowConnection = effectiveType === '2g' || effectiveType === '3g' || downlink < 1;

        console.log(`[LowBandwidth] Connection: ${effectiveType}, Downlink: ${downlink} Mbps, Slow: ${this.isSlowConnection}`);

        // Auto-enable if very slow
        if (this.isSlowConnection && !this.isLowBandwidth) {
            console.log('[LowBandwidth] Auto-enabling low-bandwidth mode due to slow connection');
            this.setLowBandwidth(true);
        }
    }

    /**
     * Toggle low-bandwidth mode
     */
    setLowBandwidth(enabled) {
        this.isLowBandwidth = enabled;
        localStorage.setItem('lowBandwidthMode', enabled ? 'true' : 'false');
        this.applySettings();
        this.notifyUI();
        console.log(`[LowBandwidth] Mode ${enabled ? 'enabled' : 'disabled'}`);
    }

    /**
     * Apply low-bandwidth settings to page
     */
    applySettings() {
        if (this.isLowBandwidth) {
            document.documentElement.setAttribute('data-low-bandwidth', 'true');
            this.disableImages();
            this.disableVideos();
            this.disableAnimations();
            this.compressContent();
        } else {
            document.documentElement.removeAttribute('data-low-bandwidth');
            this.enableImages();
            this.enableAnimations();
        }
    }

    /**
     * Disable image loading and show placeholders
     */
    disableImages() {
        // Hide all images
        const images = document.querySelectorAll('img:not([data-no-disable])');
        images.forEach(img => {
            img.style.display = 'none';
            // Create text placeholder
            if (!img.nextElementSibling || !img.nextElementSibling.classList.contains('image-placeholder')) {
                const placeholder = document.createElement('div');
                placeholder.className = 'image-placeholder';
                placeholder.textContent = `📷 ${img.alt || 'Image'}`;
                placeholder.style.cssText = `
                    display: inline-block;
                    background: #f0f0f0;
                    border: 1px dashed #999;
                    padding: 10px;
                    border-radius: 4px;
                    font-size: 12px;
                    color: #666;
                    margin: 5px 0;
                `;
                img.parentNode.insertBefore(placeholder, img.nextSibling);
            }
        });
    }

    /**
     * Re-enable image loading
     */
    enableImages() {
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            img.style.display = '';
        });
        const placeholders = document.querySelectorAll('.image-placeholder');
        placeholders.forEach(p => p.remove());
    }

    /**
     * Disable video content
     */
    disableVideos() {
        const videos = document.querySelectorAll('video, iframe[src*="youtube"], iframe[src*="vimeo"]');
        videos.forEach(video => {
            video.style.display = 'none';
            const msg = document.createElement('div');
            msg.className = 'video-disabled-notice';
            msg.innerHTML = `
                <p style="background: #fff3cd; border: 1px solid #ffc107; padding: 10px; border-radius: 4px;">
                    📹 Video content disabled in low-bandwidth mode
                </p>
            `;
            video.parentNode.insertBefore(msg, video);
        });
    }

    /**
     * Disable CSS animations
     */
    disableAnimations() {
        if (document.getElementById('no-animations-style')) return;

        const style = document.createElement('style');
        style.id = 'no-animations-style';
        style.textContent = `
            * {
                animation: none !important;
                transition: none !important;
            }
            [data-low-bandwidth="true"] * {
                animation: none !important;
                transition: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Remove animation disable styles
     */
    enableAnimations() {
        const style = document.getElementById('no-animations-style');
        if (style) style.remove();
    }

    /**
     * Reduce content size and complexity
     */
    compressContent() {
        // Remove shadows and complex effects
        if (document.getElementById('no-effects-style')) return;

        const style = document.createElement('style');
        style.id = 'no-effects-style';
        style.textContent = `
            [data-low-bandwidth="true"] * {
                box-shadow: none !important;
                text-shadow: none !important;
                filter: none !important;
                backdrop-filter: none !important;
            }
            [data-low-bandwidth="true"] .shadow {
                box-shadow: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Notify UI that low-bandwidth mode changed
     */
    notifyUI() {
        const event = new CustomEvent('lowBandwidthModeChanged', {
            detail: { enabled: this.isLowBandwidth }
        });
        document.dispatchEvent(event);
    }

    /**
     * Get current bandwidth status
     */
    getStatus() {
        return {
            enabled: this.isLowBandwidth,
            slow: this.isSlowConnection,
            connectionType: navigator.connection?.effectiveType || 'unknown'
        };
    }

    /**
     * Load images on demand (when clicked)
     */
    enableOnDemandImages() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('image-placeholder')) {
                const img = e.target.nextElementSibling;
                if (img && img.tagName === 'IMG') {
                    e.target.remove();
                    img.style.display = '';
                }
            }
        });
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.lowBandwidthManager = new LowBandwidthManager();
    });
} else {
    window.lowBandwidthManager = new LowBandwidthManager();
}
