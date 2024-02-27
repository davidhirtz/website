import air from 'air-scss';
import builder from 'air-esbuild';

await air({
    content: [
        'app/helpers/*.php',
        'app/models/*.php',
        'app/widgets/*.php',
        'resources/views/**/*.php',
    ],
    output: 'resources/scss/__generated.scss',
});

new builder();