/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
import Header from './components/Header.vue';
import Hero from './components/Hero.vue';
import Welcome from './components/Welcome.vue';
import EventsNews from './components/EventsNews.vue';
import Features from './components/Features.vue';
import Footer from './components/Footer.vue';
import FullCalendar from './components/FullCalendar.vue';
import AdminHeader from './components/admin/AdminHeader.vue';
import AdminSidebar from './components/admin/AdminSidebar.vue';
import AdminFooter from './components/admin/AdminFooter.vue';
import AdminRegions from './components/admin/Regions.vue';
import AdminCouncils from './components/admin/Councils.vue';
import AdminSchools from './components/admin/Schools.vue';
import ResultsDistrict from './components/ResultsDistrict.vue';
import SchoolsList from './components/Schools.vue';
import Maintenance from './components/Maintenance.vue';
app.component('app-header', Header);
app.component('hero-section', Hero);
app.component('welcome-section', Welcome);
app.component('events-news', EventsNews);
app.component('features-section', Features);
app.component('footer-section', Footer);
app.component('full-calendar', FullCalendar);
app.component('admin-header', AdminHeader);
app.component('admin-sidebar', AdminSidebar);
app.component('admin-footer', AdminFooter);
app.component('admin-regions', AdminRegions);
app.component('admin-councils', AdminCouncils);
app.component('admin-schools', AdminSchools);
app.component('results-district', ResultsDistrict);
app.component('schools-list', SchoolsList);
app.component('maintenance-page', Maintenance);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
