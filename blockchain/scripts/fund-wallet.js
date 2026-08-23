const hre = require("hardhat");

async function main() {
  const [funder] = await hre.ethers.getSigners();
  const target = process.env.TARGET || "0x80354450F4c300F178de2Ab718AbA6D2818CE102";
  const amount = hre.ethers.parseEther("50");

  const before = await hre.ethers.provider.getBalance(target);
  const tx = await funder.sendTransaction({ to: target, value: amount });
  await tx.wait();
  const after = await hre.ethers.provider.getBalance(target);

  console.log("Funded", target);
  console.log("balance before:", hre.ethers.formatEther(before), "ETH");
  console.log("balance after :", hre.ethers.formatEther(after), "ETH");
}

main().catch((e) => { console.error(e); process.exitCode = 1; });
