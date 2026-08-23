require("@nomicfoundation/hardhat-toolbox");

/** Gas profiling for the transaction-cost analysis in Chapter 3 (Section 3.1).
 *  Run with:  GAS_REPORT=true npx hardhat test                     (PowerShell: $env:GAS_REPORT="true"; ...) */
if (process.env.GAS_REPORT) {
  require("hardhat-gas-reporter"); // registered via toolbox; flag enables it
}

/** @type import('hardhat/config').HardhatUserConfig */
module.exports = {
  solidity: {
    version: "0.8.24",
    settings: {
      optimizer: {
        enabled: true,
        runs: 200
      },
      viaIR: true
    }
  },
  gasReporter: {
    enabled: process.env.GAS_REPORT === "true",
    currency: "USD",
    token: "ETH",
    gasPrice: 20
  },
  networks: {
    hardhat: {
      chainId: 31337
    },
    localhost: {
      url: "http://127.0.0.1:8545",
      chainId: 31337
    },
    sepolia: {
      url: `https://eth-sepolia.g.alchemy.com/v2/${process.env.ALCHEMY_API_KEY || "demo"}`,
      accounts: process.env.PRIVATE_KEY ? [process.env.PRIVATE_KEY] : [],
      chainId: 11155111
    }
  },
  paths: {
    sources: "./contracts",
    tests: "./test",
    cache: "./cache",
    artifacts: "./artifacts"
  }
};
