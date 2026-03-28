import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')
    const hmrHost = env.VITE_HMR_HOST || '192.168.254.102'
    const hmrPort = Number(env.VITE_HMR_PORT || 5173)
    const hmrProtocol = env.VITE_HMR_PROTOCOL || 'ws'
    const originProtocol = hmrProtocol === 'wss' ? 'https' : 'http'

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/css/college.css',
                    'resources/js/app.js',
                    'resources/images/seal/1.png',
                    'resources/images/seal/2.png',
                    'resources/images/seal/3.png',
                ],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            host: '0.0.0.0',
            port: hmrPort,
            strictPort: true,
            origin: `${originProtocol}://${hmrHost}:${hmrPort}`,
            hmr: {
                host: hmrHost,
                port: hmrPort,
                clientPort: hmrPort,
                protocol: hmrProtocol,
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    }
})