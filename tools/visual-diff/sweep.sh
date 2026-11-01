#!/usr/bin/env bash
# Full sweep: every lofi layout vs the running site. Needs RESET_QUERY (screen 15) and the shopper test account.
set -u
cd "$(dirname "$0")"
# Test orders are on-hold, which reduces stock: top up the two products the captures put in the bag.
(cd ../.. && MSYS_NO_PATHCONV=1 docker compose run --rm wpcli eval 'foreach ([228 => 12, 221 => 5] as $id => $qty) { $p = wc_get_product($id); $p->set_stock_quantity($qty); $p->save(); }' >/dev/null 2>&1)
node run.js spec-foundation.json
node run.js spec-phase2.json
node capture-checkout.js
node capture-account.js
node capture-account-flows.js
node run.js spec-phase4.json
node run.js spec-panels.json
STOCK_PRODUCT=${STOCK_PRODUCT:-228} node capture-states.js
