/**
 * Low-Bandwidth Mode UI Component
 * Provides user controls for low-bandwidth features
 */

class LowBandwidthUI {
    constructor() {
        this.init();
    }

    init() {
        // Wait for manager to be available
        const checkInterval = setInterval(() => {
            if (window.lowBandwidthManager) {
                clearInterval(checkInterval);
                this.createToggle();
                this.createStatusIndicator();
                this.attachEventListeners();
            }
        }, 100);
    }

    /**
     * Create the low-bandwidth toggle button
     */
    createToggle() {
        // Check if toggle already exists
        if (document.getElementById('low-bandwidth-toggle')) return;

        const manager = window.lowBandwidthManager;
        const isEnabled = manager.isLowBandwidth;

        // Create container
        const container = document.createElement('div');
        container.id = 'low-bandwidth-ui';
        container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            background: white;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        `;

        // Status indicator
        const indicator = document.createElement('div');
        indicator.className = 'bandwidth-indicator';
        indicator.title = `Connection: ${manager.getStatus().connectionType}${manager.isSlowConnection ? ' (Slow)' : ''}`;
        indicator.style.cssText = `
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: ${isEnabled ? '#ff6b6b' : '#51cf66'};
            transition: background 0.3s;
        `;

        // Label
        const label = document.createElement('span');
        label.className = 'bandwidth-label';
        label.textContent = isEnabled ? 'Low Bandwidth' : 'Data Saver';
        label.style.cssText = `
            font-size: 13px;
            font-weight: 500;
            color: #333;
            cursor: pointer;
            user-select: none;
        `;

        // Toggle switch
        const toggle = document.createElement('input');
        toggle.type = 'checkbox';
        toggle.id = 'low-bandwidth-toggle';
        toggle.checked = isEnabled;
        toggle.style.cssText = `
            cursor: pointer;
            width: 24px;
            height: 14px;
            accent-color: #667eea;
        `;

        // Create a custom toggle switch
        const switchContainer = document.createElement('label');
        switchContainer.className = 'switch';
        switchContainer.style.cssText = `
            position: relative;
            display: inline-block;
            width: 42px;
            height: 22px;
        `;

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = 'low-bandwidth-switch';
        checkbox.checked = isEnabled;
        checkbox.style.cssText = `
            opacity: 0;
            width: 0;
            height: 0;
        `;

        const slider = document.createElement('span');
        slider.className = 'slider';
        slider.style.cssText = `
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: ${isEnabled ? '#667eea' : '#ccc'};
            transition: 0.3s;
            border-radius: 22px;
        `;

        // Create slider thumb
        const thumb = document.createElement('span');
        thumb.style.cssText = `
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: ${isEnabled ? '22px' : '2px'};
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: 0.3s;
        `;
        slider.appendChild(thumb);

        // Add change event to checkbox
        checkbox.addEventListener('change', (e) => {
            this.handleToggle(e.target.checked, slider, thumb);
        });

        switchContainer.appendChild(checkbox);
        switchContainer.appendChild(slider);

        // Append elements
        container.appendChild(indicator);
        container.appendChild(label);
        container.appendChild(switchContainer);

        document.body.appendChild(container);

        // Update toggle from manager
        document.addEventListener('lowBandwidthModeChanged', (e) => {
            checkbox.checked = e.detail.enabled;
            slider.style.backgroundColor = e.detail.enabled ? '#667eea' : '#ccc';
            thumb.style.left = e.detail.enabled ? '22px' : '2px';
            indicator.style.background = e.detail.enabled ? '#ff6b6b' : '#51cf66';
            label.textContent = e.detail.enabled ? 'Low Bandwidth' : 'Data Saver';
        });
    }

    /**
     * Create status indicator in navbar
     */
    createStatusIndicator() {
        const manager = window.lowBandwidthManager;
        if (!manager.isSlowConnection && !manager.isLowBandwidth) return;

        // Create indicator badge
        const badge = document.createElement('span');
        badge.className = 'network-status-badge';
        badge.innerHTML = `<i class="fas fa-wifi-slash"></i> Low Network`;
        badge.style.cssText = `
            background: #ff6b6b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: ${manager.isLowBandwidth ? 'inline-block' : 'none'};
            margin-left: 10px;
        `;

        // Find navbar and append badge
        const navbar = document.querySelector('nav');
        if (navbar) {
            navbar.appendChild(badge);
        }
    }

    /**
     * Handle toggle change
     */
    handleToggle(enabled, slider, thumb) {
        const manager = window.lowBandwidthManager;
        manager.setLowBandwidth(enabled);

        // Update slider appearance
        slider.style.backgroundColor = enabled ? '#667eea' : '#ccc';
        thumb.style.left = enabled ? '22px' : '2px';

        // Show toast notification
        this.showNotification(enabled ? 'Low-Bandwidth Mode On' : 'Low-Bandwidth Mode Off');
    }

    /**
     * Show notification toast
     */
    showNotification(message) {
        const toast = document.createElement('div');
        toast.className = 'bandwidth-toast';
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 90px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 9998;
            animation: slideUp 0.3s ease-out;
        `;

        // Add animation
        if (!document.getElementById('toast-animations')) {
            const style = document.createElement('style');
            style.id = 'toast-animations';
            style.textContent = `
                @keyframes slideUp {
                    from {
                        transform: translateY(20px);
                        opacity: 0;
                    }
                    to {
                        transform: translateY(0);
                        opacity: 1;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(toast);

        // Remove after 2 seconds
        setTimeout(() => {
            toast.style.animation = 'slideUp 0.3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        const manager = window.lowBandwidthManager;

        // Listen for offline events
        window.addEventListener('offline', () => {
            this.showNotification('⚠ You are offline');
        });

        window.addEventListener('online', () => {
            this.showNotification('✓ Back online!');
        });

        // Monitor connection changes
        if (navigator.connection) {
            navigator.connection.addEventListener('change', () => {
                const status = manager.getStatus();
                const typeChanged = status.slow !== manager.isSlowConnection;
                if (typeChanged) {
                    const msg = status.slow ? 'Slow connection detected' : 'Connection improved';
                    this.showNotification(msg);
                }
            });
        }
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.lowBandwidthUI = new LowBandwidthUI();
    });
} else {
    window.lowBandwidthUI = new LowBandwidthUI();
}
