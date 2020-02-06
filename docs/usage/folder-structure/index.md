# Folder structure

If you come from the [getting started guide](../getting-started.md), you may now be completely desperate because you see many folders and don't even know where to start. Here is a complete list of all available files and folders with a short description, splitted in different parts.

But before we take a look at the concept folder structure we should understand the core idea. `wp-react-starter` creates a monorepo to quickly develop high-quality WordPress plugins. One of our core ideas is to have multiple plugins in one Git repository. If you are not familiar with the monorepo concept, please read about the advantages and disadvantages of the concept to better understand why this idea is awesome.

## Advantages

From our point of view in WordPress plugin development, a Monorepo is the best solution for the following reasons:

1.  **Simplified organization**: If you maintain several WordPress plugins, you no longer have to change your development context for each plugin. One folder, one structure, one context.
1.  **Synchronized dependencies**: It has always been a problem to synchronize dependencies in multiple projects to ensure that they are technically interoperable or even share [UMDs](https://github.com/umdjs/umd). With [yarn workspaces](https://yarnpkg.com/lang/en/docs/workspaces/) and its [hoisting concepts](https://yarnpkg.com/blog/2018/02/15/nohoist/) this is a piece of cake.
1.  **Cross-project changes**: A similar problem was making changes to your code across projects. For example, changing interfaces (with TypeScript or even harder without) or consistent corporate design elements. The best option was to create an NPM or Composer package as your own project, build it, and publish it. It could then be used as a dependency in your plugins. This much work! With a Monorepo you can simply use packages as an automated symbolic link and get rid of the heavy change-build-publish process.
1.  **Single lint, build, test and release process**: Projects change over time and so do the processes. If you try to keep all your projects synchronized in all repositories, it's a lot of maintenance. And if not, it's even more time-consuming to deal with the different code styles and CI/CD pipelines. With the Monorepo concept, you can share rules and projects across multiple projects and automatically keep them in sync.
1.  **Tests across plugins**: WordPress plugins are often not a stand-alone solution, but have dependencies to various other plugins. For example page builders like [Elementor](https://elementor.com/) or additional add-on plugins. They must be compatible with each other and well tested (with E2E tests). Only a Monorepo allows you this integration to test all plugins integrated with each other always in the most current combinations.

# Impact on the folder structure

A monorepo obviously needs more structures than one project per repository. Building this structure takes a lot of time. But don't worry, we've already done this work for you with the `wp-react-starter` solution. In addition, we explain the complete folder structures.

This explanation includes the structure for the root project and the plugins. For packages we have learned that ts does not make sense to give you a fixed structure and therefore you won't find detailed documentation about a structure of it.

{% page-ref page="root.md" %}

{% page-ref page="plugin.md" %}
