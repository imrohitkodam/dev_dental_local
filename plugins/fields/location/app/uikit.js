import CloseCircleIcon from '../../../../vendor/assets/uikit/src/images/icons/close-circle.svg';
import LocationIcon from '../../../../vendor/assets/uikit/src/images/icons/location.svg';
import UIkit from '../../../../vendor/assets/uikit/src/js/api/index';
import Dropdown from '../../../../vendor/assets/uikit/src/js/core/drop';
import { default as Icon, Spinner } from '../../../../vendor/assets/uikit/src/js/core/icon';

// register components
UIkit.component('dropdown', Dropdown);
UIkit.component('icon', Icon);
UIkit.component('spinner', Spinner);

UIkit.icon.add('location', LocationIcon);
UIkit.icon.add('close-circle', CloseCircleIcon);

export default UIkit;

export const { dropdown } = UIkit;
