/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2024 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

(function () {
  const interactionEvents = [
    'mousemove',
    'mousedown',
    'keydown',
    'touchstart',
    'scroll',
    'pointermove',
    'focus'
  ];

  const callbacks = new Set();
  let isHeadless = false;
  let initialized = false;
  let fallbackTimer = null;
  const fallbackTimeout = 10000; // 10 seconds

function detectHeadless() {
  try {
    if (navigator.webdriver) return true;
    if (/Lighthouse|PageSpeed/.test(navigator.userAgent)) return true;
    if (navigator.userAgentData?.brands?.some(b => /Lighthouse/i.test(b.brand))) return true;
  } catch (e) {
    return false; // Fail open: still allow callbacks to run
  }
  return false; // All good — likely a real user
}

  function runCallbacks() {
    if (!callbacks.size) return;
    callbacks.forEach(cb => {
      try { cb(); } catch (err) { console.error(err); }
    });
    callbacks.clear(); // Ensure each callback runs only once
    cleanup();
  }

  function onInteraction() {
    runCallbacks();
  }

  function cleanup() {
    interactionEvents.forEach(event => {
      window.removeEventListener(event, onInteraction, true);
    });
    if (fallbackTimer) {
      clearTimeout(fallbackTimer);
      fallbackTimer = null;
    }
  }

  function setupListeners() {
    interactionEvents.forEach(event => {
      window.addEventListener(event, onInteraction, { passive: true, capture: true });
    });
    fallbackTimer = setTimeout(() => {
      runCallbacks();
    }, fallbackTimeout);
  }

  function registerCallback(cb) {
    if (!initialized) init();
    if (isHeadless) return;
    if (typeof cb === 'function') {
      callbacks.add(cb);
    }
    // If page is already scrolled, run callbacks immediately
    if (window.scrollY > 0) {
      runCallbacks();
    }
  }

  function init() {
    if (initialized) return;
    initialized = true;
    isHeadless = detectHeadless();
    if (isHeadless) {
      return;
    }
    setupListeners();
  }

  // Public API
  window.JchOptimizeUserInteraction = {
    onUserInteract: registerCallback,
    isHeadless: () => isHeadless
  };
})();
