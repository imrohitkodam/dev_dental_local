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

const jchOptimizeDynamicScriptLoader = {
    queue: [], // Scripts queued to be loaded synchronously
    loadJs: function (staticJsNode) {

        let newJsNode = document.createElement('script');

        if ('noModule' in HTMLScriptElement.prototype && staticJsNode.getAttribute('type') === 'jchoptimize-text/nomodule') {
            this.next();
            return;
        }

        if (!'noModule' in HTMLScriptElement.prototype && staticJsNode.getAttribute('type') === 'jchoptimize-text/module') {
            this.next();
            return;
        }

        if (staticJsNode.getAttribute('type') === 'jchoptimize-text/module') {
            newJsNode.type = 'module';
            newJsNode.onload = function () {
                jchOptimizeDynamicScriptLoader.next();
            }
        }

        if (staticJsNode.getAttribute('type') === 'jchoptimize-text/nomodule') {
            newJsNode.setAttribute('nomodule', '');
        }

        if (staticJsNode.hasAttribute('src')) {
            newJsNode.src = staticJsNode.getAttribute('src');
        }

        if (staticJsNode.innerText) {
            newJsNode.innerText = staticJsNode.innerText;
        }

        staticJsNode.replaceWith(newJsNode);
    },
    add: function (data) {
        // Load an array of scripts
        this.queue = data;
        this.next();
    },
    next: function () {
        let result;

        try {
            result = this.queue.next();
            window.onerror = () => {
                jchOptimizeDynamicScriptLoader.next();
            }
        } catch (e) {
            this.next();
        }

        if (!result.done) {
            // Load the script
            this.loadJs(result.value[1]);
        } else {
            document.dispatchEvent(new Event("onJchJsDynamicLoaded"));
            return false;
        }
    }
};

JchOptimizeUserInteraction.onUserInteract(() => {
    jchOptimizeDynamicScriptLoader.add(document.querySelectorAll('script[type^="jchoptimize-text"]').entries());
});
