import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";
import { PhpUnitMacroArgs, JestMacroArgs } from "../../../../devops/.gitlab/stage-test";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "plugins");

    // Start the cypress e2e test
    config.extends(
        [`.${prefix} jobs`, `.${prefix} only changes`, `.docker e2e cypress`],
        `${prefix} docker e2e cypress`,
        {
            // TODO:
            // only: # Add dependent plugins so you run also the test if another plugin changes
            //    changes: ["plugins/{dependent-plugin1,dependent-plugin2}/**/*"]
        }
    );

    // Test PHPUnit
    config.from<PhpUnitMacroArgs>("phpunit", { prefix });

    // Lint PHP coding
    config.from<JestMacroArgs>("jest", { prefix });
};

export { extendConfig };
