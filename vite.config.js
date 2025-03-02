import { defineConfig } from 'vite';
import { resolve } from 'path';
import fs from 'fs';

// Create a version hash for cache busting
const createVersionHash = () => {
  return Math.floor(Date.now() / 1000).toString();
};

// Get theme info from style.css
const getThemeInfo = () => {
  const styleContent = fs.readFileSync('./style.css', 'utf8');
  const version = styleContent.match(/Version: (.*)/i)?.[1] || '1.0.0';
  return { version };
};

// Write assets.php file for WordPress to get the correct assets
const writeAssetsManifest = (manifest) => {
  const themeInfo = getThemeInfo();
  const versionHash = createVersionHash();
  
  const phpContent = `<?php
/**
 * Assets manifest
 * 
 * Generated automatically by Vite build script
 * Do not modify manually
 */
return [
  'version' => '${themeInfo.version}.${versionHash}',
  'manifest' => ${JSON.stringify(manifest, null, 2)}
];`;

  fs.writeFileSync('./dist/assets.php', phpContent);
};

export default defineConfig({
  // Base public path when served in production
  base: './dist/',
  
  // Build options
  build: {
    // Output directory
    outDir: 'dist',
    
    // Generate manifest.json for cache-busting
    manifest: true,
    
    // Output directory structure
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/js/main.js'),
      },
      output: {
        entryFileNames: 'assets/js/[name].js',
        chunkFileNames: 'assets/js/chunks/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const extType = info[info.length - 1];
          
          if (/\.(css|scss|sass)$/.test(assetInfo.name)) {
            return 'assets/css/[name][extname]';
          }
          
          if (/\.(png|jpe?g|gif|svg|webp|ico)$/.test(assetInfo.name)) {
            return 'assets/images/[name][extname]';
          }
          
          if (/\.(woff2?|eot|ttf|otf)$/.test(assetInfo.name)) {
            return 'assets/fonts/[name][extname]';
          }
          
          return 'assets/[name][extname]';
        },
      },
    },
    
    // Minify output
    minify: true,
    
    // CSS code splitting
    cssCodeSplit: true,
    
    // Custom hook to generate assets.php
    emptyOutDir: true,
    writePlugin: {
      name: 'write-manifest',
      writeBundle(_, bundle) {
        writeAssetsManifest(bundle);
      },
    },
  },
  
  // CSS preprocessing
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "./assets/scss/variables";`,
      },
    },
  },
  
  // Development server options
  server: {
    // Expose to network
    host: '0.0.0.0',
    
    // Use port 3000 (default for WordPress development)
    port: 3000,
    
    // Hot Module Replacement
    hmr: {
      protocol: 'ws',
      host: 'localhost',
    },
    
    // Watch for file changes
    watch: {
      usePolling: true,
    },
  },
  
  // Resolve aliases
  resolve: {
    alias: {
      '@': resolve(__dirname, 'assets'),
      '@css': resolve(__dirname, 'assets/scss'),
      '@js': resolve(__dirname, 'assets/js'),
      '@images': resolve(__dirname, 'assets/images'),
    },
  },
  
  // Plugins
  plugins: [
    // Custom plugin to write assets.php
    {
      name: 'write-manifest',
      generateBundle(_, bundle) {
        // Create dist directory if it doesn't exist
        if (!fs.existsSync('./dist')) {
          fs.mkdirSync('./dist', { recursive: true });
        }
        
        writeAssetsManifest(bundle);
      },
    },
  ],
});