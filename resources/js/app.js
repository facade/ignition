import '../css/app.css';

require('./vendor/symfony');

import Ignition from './Ignition';

window.ignite = data => {
    return new Ignition(data);
};
