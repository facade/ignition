<script>
export default {
    props: {
        tab: { required: true },
    },

    render(h) {
        return h(this.tab.component, {
            props: this.tab.props || {},
        });
    },
};
</script>
