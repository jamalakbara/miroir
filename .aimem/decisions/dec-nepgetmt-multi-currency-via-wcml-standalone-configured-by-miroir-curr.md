---
aimem: 1
id: dec-nepgetmt
type: decision
title: Multi-currency via WCML (standalone), configured by miroir-currency plugin
summary: "WPML Multilingual & Multicurrency for WooCommerce free: IDR base + USD/SGD/MYR, manual ECB rates via wp miroir currency setup|rates"
status: proposed
trust: agent
roles: [all]
tags: [currency, plugins]
paths: [wp-content/plugins/miroir-currency/**]
author: agent:claude-code:claude-opus-5-5
created: 2026-10-30T22:16:24Z
updated: 2026-10-30T22:16:24Z
links: [dec-grer46n5]
---

Quote QUO-2026-003 asks for IDR plus USD and SGD/MYR, adjustable rates, a currency switcher, free plugin preferred. That is 4 currencies.

Chosen: WPML Multilingual & Multicurrency for WooCommerce (slug woocommerce-multilingual, v5.6.3 at install 2026-10-31), running standalone without WPML. Free version: unlimited currencies, manual rates, switcher (product page + [currency_switcher] shortcode + widget), per-currency custom prices, geolocation by country.

Rejected (free versions cap currencies):
- CURCY woo-multi-currency: 2 currencies; settings save truncates the list to 2. Was installed briefly then uninstalled.
- FOX/WOOCS woocommerce-currency-switcher: 2 currencies.
- YayCurrency: unlimited currencies is premium.
- Price Based on Country: switcher is a country picker, currency switcher is Pro.

Config lives in code: wp-content/plugins/miroir-currency.
- `wp miroir currency setup`: enables WCML independent multi-currency (enable_multi_currency=2, mode by_location), adds USD/SGD/MYR (left symbol, 2 decimals, no rounding), product-page dropdown switcher showing codes, fetches rates. Re-runnable.
- `wp miroir currency rates`: refresh rates only.
- Rates come from keyless Frankfurter API (ECB reference). WCML's own auto-rate services (Fixer, CurrencyLayer, ExchangeRatesAPI, OpenExchangeRates) all need an API key; if the client wants scheduled updates, get a free key and configure it in WCML > Multicurrency instead.
- Filter woocommerce_currency_symbol: USD -> US$, SGD -> S$ (WooCommerce shows "$" for both).
- Everyone starts in IDR (default_currencies left at 0); no geolocation switching yet.

Open: header switcher belongs in the miroir theme header (shortcode [currency_switcher] or WCML widget); rounding (e.g. US$21.99 style) is a client choice, WCML supports it per currency.
