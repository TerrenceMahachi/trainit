#!/bin/bash
#
# Spin up a new project from this master boilerplate.
#
#   ./new-project.sh myshop "My Shop" ["Optional description"]
#
# Creates a sibling directory (../myshop), rewrites config.php, generates a
# fresh _APP_SECRET, resets the database, and sets the permissions php-fpm
# (_www) needs to write SQLite. The new site is then live at
# http://localhost/myshop/
set -euo pipefail

if [ $# -lt 2 ]; then
    echo "Usage: $0 <slug> <\"Display Name\"> [\"Description\"]"
    echo "Example: $0 myshop \"My Shop\" \"Online shop for widgets\""
    exit 1
fi

SLUG="$1"
DISPLAY="$2"
DESCRIPTION="${3:-$DISPLAY}"
MASTER_DIR="$(cd "$(dirname "$0")" && pwd)"
PARENT_DIR="$(dirname "$MASTER_DIR")"
TARGET="$PARENT_DIR/$SLUG"

if [[ ! "$SLUG" =~ ^[a-z0-9][a-z0-9-]*$ ]]; then
    echo "Error: slug must be lowercase letters, digits and dashes (got: $SLUG)"
    exit 1
fi
if [ -e "$TARGET" ]; then
    echo "Error: $TARGET already exists."
    exit 1
fi

# The shared Apache docroot is root-owned, so creating a sibling project
# usually needs sudo. Detect that up front with a clear message.
if [ ! -w "$PARENT_DIR" ] && [ "$(id -u)" -ne 0 ]; then
    echo "Error: $PARENT_DIR is not writable by you."
    echo "Re-run with sudo (files will be handed back to your user):"
    echo "  sudo $0 $*"
    exit 1
fi

echo "Creating $TARGET from master ..."
# Copy everything except junk and this script itself.
rsync -a \
    --exclude '.DS_Store' \
    --exclude 'storage/*' \
    --exclude 'php_errors.log' \
    --exclude 'database/app.db-wal' \
    --exclude 'database/app.db-shm' \
    --exclude 'new-project.sh' \
    "$MASTER_DIR/" "$TARGET/"

cd "$TARGET"

# --- config.php: identity + fresh secret -----------------------------------
SECRET=$(php -r "echo bin2hex(random_bytes(32));")
php <<PHP
<?php
\$slug = '$SLUG';
\$display = '$DISPLAY';
\$description = '$DESCRIPTION';
\$secret = '$SECRET';
\$c = file_get_contents('config.php');
\$c = preg_replace("/define\('_SITENAME', '[^']*'\)/", "define('_SITENAME', '\$slug')", \$c);
\$c = preg_replace("/define\('_SITE', '[^']*'\)/", "define('_SITE', '\$display')", \$c);
\$c = preg_replace("/define\('_SITEDESCRIPTION', '[^']*'\)/", "define('_SITEDESCRIPTION', '\$description')", \$c);
\$c = preg_replace("/define\('_SITEDISPLAYNAME', '[^']*'\)/", "define('_SITEDISPLAYNAME', '\$display')", \$c);
\$c = preg_replace("/define\('_APP_SECRET', '[^']*'\)/", "define('_APP_SECRET', '\$secret')", \$c);
file_put_contents('config.php', \$c);
echo "config.php updated\n";
PHP

# --- fresh database ----------------------------------------------------------
# Start the new project with an empty DB; tables auto-create on first use.
rm -f database/app.db database/app.db-wal database/app.db-shm
touch database/app.db

# --- permissions php-fpm (_www) needs for SQLite -----------------------------
mkdir -p storage
chmod 777 database storage
chmod 666 database/app.db

# When run under sudo, hand ownership back to the invoking user so they can
# edit the project without sudo afterwards.
if [ -n "${SUDO_USER:-}" ]; then
    chown -R "$SUDO_USER" "$TARGET"
fi

echo ""
echo "Done. New project '$SLUG' created."
echo "  URL:    http://localhost/$SLUG/"
echo "  Config: $TARGET/config.php  (secret already regenerated)"
echo ""
echo "Next steps:"
echo "  - Register the first user at http://localhost/$SLUG/register (first admin:"
echo "    update the user's role to 1 via /query-db or the users screen)"
echo "  - Set _ENV to 'production' in config.php before going live"
echo "  - Run: php tests/smoke.php $SLUG   (quick health check)"
