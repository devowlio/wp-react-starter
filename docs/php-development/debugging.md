# Debugging

If you are using **VSCode** you are ready to use preconfigured debug settings for PHP files. Just navigate to the ant icon left in your VSCode sidebar and click the play button. Read more about [here](https://code.visualstudio.com/docs/editor/debugging).

Generally, the following things gets done when it comes to debugging:

1. Start your environment with [`yarn docker:start`](../usage/available-commands/root.md#development)
1. Start the debugger with the play icon
1. Wait a moment until the debugger is ready. This can take a while for the first time because XDebug will be installed in the WordPress container
1. Create a breakpoint in a PHP file
1. Do something so the code gets executed
1. The browser freezes and VSCode shows you that a breakpoint is reached
1. Do something
1. Stop the debugger
1. XDebug gets deactivated in the WordPress container automatically

{% hint style="warning" %}
Do you get an error while starting the debugger? Please refer to this [thread](https://github.com/qoomon/docker-host/issues/21#issuecomment-497831038).
{% endhint %}

## Remote development

If you are using the [Remote SSH extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-ssh) already you do not need to take further configurations. Just connect with your server via SSH and start the debugger. If you notice that the debugger "freezes" or "hangs" please make sure the debug port (in our case `9000`) can be opened. Check your firewall, read more [here](https://serverfault.com/a/309111).
