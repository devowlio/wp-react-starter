import { ExtendConfigFunction } from "node-gitlab-ci";
import { createPackageJobs } from "../../../../.gitlab-ci";

const extendConfig: ExtendConfigFunction = async (config) => {
    createPackageJobs(config, __dirname, "packages");

    await config.include(__dirname, ["./stage-*.ts"]);
};

export { extendConfig };
