import Vue from 'vue';
import App from './components/App';

export default class Ignition {
    constructor(data) {
        this.data = data;

        this.tabCallbacks = [];
    }

    registerBuiltinTabs() {
        Vue.component('AppTab', require('./components/Tabs/AppTab').default);
        Vue.component('ContextTab', require('./components/Tabs/ContextTab').default);
        Vue.component('DebugTab', require('./components/Tabs/DebugTab').default);
        Vue.component('RequestTab', require('./components/Tabs/RequestTab').default);
        Vue.component('StackTab', require('./components/Tabs/StackTab').default);
        Vue.component('UserTab', require('./components/Tabs/UserTab').default);
    }

    registerCustomTabs() {
        this.tabCallbacks.forEach(callback => callback(Vue, this.data));

        this.tabCallbacks = [];
    }

    registerTab(callback) {
        this.tabCallbacks.push(callback);
    }

    start() {
        this.registerBuiltinTabs();

        this.registerCustomTabs();

        window.app = new Vue({
            data: () => this.data,

            render(h) {
                return h(App, {
                    props: {
                        report: {
                            ...this.report,
                            stacktrace: this.report.stacktrace.map(frame => ({
                                ...frame,
                                relative_file: frame.file.replace(
                                    `${this.report.application_path}/`,
                                    '',
                                ),
                            })),
                        },
                        config: this.config,
                        solutions: this.solutions,
                        telescopeUrl: this.telescopeUrl,
                        shareEndpoint: this.shareEndpoint,
                    },
                });
            },
        }).$mount('#app');
    }
}
