#!/bin/bash

# 🎨 DawnBridge Modern Professional Redesign Script
# Automatically updates login.php, checkout.php, user-account.php

echo "🎨 Starting Modern Professional Redesign..."
echo ""

# Navigate to theme directory
cd /Applications/ServBay/www/dawnbridge/wp-content/themes/caremil

# Step 1: Create backups
echo "📦 Creating backups..."
cp login.php login.php.backup.$(date +%Y%m%d_%H%M%S)
cp checkout.php checkout.php.backup.$(date +%Y%m%d_%H%M%S)
cp user-account.php user-account.php.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Backups created"
echo ""

# Step 2: Color replacements
echo "🎨 Updating colors..."
for file in login.php checkout.php user-account.php; do
    # Brand colors → Modern Professional
    sed -i '' 's/brand-navy/primary-900/g' "$file"
    sed -i '' 's/brand-blue/accent-600/g' "$file"
    sed -i '' 's/brand-pink/primary-900/g' "$file"
    sed -i '' 's/brand-gold/accent-600/g' "$file"
    sed -i '' 's/brand-soft/gray-50/g' "$file"
    sed -i '' 's/brand-green/success-600/g' "$file"
    sed -i '' 's/brand-cream/gray-100/g' "$file"
    
    echo "  ✅ Colors updated in $file"
done
echo ""

# Step 3: Font replacements
echo "📝 Updating fonts..."
for file in login.php checkout.php user-account.php; do
    sed -i '' 's/font-display/font-sans/g' "$file"
    sed -i '' 's/Quicksand/Inter/g' "$file"
    sed -i '' 's/Baloo 2/Inter/g' "$file"
    
    echo "  ✅ Fonts updated in $file"
done
echo ""

# Step 4: Border radius simplification
echo "🔲 Simplifying borders..."
for file in login.php checkout.php user-account.php; do
    # Only replace container rounded-3xl, keep buttons as is
    sed -i '' 's/rounded-3xl p-/rounded-xl p-/g' "$file"
    sed -i '' 's/rounded-3xl shadow/rounded-xl shadow/g' "$file"
    
    echo "  ✅ Borders simplified in $file"
done
echo ""

# Step 5: Shadow updates
echo "✨ Updating shadows..."
for file in login.php checkout.php user-account.php; do
    sed -i '' 's/shadow-soft/shadow-sm/g' "$file"
    
    echo "  ✅ Shadows updated in $file"
done
echo ""

# Step 6: Specific CSS color updates
echo "🎨 Updating inline CSS colors..."

# Login.php specific
sed -i '' 's/#1a4f8a/#0f172a/g' login.php
sed -i '' 's/#4cc9f0/#3b82f6/g' login.php
sed -i '' 's/#ef476f/#0f172a/g' login.php
sed -i '' 's/#ffd166/#3b82f6/g' login.php

# Checkout.php specific
sed -i '' 's/#1a4f8a/#0f172a/g' checkout.php
sed -i '' 's/#4cc9f0/#3b82f6/g' checkout.php
sed -i '' 's/#ef476f/#0f172a/g' checkout.php
sed -i '' 's/#ffd166/#3b82f6/g' checkout.php

# User-account.php specific
sed -i '' 's/#1a4f8a/#0f172a/g' user-account.php
sed -i '' 's/#4cc9f0/#3b82f6/g' user-account.php
sed -i '' 's/#ef476f/#0f172a/g' user-account.php
sed -i '' 's/#ffd166/#3b82f6/g' user-account.php

echo "✅ Inline CSS colors updated"
echo ""

# Summary
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 REDESIGN COMPLETE!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Updated files:"
echo "   - login.php"
echo "   - checkout.php"
echo "   - user-account.php"
echo ""
echo "📦 Backups saved with timestamp"
echo ""
echo "⚠️  NEXT STEPS:"
echo "   1. Clear browser cache (Cmd+Shift+R)"
echo "   2. Test login page: /dang-nhap"
echo "   3. Test checkout: /thanh-toan"
echo "   4. Test account: /tai-khoan"
echo "   5. Verify Pancake integration works"
echo ""
echo "🆘 If issues occur:"
echo "   - Check browser console for errors"
echo "   - Restore from backup files"
echo "   - Verify Pancake API connection"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
