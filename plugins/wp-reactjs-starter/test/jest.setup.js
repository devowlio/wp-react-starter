const Enzyme = require("enzyme");
const Adapter = require("enzyme-adapter-react-16");
Enzyme.configure({ adapter: new Adapter() });

process.env.PACKAGE_SCOPE = process.env.npm_package_name.split("/")[0];
