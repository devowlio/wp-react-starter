import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";
import { PhpUnitMacroArgs, JestMacroArgs } from "../../../../devops/.gitlab/stage-test";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "packages");

    // Test PHPUnit
    config.from<PhpUnitMacroArgs>("phpunit", { prefix });

    // Test Jest
    config.from<JestMacroArgs>("jest", { prefix });
};

export { extendConfig };
