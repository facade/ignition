module.exports = {
    important: true,
    theme: {
        fontFamily: {
            sans: [
                '-apple-system',
                'BlinkMacSystemFont',
                '"Segoe UI"',
                'Roboto',
                '"Helvetica Neue"',
                'Arial',
                '"Noto Sans"',
                'sans-serif',
                '"Apple Color Emoji"',
                '"Segoe UI Emoji"',
                '"Segoe UI Symbol"',
                '"Noto Color Emoji"',
            ],
            mono: [
                'SFMono-Regular',
                'Menlo',
                'Monaco',
                'Consolas',
                '"Liberation Mono"',
                '"Courier New"',
                'monospace',
            ],
        },
        colors: {
            white: 'var(--white)',
            blue: {
                400: 'var(--blue-400)',
            },
            green: {
                100: 'var(--green-100)',
                300: 'var(--green-300)',
                500: 'var(--green-500)',
            },
            purple: {
                100: 'var(--purple-100)',
                200: 'var(--purple-200)',
                300: 'var(--purple-300)',
                400: 'var(--purple-400)',
                500: 'var(--purple-500)',
                600: 'var(--purple-600)',
                800: 'var(--purple-800)',
            },
            red: {
                100: 'var(--red-100)',
                300: 'var(--red-300)',
                400: 'var(--red-400)',
            },
            yellow: {
                100: 'var(--yellow-100)',
                200: 'var(--yellow-200)',
                300: 'var(--yellow-300)',
                400: 'var(--yellow-400)',
            },
            gray: {
                50: 'var(--gray-50)',
                100: 'var(--gray-100)',
                200: 'var(--gray-200)',
                300: 'var(--gray-300)',
                400: 'var(--gray-400)',
                500: 'var(--gray-500)',
                600: 'var(--gray-600)',
                700: 'var(--gray-700)',
                800: 'var(--gray-800)',
            },
            tint: {
                50: 'var(--tint-50)',
                100: 'var(--tint-100)',
                200: 'var(--tint-200)',
                300: 'var(--tint-300)',
                400: 'var(--tint-400)',
                500: 'var(--tint-500)',
                600: 'var(--tint-600)',
                700: 'var(--tint-700)',
            },
        },
        extend: {
            borderWidth: {
                3: '3px',
            },
            boxShadow: {
                sm: 'var(--shadow-sm)',
                default: 'var(--shadow-default)',
                lg: 'var(--shadow-default)',
                input: 'var(--shadow-default)',
            },
            inset: {
                full: '100%',
            },
            maxWidth: {
                '7xl': '80rem',
            },
            minHeight: {
                6: '1.5rem',
                10: '2.5rem',
                12: '3rem',
                full: '100%',
            },
            minWidth: {
                8: '2rem',
            },
            zIndex: {
                1: '1',
            },
            leading: {
                tight: '1.1',
            },
        },
    },
};
