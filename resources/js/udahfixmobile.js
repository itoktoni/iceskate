import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import * as bootstrap from 'bootstrap';
// import * as Turbo from "@hotwired/turbo";
import TomSelect from 'tom-select';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css'; // for React, Vue and Svelte

window.TomSelect = TomSelect;
window.bootstrap = bootstrap;

import { initializeSidebar } from './initialize';
initializeSidebar();
window.Notif = Notyf;

Livewire.start();
