import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    createPackageJobs(config, __dirname, "plugins");

    await config.include(__dirname, ["./stage-*.ts"]);
};

export { extendConfig };
