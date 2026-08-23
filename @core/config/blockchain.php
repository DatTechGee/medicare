<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MediFund on-chain configuration
    |--------------------------------------------------------------------------
    | Consumed by blockchain:sync-web3 and by the Web3 frontend bundle.
    | The demo (Hardhat) network runs at http://127.0.0.1:8545 — MetaMask can
    | add it as a custom RPC network for a fully local end-to-end demo.
    */

    'chain_name' => env('MEDIFUND_CHAIN_NAME', 'MediFund Demo Network'),

    'rpc_url' => env('MEDIFUND_RPC_URL', 'http://127.0.0.1:8545'),

];
