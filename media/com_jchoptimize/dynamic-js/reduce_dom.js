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
    'use script';

    const lazyLoadHtml = () => {
        let callback = (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const template = entry.target.querySelector(':scope > template.jchoptimize-reduce-dom__template');
                    if (template !== null) {
                        const clone = template.content.cloneNode(true);
                        entry.target.replaceChild(clone, template);
                        entry.target.className = entry.target.className.replace(
                            /(jchoptimize-reduce-dom)/,
                            "$1_loaded"
                        );
                        document.dispatchEvent(new Event("onJchDomLoaded"));
                    }

                    observer.unobserve(entry.target);

                    const innerTargets = entry.target.querySelectorAll('.jchoptimize-reduce-dom');
                    innerTargets.forEach((innerTarget) => {
                        observer.observe(innerTarget);
                    });
                }
            })
        }

        const observer = new IntersectionObserver(callback);
        const targets = document.querySelectorAll('.jchoptimize-reduce-dom');

        targets.forEach((target) => {
            observer.observe(target);
        });
    }

    if (document.readyState === 'loading') {
        window.addEventListener('DOMContentLoaded', lazyLoadHtml);
    } else {
        lazyLoadHtml();
    }
}());
