/*!
* @package     Extly.Library
* @subpackage  extlyframework6 for AutoTweet - Extly Framework
*
* @author      Extly, CB. <team@extly.com>
* @copyright   Copyright (C) 2012-2024 Extly, CB. All rights reserved.
* @license     http://http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* @link        http://www.extly.com http://support.extly.com
*/

XTGetMedia = (data) => new Promise((resolve, reject) => {
  if (!data || typeof data === 'object' && (!data.path || data.path === '')) {
    Joomla.selectedMediaFile = {};
    resolve({
      resp: {
        success: false
      }
    });
    return;
  }

  // Compile the url
  const url = new URL(Joomla.getOptions('media-picker-api').apiBaseUrl ? Joomla.getOptions('media-picker-api').apiBaseUrl : `${Joomla.getOptions('system.paths').baseFull}index.php?option=com_media&format=json`);
  url.searchParams.append('task', 'api.files');
  url.searchParams.append('url', true);
  url.searchParams.append('path', data.path);
  url.searchParams.append('mediatypes', '0,1,2,3');
  url.searchParams.append(Joomla.getOptions('csrf.token'), 1);
  fetch(url, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  }).then(response => response.json()).then(async response => resolve(response)).catch(error => reject(error));
});