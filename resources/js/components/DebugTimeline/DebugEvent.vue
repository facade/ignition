<script>
export default {
    props: ['event'],

    render(h) {
        return h(require(`./Events/${this.event.getComponentName()}.vue`).default, {
            props: { event: this.event },
            attrs: {
                class: 'tab-content-section',
            },
        });
    },
};
</script>
