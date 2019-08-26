export default {
    functional: true,

    props: {
        label: { default: '' },
    },

    render(h, context) {
        return [
            h(
                'dt',
                {
                    attrs: {
                        class: 'definition-label',
                    },
                },
                context.props.label,
            ),
            h(
                'dd',
                {
                    attrs: {
                        class: 'definition-value',
                    },
                },
                context.children,
            ),
        ];
    },
};
