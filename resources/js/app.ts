import { createApp, h } from 'vue'
import { Head, Link, createInertiaApp } from '@inertiajs/inertia-vue3'
import {InertiaProgress} from '@inertiajs/progress'
import route from "ziggy-js";

createInertiaApp({
  title: title => `Laravel Support - ${title}`,
  resolve: name => require(`./Pages/${name}`),
  setup({ el, app, props, plugin }) {
    createApp({ render: () => h(app, props) })
      .use(plugin)
      .component('Head', Head)
      .component('Link', Link)
      .mixin({ methods: { route } })
      .mount(el);
  },
})

InertiaProgress.init();