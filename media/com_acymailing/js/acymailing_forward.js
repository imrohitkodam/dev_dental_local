/**
 * @package    AcyMailing for Joomla!
 * @version    5.13.0
 * @author     acyba.com
 * @copyright  (C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

var formName;

function submitacymailingforward(newformName) {
    formName = newformName;

    var recaptchaid = 'acymailing-captcha';
    if (newformName) recaptchaid = newformName + '-captcha';

    var invisibleRecaptcha = document.querySelector('#' + recaptchaid + '[class="g-recaptcha"][data-size="invisible"]');
    if (invisibleRecaptcha && typeof grecaptcha == 'object') {

        var grcID = invisibleRecaptcha.getAttribute('grcID');

        if (!grcID) {
            grcID = grecaptcha.render(recaptchaid, {
                'sitekey': invisibleRecaptcha.getAttribute('data-sitekey'),
                'callback': 'acySubmitForward',
                'size': 'invisible',
                'expired-callback': 'resetRecaptcha'
            });

            invisibleRecaptcha.setAttribute('grcID', grcID);
        }

        var response = grecaptcha.getResponse(grcID);
        if (response) {
            return acySubmitForward();
        } else {
            grecaptcha.execute(grcID);
            return false;
        }
    } else {
        return acySubmitForward();
    }
}

function resetRecaptcha() {
    var recaptchaid = 'acymailing-captcha';
    if (formName) recaptchaid = formName + '-captcha';

    var invisibleRecaptcha = document.querySelector('#' + recaptchaid + '[class="g-recaptcha"][data-size="invisible"]');
    if (!invisibleRecaptcha) return;

    var grcID = invisibleRecaptcha.getAttribute('grcID');
    grecaptcha.reset(grcID);
}

function acySubmitForward() {
    var varform = document[formName];
    varform.submit();
    return false;
}
