import {readdir, readFile, writeFile} from 'node:fs/promises';
import {join} from 'node:path';
import {optimize} from 'svgo';

const root = 'web/images';

// SVGs svgo must leave alone, as paths relative to `web/images` (e.g. `site/logo.svg`). In rare cases its default
// plugins break an animation, by merging the paths or dropping the ids that CSS or SMIL animate.
const exclude = [];

// The defaults, except that styles stay in their `<style>` element: inlined into a `style` attribute, a rule would
// beat the one in a media query, as the dark mode fill of the favicon.
const config = {
    plugins: [
        {
            name: 'preset-default',
            params: {
                overrides: {
                    inlineStyles: false,
                },
            },
        },
    ],
};

for (const file of (await readdir(root, {recursive: true})).map((file) => file.replaceAll('\\', '/')).sort()) {
    if (!file.endsWith('.svg') || exclude.includes(file)) {
        continue;
    }

    const path = join(root, file);
    const svg = await readFile(path, 'utf8');
    const {data} = optimize(svg, {...config, path});

    // Only a smaller result is written, so files that are already optimized stay untouched.
    if (data.length < svg.length) {
        await writeFile(path, data);
        console.log(`${file}: ${svg.length} → ${data.length} bytes`);
    }
}
