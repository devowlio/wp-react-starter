import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";
import { EsLintMacroArgs, PhpCsMacroArgs } from "../../../../devops/.gitlab/stage-build";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "packages");

    // Lint JavaScript/TypeScript coding
    config.from<EsLintMacroArgs>("lint eslint", { prefix });

    // Lint PHP coding
    config.from<PhpCsMacroArgs>("lint phpcs", { prefix });
};

export { extendConfig };
