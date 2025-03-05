import {defineConfig} from 'vite'
import tailwindcss from "@tailwindcss/vite";
import type { Plugin, EnvironmentModuleNode } from 'vite'

function hmrReload(): Plugin {
    return {
        name: 'hmr-reload',
        enforce: 'post',
        hotUpdate: {
            order: 'post',
            handler({ modules, server, timestamp }) {
                if (this.environment.name !== 'ssr') return

                let hasSsrOnlyModules = false

                const invalidatedModules = new Set<EnvironmentModuleNode>()
                for (const mod of modules) {
                    if (mod.id == null) continue
                    const clientModule =
                        server.environments.client.moduleGraph.getModuleById(mod.id)
                    if (clientModule != null) continue

                    this.environment.moduleGraph.invalidateModule(
                        mod,
                        invalidatedModules,
                        timestamp,
                        true,
                    )
                    hasSsrOnlyModules = true
                }

                if (hasSsrOnlyModules) {
                    server.ws.send({ type: 'full-reload' })
                    return []
                }
            },
        },
    }
}

export default defineConfig(({}) => ({
    build: {
        assetsDir: '',
        assetsInlineLimit: 0,
        outDir: './web/dist/',
        manifest: true,
        modulePreload: {
            polyfill: false,
        },
        rollupOptions: {
            external: [
            ],
            input: {
                'app': './resources/js/app.ts',
            },
            preserveEntrySignatures: 'strict',
        },
    },
    plugins: [
        hmrReload(),
        tailwindcss(),
    ],
}));