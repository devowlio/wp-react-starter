import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";
import { YarnLicensesMacroArgs, ComposerLicensesMacroArgs } from "../../../../devops/.gitlab/stage-validate";

const extendConfig: ExtendConfigFunction = async (config) => {
    const { prefix } = createPackageJobs(config, __dirname, "plugins");

    // Validate licenses for yarn packages
    config.from<YarnLicensesMacroArgs>("yarn licenses", { prefix });

    // Validate licenses for composer packages
    config.from<ComposerLicensesMacroArgs>("composer licenses", { prefix });
};

export { extendConfig };
