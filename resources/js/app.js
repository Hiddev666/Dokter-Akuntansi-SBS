import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;

Alpine.plugin(collapse);

Alpine.store('sidebar', {
    collapsed: false,
    toggle() {
        this.collapsed = !this.collapsed;
    }
});

Alpine.start();
