<?php

namespace Leuverink\Bundle;

use SplFileInfo;
use Leuverink\Bundle\Traits\Constructable;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Leuverink\Bundle\Contracts\BundleManager as BundleManagerContract;

class InjectCore
{
    use Constructable;

    /** Injects a inline script tag containing Bundle's core inside every full-page response */
    public function __invoke(RequestHandled $handled)
    {
        $html = $handled->response->getContent();

        // Skip if request doesn't return a full page
        if (! str_contains($html, '</html>')) {
            return;
        }

        // Skip if core was included before
        if (str_contains($html, '<!--[BUNDLE-CORE]-->')) {
            return;
        }

        // Bundle it up & wrap in script tag
        $script = $this->wrapInScriptTag(
            file_get_contents($this->bundle())
        );

        // Inject into response
        $originalContent = $handled->response->original;

        $handled->response->setContent(
            $this->injectAssets($html, $script)
        );

        $handled->response->original = $originalContent;
    }

    public function bundle(): SplFileInfo
    {
        return $this->manager()->bundle(
            $this->core()
        );
    }

    /** Get an instance of the BundleManager */
    protected function manager(): BundleManagerContract
    {
        return BundleManager::new();
    }

    /** Injects Bundle's core into given html string (taken from Livewire's injection mechanism) */
    protected function injectAssets(string $html, string $core): string
    {
        $html = str($html);

        if ($html->test('/<\s*\/\s*head\s*>/i')) {
            return $html
                ->replaceMatches('/(<\s*\/\s*head\s*>)/i', $core . '$1')
                ->toString();
        }

        return $html
            ->replaceMatches('/(<\s*html(?:\s[^>])*>)/i', '$1' . $core)
            ->toString();
    }

    /** Wrap the contents in a inline script tag */
    protected function wrapInScriptTag($contents): string
    {
        return <<< HTML
        <!--[BUNDLE-CORE]-->
        <script type="module" data-bundle="core">
        {$contents}
        </script>
        <!--[ENDBUNDLE]>-->
        HTML;
    }

    protected function core(): string
    {
        $timeout = $this->manager()->config()->get('import_resolution_timeout');

        return <<< JS

            //--------------------------------------------------------------------------
            // Expose x_import_modules map
            //--------------------------------------------------------------------------
            if(!window.x_import_modules) window.x_import_modules = {};


            //--------------------------------------------------------------------------
            // Expose _import function
            //--------------------------------------------------------------------------
            window._import = async function(alias, exportName = 'default') {

            // Wait for module to become available (account for invoking from non-deferred script)
            const module = await poll(
                () => window.x_import_modules[alias],
                {$timeout}, 5, alias
            )

            if(module === undefined) {
                console.info('When invoking _import() from a script tag make sure it has type="module"')
                throw `BUNDLE ERROR: '\${alias}' not found`;
            }

            return module[exportName] !== undefined
                // Return export if it exists
                ? module[exportName]
                // Otherwise the entire module
                : module
            };


            //--------------------------------------------------------------------------
            // Non-blocking polling mechanism
            //--------------------------------------------------------------------------
            async function poll(success, timeout, interval, ref) {
                const startTime = new Date().getTime();

                while (true) {
                    // If the success callable returns something truthy, return
                    let result = success()
                    if (result) return result;

                    // Check if timeout has elapsed
                    const elapsedTime = new Date().getTime() - startTime;
                    if (elapsedTime >= timeout) {
                        throw `BUNDLE TIMEOUT: '\${ref}' could not be resolved`;
                    }

                    // Wait for a set interval
                    await new Promise(resolve => setTimeout(resolve, interval));
                }
            };

        JS;
    }
}
