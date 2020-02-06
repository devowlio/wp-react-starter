# Persistent database snapshot

Sometimes it can be useful and necessery to import initial database entries to the WordPress database. The boilerplate comes with a mechanism that allows you to define tables, they get dumped into a single file and are imported automatically with the next WordPress installation.

{% hint style="info" %}
It can be extremely helpful using initial imports for [E2E tests](tests.md#E2E).
{% endhint %}

Here is a simple scenario you can adopt to your use case:

1. [`yarn docker:start`](../usage/available-commands/root.md#development) to start your WordPress installation
1. [Login](../usage/getting-started#open-wordpress) to your WordPress instance and create a new post
1. Define tables you want to snapshot for the startup in [`package.json#db:snapshot`](../usage/folder-structure/root.md#folder-structure) like this `"db:snapshot": ["wp_posts", "wp_postmeta"]`
1. [`yarn db:snapshot-import-on-startup`](../usage/available-commands/root.md#database) to export the defined database tables into a file in [`devops/scripts/startup.sql`](../usage/folder-structure/root.md#folder-structure)
1. [`yarn docker:purge`](../usage/available-commands/root.md#development) removes your current WordPress installation completely
1. [`yarn docker:start`](../usage/available-commands/root.md#development) again and you will see that post is immediatly available
