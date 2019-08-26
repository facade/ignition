import '../css/app.css';

import Ignition from './Ignition';

window.ignite = data => {
    return new Ignition(data);
};
