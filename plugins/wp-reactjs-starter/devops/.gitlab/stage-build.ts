import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";
import { EsLintMacroArgs, PhpCsMacroArgs, BuildPluginMacroArgs } from "../../../../devops/.gitlab/stage-build";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "plugins");

    // Generate technical documents
    config.extends([`.${prefix} jobs`, `.${prefix} only changes`, `.docs`], `${prefix} docs`, {});

    // Lint JavaScript/TypeScript coding
    config.from<EsLintMacroArgs>("lint eslint", { prefix });

    // Lint PHP coding
    config.from<PhpCsMacroArgs>("lint phpcs", { prefix });

    // Create build files and run it through docker
    config.from<BuildPluginMacroArgs>("build plugin", {
        prefix
    });
};

export { extendConfig };
