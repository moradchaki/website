#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# generate.sh — Daily E-Commerce Page Generator
# Run manually or via cron: 1 0 * * * /path/to/generate.sh
# ─────────────────────────────────────────────────────────────
set -euo pipefail

# ── Config ───────────────────────────────────────────────────
STORE_NAME="My Store"
DEAL_TYPE="Flash Sale"
HERO_HEADING="Today's Hottest Deals"
CTA_LABEL="Shop Now"
SECTION_TITLE="Today's Picks"
ACCENT_COLOR="#e53e3e"
ACCENT_DARK="#c53030"
CURRENCY_SYMBOL="$"
BADGE_TEXT="TODAY ONLY"
CONTACT_INFO="contact@mystore.com"

TEMPLATE_FILE="$(dirname "$0")/template.html"
PRODUCTS_FILE="$(dirname "$0")/products.json"
OUTPUT_DIR="$(dirname "$0")/dist"

# ── Date ─────────────────────────────────────────────────────
DATE=$(date +%Y-%m-%d)
OUTPUT_PATH="$OUTPUT_DIR/$DATE"

echo "──────────────────────────────────"
echo "🛒  Generating e-commerce page"
echo "    Date:  $DATE"
echo "    Store: $STORE_NAME"
echo "    Deal:  $DEAL_TYPE"
echo "──────────────────────────────────"

# ── Create output directory ──────────────────────────────────
mkdir -p "$OUTPUT_PATH"

# ── Check required files ─────────────────────────────────────
if [[ ! -f "$TEMPLATE_FILE" ]]; then
  echo "❌ template.html not found at $TEMPLATE_FILE"
  exit 1
fi

if [[ ! -f "$PRODUCTS_FILE" ]]; then
  echo "⚠️  products.json not found — using inline fallback products"
fi

# ── Variable substitution ─────────────────────────────────────
sed \
  -e "s|{{STORE_NAME}}|$STORE_NAME|g" \
  -e "s|{{DEAL_TYPE}}|$DEAL_TYPE|g" \
  -e "s|{{HERO_HEADING}}|$HERO_HEADING|g" \
  -e "s|{{CTA_LABEL}}|$CTA_LABEL|g" \
  -e "s|{{SECTION_TITLE}}|$SECTION_TITLE|g" \
  -e "s|{{ACCENT_COLOR}}|$ACCENT_COLOR|g" \
  -e "s|{{ACCENT_DARK}}|$ACCENT_DARK|g" \
  -e "s|{{CURRENCY_SYMBOL}}|$CURRENCY_SYMBOL|g" \
  -e "s|{{BADGE_TEXT}}|$BADGE_TEXT|g" \
  -e "s|{{CONTACT_INFO}}|$CONTACT_INFO|g" \
  "$TEMPLATE_FILE" > "$OUTPUT_PATH/index.html"

# ── Copy products.json ─────────────────────────────────────────
if [[ -f "$PRODUCTS_FILE" ]]; then
  cp "$PRODUCTS_FILE" "$OUTPUT_PATH/products.json"
  echo "✅ Copied products.json → $OUTPUT_PATH/products.json"
fi

echo "✅ Generated: $OUTPUT_PATH/index.html"

# ── Optional: deploy via rsync ────────────────────────────────
# Uncomment and configure to auto-deploy after generation:
#
# REMOTE_USER="ubuntu"
# REMOTE_HOST="your-server.com"
# REMOTE_PATH="/var/www/html/shop"
#
# echo "🚀 Deploying to $REMOTE_HOST..."
# rsync -az --delete "$OUTPUT_PATH/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/"
# echo "✅ Deployed to https://your-server.com/shop"

# ── Optional: send notification ───────────────────────────────
# curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
#   -d "chat_id=$TELEGRAM_CHAT_ID&text=✅ Daily store page generated for $DATE"

echo ""
echo "Done! Open $OUTPUT_PATH/index.html in your browser."
