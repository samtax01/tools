class HrefMonitor {
  constructor() {
    this.ignorableClasses = new Set(['ignore-href-monitor']); // Add class names here
    this.debouncedUpdate = this.debounce(() => this.updateAllHrefLabels(), 300);

    this.injectStyles();
    this.updateAllHrefLabels();
    this.setupMutationObserver();
  }

  injectStyles() {
    if (!document.getElementById('href-monitor-styles')) {
      const styleElement = document.createElement('style');
      styleElement.id = 'href-monitor-styles';
      styleElement.textContent = `
      .href-label {
        display: inline-block;
        margin-left: 5px;
        padding: 2px 6px;
        background-color: rgba(243, 244, 246, 0.9);
        border-radius: 4px;
        font-size: 0.75rem;
        color: #6b7280;
        vertical-align: middle;
        transition: all 0.3s ease;
        cursor: help;
        user-select: none;
      }
      .href-label:hover {
        background-color: rgba(229, 231, 235, 0.9);
      }
    `;
      document.head.appendChild(styleElement);
    }
  }

  setupMutationObserver() {
    const observer = new MutationObserver((mutations) => {
      for (const mutation of mutations) {
        if (
          mutation.type === 'childList' ||
          (mutation.type === 'attributes' && mutation.attributeName === 'href')
        ) {
          this.debouncedUpdate();
          break;
        }
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ['href']
    });
  }

  updateAllHrefLabels() {
    const anchors = document.querySelectorAll('a');

    anchors.forEach(anchor => {
      // Skip if anchor has ignorable class
      if ([...this.ignorableClasses].some(cls => anchor.classList.contains(cls))) return;

      this.updateOrCreateLabel(anchor);
    });
  }

  updateOrCreateLabel(anchor) {
    const href = anchor.getAttribute('href');
    if (!href) return;

    let label = anchor.nextElementSibling;
    if (label && label.classList.contains('href-label')) {
      if (label.textContent !== href) {
        label.textContent = href; // Update only if different
      }
    } else {
      label = document.createElement('span');
      label.className = 'href-label';
      label.textContent = href;
      anchor.parentNode.insertBefore(label, anchor.nextSibling);
    }
  }

  debounce(func, wait) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new HrefMonitor();
});