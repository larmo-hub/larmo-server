# Plugins in Larmo Hub

## What Larmo Hub plugins can do?

At the moment, plugins are responsible for:
* filtering and interpreting incoming messages from different agents
* [[TODO](https://github.com/adrianpietka/larmo/issues/22)] preparing filtered output for different output streams

When agent talks to Larmo Hub, decoded messages are filtered by `source` header field. 
That field is basically identifier of a plugin, that is responsible for decoding messages from that source.
If you have GitHub agent that's sending messages with `source="github"`, you should also have a plugin 
that will have `"github"` identifier. If no plugin exists for given `source` field, packet will be dropped 
without any further processing.

## How to use plugins?

Just place your plugin in `hub/Plugin` directory. If your plugins meets plugin requiremets, 
it will be automatically recognized by Hub. Please make sure you've read 
plugin's README.md and configured it properly before reporting any issues.


## How can I create my own plugin?

Assuming your plugin has identifier `myplugin`, you have to:
* place `PluginManifest.php` file in `hub/Plugin/Myplugin` directory (please note the directory name case)
* make sure `PluginManifest.php` file's namespace is `FP\Larmo\Plugin\Myplugin`
* `PluginManifest.php` file contains only one class `PluginManifest` that implements `FP\Larmo\Domain\Service\PluginManifestInterface`

**Disclaimer**: this project for now is at _Proof of Concept_ state, meaning we don't guarantee that everything will be working properly! You've been warned!

If you need more information, please check our stock plugin `Github`. 



