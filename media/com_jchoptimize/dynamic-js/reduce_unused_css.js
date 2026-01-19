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

JchOptimizeUserInteraction.onUserInteract(() => {
    const linkNodeList = document.querySelectorAll(
        'link[type^="jchoptimize-text"], style[type^="jchoptimize-text"]'
    );

    linkNodeList.forEach(function (staticNode, index) {
        if (staticNode.tagName === 'LINK') {
            let newLinkNode = document.createElement('link');
            newLinkNode.rel = 'stylesheet';
            newLinkNode.href = staticNode.href;

            staticNode.replaceWith(newLinkNode);
        } else if (staticNode.tagName === 'STYLE') {
            let newStyleNode = document.createElement('style');
            newStyleNode.textContent = staticNode.textContent;

            staticNode.replaceWith(newStyleNode);
        }
    });

    document.dispatchEvent(new Event("onJchCssAsyncLoaded"));
});
