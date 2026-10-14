import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import ui from './ui';
import configurator from './ui/configurator';
import consent from './ui/consent';

document.documentElement.classList.remove('no-js');

Alpine.plugin(ui);
Alpine.plugin(configurator);
Alpine.plugin(consent);

Livewire.start();
