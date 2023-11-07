import air from 'air-scss';
import builder from 'air-esbuild';

await air({
    content: [
        'app/components/**/*.php',
        'app/models/*.php',
        'resources/views/**/*.php',
    ],
    output: 'resources/scss/__generated.scss',
});

new builder();