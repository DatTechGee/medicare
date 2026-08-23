/**
 * Post-deploy helper: rewrites the frontend contract bundle and the DB
 * static options from deployments.json. Run WITHOUT --network (no chain needed):
 *   node scripts/post-deploy-sync.js
 */
const fs = require("fs");
const path = require("path");

const dep = JSON.parse(fs.readFileSync(path.join(__dirname, "../deployments.json"), "utf8"));
const out = {
  generatedAt: new Date().toISOString(),
  network: "hardhat",
  chainId: 31337,
  chainName: "MediFund Demo Network",
  rpcUrl: "http://127.0.0.1:8545",
  currency: { name: "Ether", symbol: "ETH", decimals: 18 },
  contracts: {},
};

for (const name of ["MediFundCampaign", "MediFundDonation", "MediFundEscrow", "MediFundVerification"]) {
  const artifact = JSON.parse(
    fs.readFileSync(path.join(__dirname, `../artifacts/contracts/${name}.sol/${name}.json`), "utf8")
  );
  out.contracts[name] = { address: dep.contracts[name].toLowerCase(), abi: artifact.abi };
}

fs.writeFileSync(
  "C:/xampp/htdocs/fundorex/@core/public/assets/blockchain/medifund-contracts.json",
  JSON.stringify(out, null, 2)
);
console.log("medifund-contracts.json updated");
for (const [k, v] of Object.entries(dep.contracts)) console.log(`  ${k} = ${v}`);

/* DB options via artisan tinker */
const { execSync } = require("child_process");
const map = [
  ["blockchain_contract_address", dep.contracts.MediFundCampaign],
  ["blockchain_donation_contract_address", dep.contracts.MediFundDonation],
  ["blockchain_escrow_contract_address", dep.contracts.MediFundEscrow],
  ["blockchain_verification_contract_address", dep.contracts.MediFundVerification],
];
const php = "<?php\n" + map.map(([k, v]) =>
  `\\App\\StaticOption::updateOrCreate(['option_name' => '${k}'], ['option_value' => '${v}']);`
).join("\n") + "\necho 'DB options synced\\n';\n";
fs.writeFileSync(process.env.TEMP + "/mf-db-sync.php", php);
execSync(
  `"C:\\xampp\\php\\php.exe" artisan tinker --execute="require getenv('TEMP') . '/mf-db-sync.php';"`,
  { cwd: "C:/xampp/htdocs/fundorex/@core", stdio: "inherit" }
);
